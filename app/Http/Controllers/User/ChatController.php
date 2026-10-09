<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Database;
use App\Models\User;

class ChatController extends Controller
{
    protected $database;
    protected $storage;

    public function __construct()
    {
        // تهيئة Firebase
        $factory = (new Factory)
            ->withServiceAccount(config('firebase.credentials'))
            ->withDatabaseUri(config('firebase.database_url'));
        
        $this->database = $factory->createDatabase();
        $this->storage = $factory->createStorage();
    }

    /**
     * 1. جلب قائمة المحادثات للمستخدم الحالي
     * GET /api/chat/conversations
     */
    public function getConversations(Request $request)
    {
        dd('getConversations');
        try {
            $userId = Auth::id();
            
            // جلب جميع المحادثات من Firebase
            $chatsRef = $this->database->getReference('conversations');
            $allChats = $chatsRef->getValue();
            
            $userConversations = [];
            
            if ($allChats) {
                foreach ($allChats as $chatId => $chat) {
                    // التحقق من أن المستخدم مشارك في هذه المحادثة
                    if (isset($chat['participants']) && in_array((string)$userId, $chat['participants'])) {
                        
                        // جلب معلومات المستخدم الآخر
                        $otherUserId = null;
                        foreach ($chat['participants'] as $participantId) {
                            if ($participantId != $userId) {
                                $otherUserId = $participantId;
                                break;
                            }
                        }
                        
                        $otherUser = User::find($otherUserId);
                        
                        $userConversations[] = [
                            'id' => $chatId,
                            'chatId' => $chatId,
                            'name' => $otherUser ? $otherUser->name : 'Unknown User',
                            'avatar' => $otherUser && $otherUser->avatar ? $otherUser->avatar : '/default-avatar.png',
                            'lastMessage' => $chat['latestMessage']['message'] ?? '',
                            'lastMessageTime' => $chat['latestMessage']['sentDate'] ?? 0,
                            'time' => $this->formatTimestamp($chat['latestMessage']['sentDate'] ?? 0),
                            'unread' => $this->getUnreadCountForChat($chatId, $userId),
                            'isRead' => $chat['latestMessage']['isRead'] ?? true,
                            'otherId' => $otherUserId,
                        ];
                    }
                }
                
                // ترتيب المحادثات حسب آخر رسالة
                usort($userConversations, function($a, $b) {
                    return $b['lastMessageTime'] - $a['lastMessageTime'];
                });
            }
            
            return response()->json([
                'success' => true,
                'conversations' => $userConversations,
                'count' => count($userConversations)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch conversations',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 2. جلب رسائل محادثة معينة
     * GET /api/chat/messages/{chatId}
     */
    public function getMessages(Request $request, $chatId)
    {
        try {
            $userId = Auth::id();
            
            // التحقق من أن المستخدم مشارك في هذه المحادثة
            $chatRef = $this->database->getReference('conversations/' . $chatId);
            $chat = $chatRef->getValue();
            
            if (!$chat || !isset($chat['participants']) || !in_array((string)$userId, $chat['participants'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to this conversation'
                ], 403);
            }
            
            // جلب الرسائل
            $messagesRef = $this->database->getReference('conversations/' . $chatId . '/messages');
            $messages = $messagesRef->getValue();
            
            $formattedMessages = [];
            
            if ($messages) {
                foreach ($messages as $messageId => $message) {
                    $formattedMessages[] = [
                        'id' => $messageId,
                        'messageId' => $messageId,
                        'text' => $message['message'] ?? '',
                        'senderId' => $message['senderId'] ?? '',
                        'from' => $message['senderId'] == $userId ? 'me' : 'them',
                        'time' => $this->formatTimestamp($message['sentDate'] ?? 0),
                        'timestamp' => $message['sentDate'] ?? 0,
                        'isRead' => $message['isRead'] ?? false,
                        'kind' => $message['kind'] ?? 'text', // text, image, audio
                        'displayName' => $message['displayName'] ?? '',
                        'mediaUrl' => $message['mediaUrl'] ?? null,
                    ];
                }
                
                // ترتيب الرسائل حسب الوقت
                usort($formattedMessages, function($a, $b) {
                    return $a['timestamp'] - $b['timestamp'];
                });
            }
            
            return response()->json([
                'success' => true,
                'messages' => $formattedMessages,
                'chatId' => $chatId
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch messages',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 3. إرسال رسالة نصية
     * POST /api/chat/send-message
     * Body: { chatId, message, receiverId }
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'chatId' => 'nullable|string',
            'receiverId' => 'required_without:chatId|integer'
        ]);
        
        try {
            $userId = Auth::id();
            $user = Auth::user();
            $message = $request->input('message');
            $chatId = $request->input('chatId');
            $receiverId = $request->input('receiverId');
            
            // إذا لم يكن هناك chatId، إنشاء محادثة جديدة
            if (!$chatId) {
                $chatId = $this->getOrCreateChat($userId, $receiverId);
            }
            
            // إنشاء رسالة جديدة
            $messageId = $this->database->getReference('conversations/' . $chatId . '/messages')->push()->getKey();
            
            $messageData = [
                'messageId' => $messageId,
                'senderId' => (string)$userId,
                'message' => $message,
                'kind' => 'text',
                'sentDate' => time(),
                'isRead' => false,
                'displayName' => $user->name ?? 'User',
            ];
            
            // حفظ الرسالة
            $this->database->getReference('conversations/' . $chatId . '/messages/' . $messageId)->set($messageData);
            
            // تحديث آخر رسالة في المحادثة
            $this->database->getReference('conversations/' . $chatId . '/latestMessage')->set($messageData);
            
            return response()->json([
                'success' => true,
                'message' => 'Message sent successfully',
                'data' => [
                    'chatId' => $chatId,
                    'messageId' => $messageId,
                    'messageData' => $messageData
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send message',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 4. رفع وإرسال صورة
     * POST /api/chat/send-image
     * Body: { chatId, image (file), receiverId }
     */
    public function sendImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:10240', // 10MB max
            'chatId' => 'nullable|string',
            'receiverId' => 'required_without:chatId|integer'
        ]);
        
        try {
            $userId = Auth::id();
            $user = Auth::user();
            $chatId = $request->input('chatId');
            $receiverId = $request->input('receiverId');
            
            // إذا لم يكن هناك chatId، إنشاء محادثة جديدة
            if (!$chatId) {
                $chatId = $this->getOrCreateChat($userId, $receiverId);
            }
            
            // رفع الصورة
            $image = $request->file('image');
            $imageName = 'chat_images/' . time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('public', $imageName);
            $imageUrl = Storage::url($imageName);
            
            // إنشاء رسالة جديدة
            $messageId = $this->database->getReference('conversations/' . $chatId . '/messages')->push()->getKey();
            
            $messageData = [
                'messageId' => $messageId,
                'senderId' => (string)$userId,
                'message' => '📷 Image',
                'kind' => 'image',
                'mediaUrl' => url($imageUrl),
                'sentDate' => time(),
                'isRead' => false,
                'displayName' => $user->name ?? 'User',
            ];
            
            // حفظ الرسالة
            $this->database->getReference('conversations/' . $chatId . '/messages/' . $messageId)->set($messageData);
            
            // تحديث آخر رسالة
            $this->database->getReference('conversations/' . $chatId . '/latestMessage')->set($messageData);
            
            return response()->json([
                'success' => true,
                'message' => 'Image sent successfully',
                'data' => [
                    'chatId' => $chatId,
                    'messageId' => $messageId,
                    'imageUrl' => url($imageUrl),
                    'messageData' => $messageData
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send image',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 5. رفع وإرسال تسجيل صوتي
     * POST /api/chat/send-audio
     * Body: { chatId, audio (file), receiverId }
     */
    public function sendAudio(Request $request)
    {
        $request->validate([
            'audio' => 'required|mimes:mp3,wav,m4a,ogg|max:20480', // 20MB max
            'chatId' => 'nullable|string',
            'receiverId' => 'required_without:chatId|integer'
        ]);
        
        try {
            $userId = Auth::id();
            $user = Auth::user();
            $chatId = $request->input('chatId');
            $receiverId = $request->input('receiverId');
            
            // إذا لم يكن هناك chatId، إنشاء محادثة جديدة
            if (!$chatId) {
                $chatId = $this->getOrCreateChat($userId, $receiverId);
            }
            
            // رفع التسجيل الصوتي
            $audio = $request->file('audio');
            $audioName = 'chat_audio/' . time() . '_' . Str::random(10) . '.' . $audio->getClientOriginalExtension();
            $audioPath = $audio->storeAs('public', $audioName);
            $audioUrl = Storage::url($audioName);
            
            // إنشاء رسالة جديدة
            $messageId = $this->database->getReference('conversations/' . $chatId . '/messages')->push()->getKey();
            
            $messageData = [
                'messageId' => $messageId,
                'senderId' => (string)$userId,
                'message' => '🎤 Voice message',
                'kind' => 'audio',
                'mediaUrl' => url($audioUrl),
                'sentDate' => time(),
                'isRead' => false,
                'displayName' => $user->name ?? 'User',
            ];
            
            // حفظ الرسالة
            $this->database->getReference('conversations/' . $chatId . '/messages/' . $messageId)->set($messageData);
            
            // تحديث آخر رسالة
            $this->database->getReference('conversations/' . $chatId . '/latestMessage')->set($messageData);
            
            return response()->json([
                'success' => true,
                'message' => 'Audio sent successfully',
                'data' => [
                    'chatId' => $chatId,
                    'messageId' => $messageId,
                    'audioUrl' => url($audioUrl),
                    'messageData' => $messageData
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send audio',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 6. تحديث حالة القراءة
     * POST /api/chat/mark-read/{chatId}
     */
    public function markAsRead(Request $request, $chatId)
    {
        try {
            $userId = Auth::id();
            
            // جلب جميع الرسائل في المحادثة
            $messagesRef = $this->database->getReference('conversations/' . $chatId . '/messages');
            $messages = $messagesRef->getValue();
            
            if ($messages) {
                foreach ($messages as $messageId => $message) {
                    // تحديث فقط الرسائل التي ليست من المستخدم الحالي
                    if ($message['senderId'] != $userId && !$message['isRead']) {
                        $this->database->getReference('conversations/' . $chatId . '/messages/' . $messageId . '/isRead')->set(true);
                    }
                }
            }
            
            // تحديث latestMessage إذا كانت غير مقروءة
            $latestMessageRef = $this->database->getReference('conversations/' . $chatId . '/latestMessage');
            $latestMessage = $latestMessageRef->getValue();
            
            if ($latestMessage && $latestMessage['senderId'] != $userId) {
                $this->database->getReference('conversations/' . $chatId . '/latestMessage/isRead')->set(true);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Messages marked as read'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark messages as read',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 7. إنشاء محادثة جديدة
     * POST /api/chat/create-conversation
     * Body: { receiverId }
     */
    public function createConversation(Request $request)
    {
        $request->validate([
            'receiverId' => 'required|integer|exists:users,id'
        ]);
        
        try {
            $userId = Auth::id();
            $receiverId = $request->input('receiverId');
            
            // التحقق من عدم إنشاء محادثة مع نفس المستخدم
            if ($userId == $receiverId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot create conversation with yourself'
                ], 400);
            }
            
            $chatId = $this->getOrCreateChat($userId, $receiverId);
            
            return response()->json([
                'success' => true,
                'message' => 'Conversation created successfully',
                'chatId' => $chatId
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create conversation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 8. حذف رسالة
     * DELETE /api/chat/delete-message/{messageId}
     * Body: { chatId }
     */
    public function deleteMessage(Request $request, $messageId)
    {
        $request->validate([
            'chatId' => 'required|string'
        ]);
        
        try {
            $userId = Auth::id();
            $chatId = $request->input('chatId');
            
            // التحقق من أن الرسالة تخص المستخدم الحالي
            $messageRef = $this->database->getReference('conversations/' . $chatId . '/messages/' . $messageId);
            $message = $messageRef->getValue();
            
            if (!$message || $message['senderId'] != $userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to delete this message'
                ], 403);
            }
            
            // حذف الرسالة
            $messageRef->remove();
            
            return response()->json([
                'success' => true,
                'message' => 'Message deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete message',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 9. البحث في المحادثات
     * GET /api/chat/search?query=...
     */
    public function searchConversations(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:1'
        ]);
        
        try {
            $userId = Auth::id();
            $query = strtolower($request->input('query'));
            
            // جلب جميع المحادثات
            $conversations = $this->getConversations($request)->getData()->conversations;
            
            // البحث في الأسماء والرسائل
            $results = array_filter($conversations, function($conv) use ($query) {
                return stripos($conv->name, $query) !== false || 
                       stripos($conv->lastMessage, $query) !== false;
            });
            
            return response()->json([
                'success' => true,
                'results' => array_values($results),
                'count' => count($results)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Search failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 10. جلب معلومات المستخدم للشات
     * GET /api/chat/user-info/{userId}
     */
    public function getUserInfo($userId)
    {
        try {
            $user = User::find($userId);
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->avatar ?? '/default-avatar.png',
                    'phone' => $user->phone ?? null,
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch user info',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 11. جلب عدد الرسائل غير المقروءة
     * GET /api/chat/unread-count
     */
    public function getUnreadCount(Request $request)
    {
        try {
            $userId = Auth::id();
            
            $chatsRef = $this->database->getReference('conversations');
            $allChats = $chatsRef->getValue();
            
            $totalUnread = 0;
            
            if ($allChats) {
                foreach ($allChats as $chatId => $chat) {
                    if (isset($chat['participants']) && in_array((string)$userId, $chat['participants'])) {
                        $totalUnread += $this->getUnreadCountForChat($chatId, $userId);
                    }
                }
            }
            
            return response()->json([
                'success' => true,
                'unreadCount' => $totalUnread
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch unread count',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ========== Helper Functions ==========

    /**
     * الحصول على chatId موجود أو إنشاء محادثة جديدة
     */
    private function getOrCreateChat($userId1, $userId2)
    {
        // البحث عن محادثة موجودة
        $chatsRef = $this->database->getReference('conversations');
        $allChats = $chatsRef->getValue();
        
        if ($allChats) {
            foreach ($allChats as $chatId => $chat) {
                if (isset($chat['participants']) && 
                    in_array((string)$userId1, $chat['participants']) && 
                    in_array((string)$userId2, $chat['participants'])) {
                    return $chatId;
                }
            }
        }
        
        // إنشاء محادثة جديدة
        $newChatRef = $chatsRef->push();
        $chatId = $newChatRef->getKey();
        
        $user1 = User::find($userId1);
        $user2 = User::find($userId2);
        
        $chatData = [
            'id' => $chatId,
            'participants' => [(string)$userId1, (string)$userId2],
            'sendId' => (string)$userId1,
            'otherId' => (string)$userId2,
            'name' => $user2->name ?? 'User',
            'otherUserName' => $user2->name ?? 'User',
            'otherUserPhoto' => $user2->avatar ?? '',
            'photo' => $user1->avatar ?? '',
            'latestMessage' => [
                'message' => '',
                'sentDate' => time(),
                'senderId' => (string)$userId1,
                'isRead' => false,
                'kind' => 'text',
                'displayName' => $user1->name ?? 'User'
            ],
            'messages' => []
        ];
        
        $newChatRef->set($chatData);
        
        return $chatId;
    }

    /**
     * حساب عدد الرسائل غير المقروءة في محادثة معينة
     */
    private function getUnreadCountForChat($chatId, $userId)
    {
        $messagesRef = $this->database->getReference('conversations/' . $chatId . '/messages');
        $messages = $messagesRef->getValue();
        
        $unreadCount = 0;
        
        if ($messages) {
            foreach ($messages as $message) {
                if ($message['senderId'] != $userId && !$message['isRead']) {
                    $unreadCount++;
                }
            }
        }
        
        return $unreadCount;
    }

    /**
     * تنسيق الوقت
     */
    private function formatTimestamp($timestamp)
    {
        if (!$timestamp) return 'Now';
        
        $diff = time() - $timestamp;
        
        if ($diff < 60) return 'Now';
        if ($diff < 3600) return floor($diff / 60) . ' min ago';
        if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
        if ($diff < 604800) return floor($diff / 86400) . ' days ago';
        
        return date('M d, Y', $timestamp);
    }
}
