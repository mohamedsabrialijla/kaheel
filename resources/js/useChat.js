// ============================================
// Vue Composable for Chat with Firebase
// ============================================
// احفظ هذا الملف في: src/composables/useChat.js

import { ref, computed, onMounted, onUnmounted } from 'vue';
import { initializeApp } from 'firebase/app';
import { getDatabase, ref as dbRef, onValue, push, set, update, remove, query, orderByChild } from 'firebase/database';
import { getStorage, ref as storageRef, uploadBytes, getDownloadURL } from 'firebase/storage';
import { authApi } from '@/axiosUser'; // استخدم axios instance الموجود

// Firebase Configuration
const firebaseConfig = {
  apiKey: import.meta.env.VITE_FIREBASE_API_KEY || "AIzaSyAb1sUcV73XaUtr6Mx_OtnU9TZXzSutI24",
  authDomain: import.meta.env.VITE_FIREBASE_AUTH_DOMAIN || "brandy-bb646.firebaseapp.com",
  databaseURL: import.meta.env.VITE_FIREBASE_DATABASE_URL || "https://brandy-bb646-default-rtdb.firebaseio.com",
  projectId: import.meta.env.VITE_FIREBASE_PROJECT_ID || "brandy-bb646",
  storageBucket: import.meta.env.VITE_FIREBASE_STORAGE_BUCKET || "brandy-bb646.firebasestorage.app",
  messagingSenderId: import.meta.env.VITE_FIREBASE_MESSAGING_SENDER_ID || "122215339644",
  appId: import.meta.env.VITE_FIREBASE_APP_ID || "1:122215339644:web:eba73821848baa759e9bf9"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
const database = getDatabase(app);
const storage = getStorage(app);

/**
 * @param {Object} [websiteStore] - على الموقع مرّر store (مثلاً useStore()) لتفعيل جلب المحادثات بعد تحميل المستخدم
 */
export function useChat(websiteStore = null) {
  // State
  const conversations = ref([]);
  const currentChat = ref(null);
  const messages = ref([]);
  const unreadCount = ref(0);
  const isLoading = ref(false);
  const currentUser = ref(null);
  
  // Listeners
  let conversationsListener = null;
  let messagesListener = null;

  /**
   * إنشاء ID مركب للتمييز بين Admin و User
   * @param {Object} user - بيانات المستخدم
   * @returns {string} - ID مركب (مثلاً: "admin_5" أو "user_5")
   */
  const getCompositeUserId = (user) => {
    if (!user || !user.id) return null;
    const userType = user.userType || 'user'; // افتراضي 'user' إذا لم يكن محدد
    return `${userType}_${user.id}`;
  };

  /**
   * استخراج ID الأصلي من ID المركب
   * @param {string} compositeId - ID مركب (مثلاً: "admin_5")
   * @returns {string} - ID الأصلي (مثلاً: "5")
   */
  const extractOriginalId = (compositeId) => {
    if (!compositeId) return null;
    const parts = String(compositeId).split('_');
    return parts.length > 1 ? parts.slice(1).join('_') : compositeId;
  };

  /**
   * تهيئة المستخدم الحالي
   */ 
  const initCurrentUser = () => {
    console.log('🔍 initCurrentUser called');
    const raw = localStorage.getItem('user');
    console.log('🔍 localStorage.user raw value:', raw);
    
    if (!raw || raw === 'null' || raw === 'undefined') {
      console.warn('⚠️ localStorage.user is empty or null');
      // محاولة جلب المستخدم من Vuex store (إذا كان متاحاً)
      try {
        // محاولة الوصول إلى Vuex store من window أو من Vue app
        const vueApp = document.querySelector('#app').__vue_app__;
        if (vueApp && vueApp.config && vueApp.config.globalProperties && vueApp.config.globalProperties.$store) {
          const store = vueApp.config.globalProperties.$store;
          const storeUser = store.getters.getUser;
          console.log('🔍 Found user in Vuex store:', storeUser);
          if (storeUser && storeUser.id) {
            // التأكد من وجود userType
            if (!storeUser.userType) {
              // محاولة تحديد userType من السياق (Dashboard = admin, Website = user)
              const isDashboard = window.location.pathname.includes('/dashboard');
              storeUser.userType = isDashboard ? 'admin' : 'user';
            }
            currentUser.value = storeUser;
            localStorage.setItem('user', JSON.stringify(storeUser));
            console.log('✅ User set from Vuex store');
            return;
          }
        }
      } catch (e) {
        console.warn('⚠️ Could not access Vuex store:', e);
      }
      console.warn('⚠️ No user found in localStorage or Vuex store');
      return;
    }
    
    try {
      // حاول قراءة JSON طبيعي
      const parsedUser = JSON.parse(raw);
      // التأكد من وجود userType
      if (!parsedUser.userType) {
        // محاولة تحديد userType من السياق
        const isDashboard = window.location.pathname.includes('/dashboard');
        parsedUser.userType = isDashboard ? 'admin' : 'user';
        localStorage.setItem('user', JSON.stringify(parsedUser));
      }
      currentUser.value = parsedUser;
      console.log('✅ User parsed from localStorage:', currentUser.value);
      console.log('✅ User ID:', currentUser.value?.id);
      console.log('✅ User Type:', currentUser.value?.userType);
      console.log('✅ Composite User ID:', getCompositeUserId(currentUser.value));
    } catch (e) {
      // في حال كانت القيمة تم تعيينها ككائن مباشرة ("[object Object]") أو نص غير صالح
      console.warn('❌ Invalid localStorage.user value. Error:', e);
      console.warn('❌ Raw value was:', raw);
      localStorage.removeItem('user');
      currentUser.value = null;
    }
  };

  /**
   * جلب قائمة المحادثات من Firebase
   */
  const fetchConversations = () => {
    
    console.log('=== fetchConversations START ===');
    
    if (!currentUser.value) {
      console.log('currentUser.value is null, initializing...');
      initCurrentUser();
    }

    console.log('currentUser.value:', currentUser.value);
    // استخدام ID مركب للتمييز بين Admin و User
    const userId = getCompositeUserId(currentUser.value);
    console.log('userId (composite):', userId, 'type:', typeof userId);
    
    if (!userId) {
      console.warn('❌ No userId found! currentUser.value:', currentUser.value);
      return;
    }

    const conversationsRef = dbRef(database, 'conversations');
    console.log('Fetching conversations from path: conversations/');
    
    conversationsListener = onValue(conversationsRef, (snapshot) => {
      const data = snapshot.val();
      console.log('📥 Received data from Firebase:', data);
      const convList = [];

      if (data) {
        console.log('Total conversations in Firebase:', Object.keys(data).length);
        
        Object.keys(data).forEach((chatId) => {
          const chat = data[chatId];
          console.log(`Checking chat ${chatId}:`, {
            participants: chat.participants,
            userId: userId,
            includes: chat.participants?.includes(userId),
            participantTypes: chat.participants?.map(p => ({ value: p, type: typeof p }))
          });
          
          // التحقق من أن المستخدم مشارك في المحادثة (يدعم كل من "user_123" و 123 في Firebase)
          const participantsAsStrings = chat.participants?.map(p => String(p)) || [];
          const userIdStr = String(userId);
          const userIdNum = extractOriginalId(userId);
          const isParticipant = participantsAsStrings.includes(userIdStr) ||
            (userIdNum && participantsAsStrings.includes(userIdNum));
          
          if (isParticipant) {
            console.log(`✅ User ${userId} is participant in chat ${chatId}`);
            
            // جلب معلومات المستخدم الآخر (مقارنة بنص الـ id والرقم)
            const isCurrentUser = (id) => String(id) === userIdStr || String(id) === userIdNum;
            const otherUserId = chat.participants.find(id => !isCurrentUser(id));
            
            convList.push({
              id: chatId,
              chatId: chatId,
              name: chat.otherUserName || chat.name || 'Unknown User',
              avatar: chat.otherUserPhoto || chat.photo || '/default-avatar.png',
              text: chat.latestMessage?.message || '',
              lastMessage: chat.latestMessage?.message || '',
              time: formatTimestamp(chat.latestMessage?.sentDate || 0),
              timestamp: chat.latestMessage?.sentDate || 0,
              unread: getUnreadCountForChat(chat, userId),
              isRead: chat.latestMessage?.isRead || false,
              otherId: otherUserId,
            });
          } else {
            console.log(`❌ User ${userId} is NOT participant in chat ${chatId}`);
          }
        });

        // ترتيب المحادثات حسب آخر رسالة
        convList.sort((a, b) => b.timestamp - a.timestamp);
        console.log(`✅ Found ${convList.length} conversations for user ${userId}`);
      } else {
        console.warn('⚠️ No data in Firebase conversations');
      }

      conversations.value = convList;
      console.log('Final conversations list:', conversations.value);
      calculateUnreadCount();
    });
  };

  /**
   * فتح محادثة معينة
   */
  const openChat = (conversation) => {
    currentChat.value = conversation;
    fetchMessages(conversation.chatId);
    markAsRead(conversation.chatId);
  };

  /**
   * جلب رسائل محادثة معينة
   */
  const fetchMessages = (chatId) => {
    if (messagesListener) {
      messagesListener();
    }

    const messagesRef = dbRef(database, `conversations/${chatId}/messages`);
    
    messagesListener = onValue(messagesRef, (snapshot) => {
      const data = snapshot.val();
      const msgList = [];

      if (data) {
        Object.keys(data).forEach((messageId) => {
          const msg = data[messageId];
          
          // Debug: طباعة البيانات
          console.log('📥 Message from Firebase:', {
            messageId,
            kind: msg.kind,
            mediaUrl: msg.mediaUrl,
            message: msg.message,
            sentDate: msg.sentDate,
            fullMessage: msg
          });
          
          // التأكد من أن kind و mediaUrl موجودين
          const messageKind = msg.kind || 'text';
          const messageMediaUrl = msg.mediaUrl || null;
          
          // إذا كان kind هو image أو audio لكن mediaUrl غير موجود، نعرض رسالة خطأ
          if ((messageKind === 'image' || messageKind === 'audio') && !messageMediaUrl) {
            console.warn('⚠️ Message has kind', messageKind, 'but no mediaUrl:', msg);
          }
          
          const composite = getCompositeUserId(currentUser.value);
          const senderStr = String(msg.senderId || '');
          const compositeStr = String(composite || '');
          const senderNum = extractOriginalId(senderStr) || senderStr;
          const myNum = extractOriginalId(compositeStr) || compositeStr;
          const isFromMe = senderStr === compositeStr || senderNum === myNum;
          msgList.push({
            id: messageId,
            messageId: messageId,
            text: msg.message || '',
            senderId: msg.senderId,
            from: isFromMe ? 'me' : 'them',
            time: formatTimestamp(msg.sentDate || 0),
            timestamp: msg.sentDate || Date.now(), // إذا لم يكن موجود، استخدم الوقت الحالي
            isRead: msg.isRead || false,
            kind: messageKind, // text, image, audio
            displayName: msg.displayName || '',
            mediaUrl: messageMediaUrl,
          });
          
          // Debug: طباعة الرسالة بعد المعالجة
          if (messageKind === 'image' || messageKind === 'audio') {
            console.log('✅ Media message processed:', {
              kind: messageKind,
              mediaUrl: messageMediaUrl,
              hasMediaUrl: !!messageMediaUrl
            });
          }
        });

        // ترتيب الرسائل حسب الوقت (من الأقدم للأحدث)
        msgList.sort((a, b) => {
          // التأكد من أن timestamp رقم صحيح
          const timeA = Number(a.timestamp) || 0;
          const timeB = Number(b.timestamp) || 0;
          return timeA - timeB; // ترتيب تصاعدي (الأقدم أولاً)
        });
        
        console.log('📋 Sorted messages:', msgList.map(m => ({
          id: m.id,
          timestamp: m.timestamp,
          time: m.time,
          kind: m.kind
        })));
      }

      messages.value = msgList;
      
      // التمرير للأسفل بعد تحديث الرسائل
      setTimeout(() => {
        const chatBox = document.querySelector('.chat-messages');
        if (chatBox) {
          chatBox.scrollTop = chatBox.scrollHeight;
        }
      }, 100);
    });
  };

  /**
   * Push للمستلم عبر الباك اند (لا يوقف الإرسال إذا فشل)
   */
  const notifyReceiver = (chatId, kind, text = '') => {
    try {
      if ((currentUser.value?.userType || 'user') !== 'user') return;

      const conv = conversations.value.find((c) => c.chatId === chatId)
        || (currentChat.value && (currentChat.value.chatId === chatId || currentChat.value.id === chatId) ? currentChat.value : null);
      const receiverId = conv?.otherId;
      if (!receiverId || String(receiverId).startsWith('admin_')) return;

      authApi({
        method: 'post',
        url: 'chat/notifyNewMessage',
        data: { receiver_id: String(receiverId), chat_id: chatId, kind, message: text },
      }).catch(() => {});
    } catch (e) {
      // ignore
    }
  };

  /**
   * إرسال رسالة نصية
   */
  const sendMessage = async (chatId, messageText) => {
    console.log('=== useChat.sendMessage START ===');
    console.log('chatId:', chatId);
    console.log('messageText:', messageText);
    console.log('currentUser.value:', currentUser.value);
    
    if (!messageText || !messageText.trim()) {
      console.warn('❌ Message text is empty');
      return false;
    }
    
    if (!currentUser.value) {
      console.warn('❌ currentUser.value is null');
      return false;
    }

    try {
      const messagesRef = dbRef(database, `conversations/${chatId}/messages`);
      console.log('📝 Creating message reference:', `conversations/${chatId}/messages`);
      
      const newMessageRef = push(messagesRef);
      console.log('✅ Created message ref with key:', newMessageRef.key);

      const messageData = {
        messageId: newMessageRef.key,
        senderId: getCompositeUserId(currentUser.value),
        message: messageText,
        kind: 'text',
        sentDate: Date.now(),
        isRead: false,
        displayName: currentUser.value.name || 'User',
      };

      console.log('📤 Saving message data:', messageData);
      await set(newMessageRef, messageData);
      console.log('✅ Message saved to Firebase');

      // تحديث آخر رسالة
      const latestMessageRef = dbRef(database, `conversations/${chatId}/latestMessage`);
      console.log('📤 Updating latestMessage...');
      await set(latestMessageRef, messageData);
      console.log('✅ latestMessage updated');

      notifyReceiver(chatId, 'text', messageText);

      console.log('=== useChat.sendMessage SUCCESS ===');
      return true;
    } catch (error) {
      console.error('❌ Error sending message:', error);
      console.error('Error details:', {
        message: error.message,
        stack: error.stack,
        code: error.code
      });
      return false;
    }
  };

  /**
   * إرسال صورة
   */
  const sendImage = async (chatId, imageFile) => {
    if (!imageFile || !currentUser.value) return;

    try {
      isLoading.value = true;

      // رفع الصورة إلى Firebase Storage
      const timestamp = Date.now();
      const fileName = `chat_images/${chatId}/${timestamp}_${imageFile.name}`;
      const imageRef = storageRef(storage, fileName);
      
      await uploadBytes(imageRef, imageFile);
      const imageUrl = await getDownloadURL(imageRef);

      // إنشاء رسالة
      const messagesRef = dbRef(database, `conversations/${chatId}/messages`);
      const newMessageRef = push(messagesRef);

      const messageData = {
        messageId: newMessageRef.key,
        senderId: getCompositeUserId(currentUser.value),
        message: '📷 Image',
        kind: 'image',
        mediaUrl: imageUrl,
        sentDate: Date.now(),
        isRead: false,
        displayName: currentUser.value.name || 'User',
      };
      
      console.log('📤 Image message data:', messageData);

      await set(newMessageRef, messageData);

      // تحديث آخر رسالة
      const latestMessageRef = dbRef(database, `conversations/${chatId}/latestMessage`);
      await set(latestMessageRef, messageData);

      notifyReceiver(chatId, 'image');

      isLoading.value = false;
      return true;
    } catch (error) {
      console.error('Error sending image:', error);
      isLoading.value = false;
      return false;
    }
  };

  /**
   * إرسال تسجيل صوتي
   */
  const sendAudio = async (chatId, audioFile) => {
    if (!audioFile || !currentUser.value) return;

    try {
      isLoading.value = true;

      // رفع التسجيل إلى Firebase Storage
      const timestamp = Date.now();
      const fileName = `chat_audio/${chatId}/${timestamp}_${audioFile.name}`;
      const audioRef = storageRef(storage, fileName);
      
      await uploadBytes(audioRef, audioFile);
      const audioUrl = await getDownloadURL(audioRef);

      // إنشاء رسالة
      const messagesRef = dbRef(database, `conversations/${chatId}/messages`);
      const newMessageRef = push(messagesRef);

      const messageData = {
        messageId: newMessageRef.key,
        senderId: getCompositeUserId(currentUser.value),
        message: '🎤 Voice message',
        kind: 'audio',
        mediaUrl: audioUrl,
        sentDate: Date.now(),
        isRead: false,
        displayName: currentUser.value.name || 'User',
      };
      
      console.log('📤 Audio message data:', messageData);
      console.log('📤 Audio message data - kind:', messageData.kind);
      console.log('📤 Audio message data - mediaUrl:', messageData.mediaUrl);
      console.log('📤 Saving to path:', `conversations/${chatId}/messages/${newMessageRef.key}`);

      await set(newMessageRef, messageData);
      console.log('✅ Audio message saved to Firebase');

      // تحديث آخر رسالة
      const latestMessageRef = dbRef(database, `conversations/${chatId}/latestMessage`);
      await set(latestMessageRef, messageData);

      notifyReceiver(chatId, 'audio');

      isLoading.value = false;
      return true;
    } catch (error) {
      console.error('Error sending audio:', error);
      isLoading.value = false;
      return false;
    }
  };

  /**
   * تحديث حالة القراءة
   */
  const markAsRead = async (chatId) => {
    if (!currentUser.value) return;

    try {
      const messagesRef = dbRef(database, `conversations/${chatId}/messages`);
      
      onValue(messagesRef, (snapshot) => {
        const data = snapshot.val();
        if (data) {
          Object.keys(data).forEach(async (messageId) => {
            const msg = data[messageId];
            // تحديث فقط الرسائل التي ليست من المستخدم الحالي
            if (msg.senderId !== getCompositeUserId(currentUser.value) && !msg.isRead) {
              const messageRef = dbRef(database, `conversations/${chatId}/messages/${messageId}/isRead`);
              await set(messageRef, true);
            }
          });
        }
      }, { onlyOnce: true });

      // تحديث latestMessage
      const latestMessageRef = dbRef(database, `conversations/${chatId}/latestMessage/isRead`);
      await set(latestMessageRef, true);

    } catch (error) {
      console.error('Error marking as read:', error);
    }
  };

  /**
   * إنشاء محادثة جديدة
   */
  const createConversation = async (receiverId, receiverName, receiverAvatar) => {
    console.log('createConversation called with:', { receiverId, receiverName, receiverAvatar });
    
    if (!currentUser.value) {
      console.error('createConversation: currentUser.value is null');
      return null;
    }

    console.log('createConversation: currentUser.value:', currentUser.value);

    try {
      // البحث عن محادثة موجودة
      const existingChat = conversations.value.find(conv => 
        conv.otherId === receiverId.toString()
      );

      if (existingChat) {
        console.log('createConversation: Found existing chat:', existingChat.chatId);
        return existingChat.chatId;
      }

      // إنشاء محادثة جديدة
      const conversationsRef = dbRef(database, 'conversations');
      console.log('createConversation: Creating in path: conversations/', conversationsRef);
      console.log('createConversation: database:', database);
      
      const newChatRef = push(conversationsRef);
      const chatId = newChatRef.key;
      console.log('createConversation: Created chat with ID:', chatId);

      // استخدام ID مركب للمستخدم الحالي
      const currentUserId = getCompositeUserId(currentUser.value);
      
      // تحديد ID المستقبل: إذا كان يحتوي على _ فهو ID مركب، وإلا نضيف prefix user_
      let receiverCompositeId = receiverId.toString();
      if (!receiverCompositeId.includes('_')) {
        receiverCompositeId = `user_${receiverCompositeId}`;
      }
      
      const chatData = {
        id: chatId,
        participants: [currentUserId, receiverCompositeId],
        sendId: currentUserId,
        otherId: receiverCompositeId,
        name: receiverName,
        otherUserName: receiverName,
        otherUserPhoto: receiverAvatar || '',
        photo: currentUser.value.avatar || '',
        latestMessage: {
          message: '',
          sentDate: Date.now(),
          senderId: currentUserId,
          isRead: false,
          kind: 'text',
          displayName: currentUser.value.name || 'User'
        },
        messages: {}
      };

      console.log('createConversation: Chat data to save:', chatData);
      console.log('createConversation: Attempting to save to:', `conversations/${chatId}`);
      
      await set(newChatRef, chatData);
      
      console.log('createConversation: Successfully saved! Chat ID:', chatId);
      return chatId;
    } catch (error) {
      console.error('createConversation: Error details:', error);
      console.error('createConversation: Error message:', error.message);
      console.error('createConversation: Error stack:', error.stack);
      return null;
    }
  };

  /**
   * حذف رسالة
   */
  const deleteMessage = async (chatId, messageId) => {
    try {
      const messageRef = dbRef(database, `conversations/${chatId}/messages/${messageId}`);
      await remove(messageRef);
      return true;
    } catch (error) {
      console.error('Error deleting message:', error);
      return false;
    }
  };

  /**
   * البحث في المحادثات (فقط في الأسماء)
   */
  const searchConversations = (query) => {
    if (!query || !query.trim()) return conversations.value;

    const lowerQuery = query.toLowerCase().trim();
    return conversations.value.filter(conv => 
      conv.name && conv.name.toLowerCase().includes(lowerQuery)
    );
  };

  /**
   * حساب عدد الرسائل غير المقروءة لمحادثة معينة
   */
  const getUnreadCountForChat = (chat, userId) => {
    let count = 0;
    
    // إذا كانت هناك رسائل مباشرة في chat.messages
    if (chat.messages) {
      Object.values(chat.messages).forEach(msg => {
        if (msg.senderId !== userId && !msg.isRead) {
          count++;
        }
      });
    }
    
    // إذا لم تكن هناك رسائل مباشرة، نستخدم latestMessage
    // إذا كانت latestMessage غير مقروءة وليست من المستخدم الحالي، نضيف 1
    if (count === 0 && chat.latestMessage) {
      const latestMsg = chat.latestMessage;
      if (latestMsg.senderId !== userId && !latestMsg.isRead) {
        count = 1; // على الأقل رسالة واحدة غير مقروءة
      }
    }
    
    return count;
  };

  /**
   * حساب إجمالي الرسائل غير المقروءة
   */
  const calculateUnreadCount = () => {
    unreadCount.value = conversations.value.reduce((total, conv) => total + conv.unread, 0);
  };

  /**
   * تنسيق الوقت
   */
  const formatTimestamp = (timestamp) => {
    if (!timestamp) return 'Now';

    const now = Date.now();
    const diff = Math.floor((now - timestamp) / 1000);

    if (diff < 60) return 'Now';
    if (diff < 3600) return `${Math.floor(diff / 60)} min ago`;
    if (diff < 86400) return `${Math.floor(diff / 3600)} hours ago`;
    if (diff < 604800) return `${Math.floor(diff / 86400)} days ago`;

    const date = new Date(timestamp);
    return date.toLocaleDateString();
  };

  /**
   * إغلاق المحادثة الحالية
   */
  const closeChat = () => {
    currentChat.value = null;
    messages.value = [];
    if (messagesListener) {
      messagesListener();
      messagesListener = null;
    }
  };

  /**
   * تنظيف الـ listeners عند unmount
   */
  const cleanup = () => {
    if (conversationsListener) {
      conversationsListener();
    }
    if (messagesListener) {
      messagesListener();
    }
  };

  /**
   * إعادة جلب المحادثات بعد ظهور المستخدم (مهم للموقع حيث fetchUser غير متزامن)
   */
  const refetchWhenUserReady = () => {
    if (conversationsListener) {
      conversationsListener();
      conversationsListener = null;
    }
    initCurrentUser();
    if (getCompositeUserId(currentUser.value)) {
      fetchConversations();
    }
  };

  // Lifecycle
  onMounted(() => {
    // إن وُجد store من الموقع، نعتمد عليه لتعيين المستخدم (getters.getUser = القيمة وليست دالة)
    if (websiteStore && websiteStore.getters) {
      const user = websiteStore.getters.getUser;
      if (user && user.id) {
        const u = { ...user };
        if (!u.userType) u.userType = 'user';
        currentUser.value = u;
        localStorage.setItem('user', JSON.stringify(u));
      }
    }
    if (!currentUser.value) initCurrentUser();
    fetchConversations();

    // على الموقع: عند تمرير store، نعيد المحاولة عند ظهور المستخدم (fetchUser ينتهي لاحقاً)
    if (websiteStore && websiteStore.getters) {
      let attempts = 0;
      const maxAttempts = 20;
      const intervalId = setInterval(() => {
        attempts++;
        const user = websiteStore.getters.getUser;
        if (user && user.id) {
          const composite = getCompositeUserId(currentUser.value);
          if (!composite || currentUser.value?.id !== user.id) {
            const u = { ...user };
            if (!u.userType) u.userType = 'user';
            currentUser.value = u;
            localStorage.setItem('user', JSON.stringify(u));
            refetchWhenUserReady();
          }
          clearInterval(intervalId);
        }
        if (attempts >= maxAttempts) clearInterval(intervalId);
      }, 500);
    }
  });

  onUnmounted(() => {
    cleanup();
  });

  return {
    // State
    conversations,
    currentChat,
    messages,
    unreadCount,
    isLoading,
    currentUser,

    // Methods
    fetchConversations,
    openChat,
    closeChat,
    sendMessage,
    sendImage,
    sendAudio,
    markAsRead,
    createConversation,
    deleteMessage,
    searchConversations,
    formatTimestamp,
    cleanup,
  };
}
