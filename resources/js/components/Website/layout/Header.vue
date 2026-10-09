<template>
    <div class="custom-header-bg">
        <div class="container py-3">
            <div class="d-flex align-items-center justify-content-between">
                <!-- Logo -->
                <router-link :to="`/${locale}`" class="header-logo-link">
                    <img
                        alt="Logo"
                        src="/assets/media/svg/brand-logos/logo.png"
                        class="header-logo-img"
                    />
                </router-link>
                <!-- Right side: Language & Login -->
                <div class="d-flex align-items-center gap-3">
                    <!-- <button
                        class="header-btn header-lang-btn"
                        @click="switchLocale" 
                    >
                        {{ otherLocale === "en" ? "English" : "العربية" }}
                        <span class="header-lang-caret">▼</span>
                    </button> -->
                    <button
                        class="header-btn header-messages-btn w-45"
                        @click="openMessagesModal"
                        style="position: relative"
                    >
                        <i class="fas fa-envelope"></i>
                        <span
                            v-if="isLoggedIn && unreadCount > 0"
                            class="messages-badge"
                            >{{ unreadCount }}</span
                        >
                    </button>
                    <button @click="openNotifications"
                        class="header-btn header-notifications-btn w-45"
                        
                        style="position: relative"
                    >
                        <i class="fas fa-bell"></i>
                        <span
                            v-if="isLoggedIn && unreadNotificationsCount > 0"
                            class="messages-badge"
                            >{{ unreadNotificationsCount }}</span
                        >
                    </button>
                    <!-- Hamburger Menu Icon -->
                    <button 
                        class="header-btn header-menu-btn w-45"
                        @click="showDrawer = true"
                        style="
                            background: transparent;
                            color: #1a2733;
                            box-shadow: none;
                            min-width: 48px;
                            padding: 8px 8px;
                            border: none;
                        ">
                        <svg
                            width="36"
                            height="36"
                            viewBox="0 0 36 36"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <rect
                                x="8"
                                y="9"
                                width="20"
                                height="3"
                                rx="1.5"
                                fill="#1a2733"
                            />
                            <rect
                                x="8"
                                y="16"
                                width="20"
                                height="3"
                                rx="1.5"
                                fill="#1a2733"
                            />
                            <rect
                                x="8"
                                y="23"
                                width="12"
                                height="3"
                                rx="1.5"
                                fill="#ffffff"
                            />
                        </svg>
                    </button>
                    <router-link v-if="!isLoggedIn"
                        :to="`/${locale}/login`"
                        class="header-btn header-login-btn"
                    >
                        {{ translations?.login || "Login" }}
                    </router-link>
                </div>
            </div>
        </div>
        <!-- Messages Modal -->
        <div
            v-if="showMessages"
            class="modal-backdrop-custom"
            @click.self="showMessages = false"
        >
            <div
                class="modal-custom messages-modal"
                style="max-width: 900px; width: 100%">
                <div class="text-center mb-4">
                    <img
                        src="@/assets/logo-b.png"
                        style="width: 80px; border-radius: 8px"
                    />
                    <h2 class="fw-bold mt-2">{{translations?.messages || 'Messages'}}</h2>
                </div>
                <div class="mb-4">
                    <input
                        v-model="searchQuery"
                        type="text"
                        class="form-control"
                        :placeholder="translations?.search || 'Search conversations...'" 
                        style="max-width: 500px; margin: 0 auto"
                        @input="handleSearch"
                    />
                </div>
                <!-- Quick test: create conversation -->
             
                <div v-if="!currentChat">
                    <div v-if="filteredConversations.length === 0 && searchQuery" class="text-center text-muted p-4">
                        <i class="fas fa-search fa-2x mb-2"></i>
                        <p>No conversations found matching "{{ searchQuery }}"</p>
                    </div>
                    <div v-else class="row g-4">
                        <div
                            class="col-md-6"
                            v-for="conv in filteredConversations"
                            :key="conv.id"
                        >
                            <div
                                class="d-flex align-items-center gap-3 bg-white rounded-4 shadow-sm p-3 mb-2 chat-user-hover"
                                @click="openChat(conv)"
                            >
                                <div style="position: relative">
                                    <img
                                        :src="conv.avatar"
                                        class="rounded-circle"
                                        style="
                                            width: 56px;
                                            height: 56px;
                                            object-fit: cover;
                                        "
                                    />
                                    <span
                                        v-if="conv.unread"
                                        class="badge bg-warning text-dark"
                                        style="
                                            position: absolute;
                                            top: -8px;
                                            left: -8px;
                                        "
                                        >{{ conv.unread }}</span
                                    >
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold">
                                        <UserNameLink
                                            :name="conv.name"
                                            :user-id="conv.otherId"
                                        />
                                        <span class="text-muted small ms-2">{{
                                            conv.time
                                        }}</span>
                                    </div>
                                    <div class="text-muted small">
                                        {{ conv.text }}
                                    </div>
                                </div>
                                <div
                                    style="
                                        font-size: 1.5rem;
                                        color: #888;
                                        cursor: pointer;
                                    "
                                >
                                    <i class="fas fa-ellipsis-v"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-else>
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <button
                            class="btn btn-link"
                            @click="closeChat()"
                        >
                            <i class="fas fa-arrow-left"></i>
                        </button>
                        <img
                            :src="currentChat?.avatar"
                            class="rounded-circle"
                            style="width: 48px; height: 48px"
                        />
                        <UserNameLink
                            :name="currentChat?.name"
                            :user-id="currentChat?.otherId"
                            link-class="fw-bold fs-4"
                        />
                    </div>
                    <div class="chat-messages">
                        <div
                            v-for="(msg, idx) in messages"
                            :key="msg.id || idx"
                            :class="[
                                'chat-message-row',
                                msg.from === 'me' ? 'chat-message-me' : 'chat-message-them',
                            ]"
                        >
                            <div class="chat-bubble-wrap">
                                <div
                                    :class="[
                                        'chat-bubble',
                                        msg.from === 'me'
                                            ? 'chat-bubble-me'
                                            : 'chat-bubble-them',
                                    ]"
                                >
                                <!-- عرض الصورة -->
                                <div v-if="msg.kind === 'image' && msg.mediaUrl" class="chat-message-image-wrap">
                                    <img 
                                        :src="msg.mediaUrl"
                                        class="chat-message-image"
                                        @click="openImagePreview(msg.mediaUrl)"
                                        alt="Image"
                                        @error="console.error('❌ Image load error:', msg.mediaUrl)"
                                        @load="console.log('✅ Image loaded:', msg.mediaUrl)"
                                    />
                                    <div v-if="msg.text && msg.text !== '📷 Image'" class="small text-muted mt-1">{{ msg.text }}</div>
                                </div>
                                <!-- عرض الصوت -->
                                <div v-else-if="msg.kind === 'audio'" class="audio-message" style="margin-bottom: 5px;">
                                    <div v-if="msg.mediaUrl">
                                        <audio 
                                            :key="`audio-${msg.id}`"
                                            controls 
                                            preload="metadata"
                                            style="max-width: 250px; min-width: 200px; display: block !important; width: 100%; outline: none; visibility: visible; opacity: 1;"
                                            @error="console.error('❌ Audio load error:', msg.mediaUrl, event)"
                                            @loadeddata="console.log('✅ Audio loaded:', msg.mediaUrl, 'msg:', msg)"
                                            @canplay="console.log('✅ Audio can play:', msg.mediaUrl)"
                                            @loadstart="console.log('🔄 Audio loading started:', msg.mediaUrl)"
                                        >
                                            <source :src="msg.mediaUrl" type="audio/webm">
                                            <source :src="msg.mediaUrl" type="audio/mpeg">
                                            <source :src="msg.mediaUrl" type="audio/wav">
                                            <source :src="msg.mediaUrl" type="audio/ogg">
                                            Your browser does not support the audio element.
                                        </audio>
                                        <div v-if="msg.text && msg.text !== '🎤 Voice message'" class="small text-muted mt-1">{{ msg.text }}</div>
                                    </div>
                                    <div v-else class="text-warning small p-2 bg-warning bg-opacity-10 rounded">
                                        <i class="fas fa-exclamation-triangle"></i> Audio file not found
                                        <div class="small text-muted mt-1">kind: {{ msg.kind }}, mediaUrl: {{ msg.mediaUrl || 'null' }}</div>
                                    </div>
                                </div>
                                <!-- عرض النص -->
                                <span v-else>{{ msg.text }}</span>
                                
                                </div>
                                <div class="small text-muted chat-time" v-if="msg.time">
                                    {{ msg.time }}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- معاينة الصورة (فوق حقل النص) -->
                    <div v-if="imagePreview && currentChat" class="mb-2 position-relative d-inline-block">
                        <img :src="imagePreview" style="max-width: 200px; max-height: 200px; border-radius: 8px; display: block;" />
                        <!-- زر X للحذف -->
                        <button 
                            class="btn btn-sm btn-danger position-absolute"
                            @click="cancelImagePreview"
                            style="top: -10px; right: -10px; width: 30px; height: 30px; border-radius: 50%; padding: 0; display: flex; align-items: center; justify-content: center; z-index: 10; box-shadow: 0 2px 4px rgba(0,0,0,0.2);"
                            type="button"
                            title="Remove image"
                        >
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <div class="d-flex align-items-center mt-3" v-if="currentChat">
                        <!-- زر الصورة -->
                        <button
                            class="btn btn-link p-2"
                            @click="triggerImageInput"
                            type="button"
                            title="Send Image"
                            style="color: #6c757d; min-width: 40px;"
                        >
                            <i class="fas fa-image fa-lg"></i>
                        </button>
                        <!-- Input مخفي للصور -->
                        <input
                            ref="imageInput"
                            type="file"
                            accept="image/*"
                            style="display: none;"
                            @change="handleImageSelect"
                        />
                        
                        <!-- زر الصوت -->
                        <button
                            v-if="!isRecording"
                            class="btn btn-link p-2"
                            @mousedown.prevent="handleStartRecording"
                            @touchstart.prevent="handleStartRecording"
                            @contextmenu.prevent
                            type="button"
                            title="Hold to record voice"
                            style="color: #6c757d; min-width: 40px;"
                        >
                            <i class="fas fa-microphone fa-lg"></i>
                        </button>
                        <!-- زر إيقاف التسجيل (يظهر أثناء التسجيل) -->
                        <button
                            v-else
                            class="btn btn-link p-2"
                            @click="handleStopRecording"
                            @contextmenu.prevent
                            type="button"
                            title="Click to stop and send"
                            style="color: #dc3545; min-width: 40px;"
                        >
                            <i class="fas fa-stop fa-lg"></i>
                        </button>
                        
                        <!-- Input النص -->
                        <input
                            v-model="newMessage"
                            @keyup.enter="handleSendMessage"
                            class="form-control"
                            placeholder="Type a message..."
                            style="flex: 1;"
                        />
                        <!-- زر الإرسال -->
                        <button
                            class="btn btn-primary ms-2"
                            @click="handleSendMessage"
                            :disabled="(!newMessage || newMessage.trim() === '') && !imagePreview"
                            type="button"
                            style="min-width: 45px;"
                            :title="imagePreview ? 'Send image' : 'Send message'"
                        >
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                    
                    <!-- رسالة عند عدم وجود محادثة مفتوحة -->
                    <div v-else class="text-center text-muted mt-3 p-3">
                        <i class="fas fa-comments fa-2x mb-2"></i>
                        <p>Select a conversation to start messaging</p>
                </div>
                    
                    <!-- مؤشر التسجيل مع زر الإيقاف -->
                    <div v-if="isRecording" class="recording-indicator mt-2 p-3 bg-light rounded d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-circle fa-pulse text-danger me-2"></i>
                            <span class="text-danger fw-bold">Recording...</span>
                            <span class="text-muted ms-2">Release button or click stop to send</span>
                        </div>
                        <button
                            class="btn btn-danger btn-sm"
                            @click="handleStopRecording"
                            type="button"
                            title="Stop and send recording"
                        >
                            <i class="fas fa-stop me-1"></i>
                            Stop & Send
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Notifications Modal --> 
        <div
            v-if="showNotifications"
            class="modal-backdrop-custom"
            @click.self="showNotifications = false">
            <div
                class="modal-custom notifications-modal"
                style="max-width: 700px; width: 100%"
            > 
                <h2 class="fw-bold mb-4">{{ translations?.notifications || "Notifications" }}</h2>
                <div class="notifications-list">
                    <template v-for="(items, label) in notifications" :key="label">
                      <div class="notifications-time-separator">
                        <span>{{ label }}</span>
                      </div>

                      <div
                        v-for="notif in items"
                        :key="notif.id"
                        class="notification-card"
                      >
                        <img :src="notif.img || defaultNotifImg" class="notification-img" />
                        <div class="notification-content">
                          <div class="notification-title">
                            {{ notif.title }}
                          </div>
                          <div class="notification-body">
                            {{ notif.body }}
                          </div>
                          <div class="notification-time">
                            {{ formatDate(notif.created_at_human || notif.created_at) }}
                          </div>
                        </div>
                      </div>
                    </template>

                </div>
            </div>
        </div>
        <!-- Side Drawer -->
        <div
            v-if="showDrawer"
            class="side-drawer-backdrop"
            @click.self="showDrawer = false">
            <div
                class="side-drawer"
                :class="locale === 'ar' ? 'side-drawer-rtl' : 'side-drawer-ltr'"
            >
                <!-- Drawer Header -->
                <div class="drawer-header">
                    <button
                        class="drawer-close-btn"
                        @click="showDrawer = false"
                    >
                        <i class="fas fa-times"></i>
                    </button>
                    <div class="drawer-profile">
                        <img v-if="isLoggedIn && user && user.logo" :src="user.logo" class="drawer-avatar" />
                        <div v-if="isLoggedIn" class="drawer-username">{{ user ? user.name : "Loading..." }}</div>
                          <router-link v-else
                                       :to="`/${locale}/login`"
                                       class="header-btn header-login-btn">
                            {{ translations.signin || "Login" }}
                          </router-link>
                    </div>
                </div>
                <!-- Drawer Menu -->
                <div class="drawer-menu">
                    <div class="drawer-menu-item" @click="goToMyPage" v-if="isLoggedIn">
                        <i class="fas fa-user-edit"></i>
                        <span>{{ translations?.my_account || "My Account" }}</span>
                    </div>
                    <div class="drawer-menu-item" @click="goToAddAd" v-if="isLoggedIn">
                        <i class="fas fa-plus-circle"></i>
                        <span>{{ translations?.Add_your_Ads || "Add your Ads" }}</span>
                    </div>
                    <div class="drawer-menu-item" @click="goToPayCommission" v-if="isLoggedIn">
                        <i class="fas fa-credit-card"></i>
                        <span>{{ translations?.Pay_Commission || "Pay Commission" }}</span>
                    </div>
                    <!-- <div class="drawer-menu-item" v-if="isLoggedIn">
                        <i class="fas fa-star"></i>
                        <span>Rate App</span>
                    </div> -->
                   <router-link :to="`/${locale}/terms`" class="drawer-menu-item">
                      <i class="fas fa-file-alt"></i>
                      <span>{{ translations?.terms_and_conditions || "terms and conditions" }}</span>
                    </router-link>

                    <router-link :to="`/${locale}/contact-us`" class="drawer-menu-item">
                        <i class="fas fa-headphones"></i>
                        <span>{{ translations?.ContactUs || "ContactUs" }}</span>
                    </router-link>

                    <router-link :to="`/${locale}/about-us`" class="drawer-menu-item">
                        <i class="fas fa-info-circle"></i>
                        <span>{{ translations?.about_us || "about_us" }}</span>
                    </router-link>

                    <div class="drawer-menu-item" v-if="isLoggedIn">
                        <i class="fas fa-bell"></i>
                        <span>{{ translations?.notifications || "notifications" }}</span>
                        <label class="switch">
                            <input
                                type="checkbox"
                                v-model="notificationsEnabled"
                            />
                            <span class="slider round"></span>
                        </label>
                    </div>
                    <div class="drawer-menu-item" @click="switchLocale">
                        <i class="fas fa-globe"></i>
                        <span>{{ translations?.Language || "Language" }}</span>
                        <span style="margin-left: auto; font-size: 0.95em"
                            >{{ otherLocale === "en" ? "English" : "العربية" }}</span
                        >
                    </div>
                    <div class="drawer-menu-item" v-if="isLoggedIn">
                        <i class="fas fa-heart"></i>
                        <span>{{ translations?.Favorite || "Favorite" }}</span>
                    </div>
                    <div class="drawer-menu-item" @click="shareApp">
                        <i class="fas fa-share-alt"></i>
                        <span>{{ translations?.Share_App || "Share App" }}</span>
                    </div>
                </div>
                <div class="drawer-logout" @click="logout()" v-if="isLoggedIn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>{{ translations?.logout || "logout" }}</span>
                </div>
            </div>
        </div>
    </div>
</template>
  
<script>
import notifyImg from "@/assets/notify.png";
import { authApi } from "@/axiosUser"; 
import { ref, nextTick, computed, watch } from 'vue';
import { useStore } from 'vuex';
import { useChat } from '@/useChat';
import { navigateToUserProfile } from '@/utils/userProfile';
import { requireLogin } from '@/utils/requireLogin';

export default {
    name: "Header",
    setup() {
        const store = useStore();
        const {
            conversations,
            currentChat,
            messages,
            unreadCount,
            openChat,
            closeChat,
            sendMessage,
            sendImage,
            sendAudio,
            createConversation,
            fetchConversations,
            searchConversations,
        } = useChat(store);

        const newMessage = ref('');
        const testReceiverId = ref('');
        const testReceiverName = ref('');
        const testReceiverAvatar = ref('');
        const searchQuery = ref('');
        const filteredConversations = ref([]);
        
        // للصور
        const imageInput = ref(null);
        const imagePreview = ref(null);
        const selectedImageFile = ref(null);
        
        // للصوت
        const isRecording = ref(false);
        const mediaRecorder = ref(null);
        const audioChunks = ref([]);
        const mediaStream = ref(null); // لحفظ stream reference
        const isRecordingStarted = ref(false); // للتحقق من أن التسجيل بدأ فعلياً
        const recordingStartTime = ref(0); // وقت بدء التسجيل

        const handleSendMessage = async () => {
            console.log('=== handleSendMessage START ===');
            console.log('newMessage.value:', newMessage.value);
            console.log('imagePreview.value:', imagePreview.value);
            console.log('currentChat.value:', currentChat.value);
            
            if (!currentChat?.value) {
                console.warn('❌ No current chat selected');
                alert('Please select a conversation');
                return;
            }
            
            // إذا كانت هناك صورة معاينة، أرسلها أولاً
            if (imagePreview.value && selectedImageFile.value) {
                console.log('📷 Image preview found, sending image...');
                await sendSelectedImage();
                // بعد إرسال الصورة، إذا كان هناك نص أيضاً، أرسله
                if (newMessage.value && newMessage.value.trim()) {
                    // انتظر قليلاً ثم أرسل النص
                    setTimeout(async () => {
                        await sendTextMessage();
                    }, 300);
                }
                return;
            }
            
            // إرسال النص فقط
            await sendTextMessage();
        };
        
        // دالة إرسال النص فقط
        const sendTextMessage = async () => {
            if (!newMessage.value || !newMessage.value.trim()) {
                console.warn('❌ Message is empty');
                return;
            }
            
            if (!currentChat?.value) {
                console.warn('❌ No current chat selected');
                alert('Please select a conversation');
                return;
            }
            
            const chatId = currentChat.value.chatId || currentChat.value.id;
            console.log('Using chatId:', chatId);
            
            if (!chatId) {
                console.warn('❌ Chat ID is missing', currentChat.value);
                alert('Chat ID is missing. Please reopen the conversation.');
                return;
            }
            
            try {
                console.log('📤 Sending message to Firebase...', {
                    chatId: chatId,
                    message: newMessage.value
                });
                
                const result = await sendMessage(chatId, newMessage.value);
                console.log('✅ Send result:', result);
                
                if (result) {
                    console.log('✅ Message sent successfully!');
            newMessage.value = '';
            await nextTick();
            const chatBox = document.querySelector('.chat-messages');
                    if (chatBox) {
                        chatBox.scrollTop = chatBox.scrollHeight;
                        console.log('✅ Scrolled to bottom');
                    }
                } else {
                    console.error('❌ Failed to send message - sendMessage returned false');
                    alert('Failed to send message. Check console for details.');
                }
            } catch (error) {
                console.error('❌ Error in handleSendMessage:', error);
                console.error('Error stack:', error.stack);
                alert('Error sending message: ' + (error.message || error));
            }
            
            console.log('=== handleSendMessage END ===');
        };

        const handleCreateConversation = async () => {
            try {
                if (!testReceiverId.value || !testReceiverName.value) {
                    alert('Please fill Receiver ID and Receiver Name');
                    return;
                }
 
                                    console.log(localStorage)

                const localUser = localStorage.getItem('user');
                if (!localUser) {
                    alert('You need to be logged in. localStorage.user is missing.');
                    return;
                }
                const chatId = await createConversation(
                    testReceiverId.value,
                    testReceiverName.value,
                    testReceiverAvatar.value || ''
                );
                if (!chatId) {
                    alert('Failed to create conversation (check Firebase rules/console).');
                    return;
                }
                setTimeout(() => {
                    const conv = conversations.value.find(c => c.chatId === chatId || c.id === chatId);
                    if (conv) {
                        openChat(conv);
                    } else {
                        alert('Conversation created but not yet listed. Try reopening the modal.');
                    }
                }, 400);
            } catch (e) {
                console.error('createConversation error', e);
                alert('Error while creating conversation. Check console for details.');
            }
        };

        // Computed للتحقق من إمكانية الإرسال
        const canSendMessage = computed(() => {
            const hasChat = !!currentChat.value;
            const hasMessage = !!newMessage.value && newMessage.value.trim().length > 0;
            const result = hasChat && hasMessage;
            
            console.log('canSendMessage check:', {
                hasChat,
                hasMessage,
                currentChat: currentChat.value,
                newMessage: newMessage.value,
                result
            });
            
            return result;
        });

        // دالة فتح معاينة الصورة
        const openImagePreview = (imageUrl) => {
            window.open(imageUrl, '_blank');
        };
        
        // دالة فتح اختيار الصورة
        const triggerImageInput = () => {
            if (imageInput.value) {
                imageInput.value.click();
            }
        };
        
        // دالة معالجة اختيار الصورة
        const handleImageSelect = (event) => {
            const file = event.target.files[0];
            if (!file) return;
            
            // التحقق من نوع الملف
            if (!file.type.startsWith('image/')) {
                alert('Please select an image file');
                return;
            }
            
            // التحقق من الحجم (مثلاً 10MB)
            if (file.size > 10 * 1024 * 1024) {
                alert('Image size should be less than 10MB');
                return;
            }
            
            selectedImageFile.value = file;
            
            // عرض معاينة
            const reader = new FileReader();
            reader.onload = (e) => {
                imagePreview.value = e.target.result;
            };
            reader.readAsDataURL(file);
            
            // لا نرسل الصورة تلقائياً - المستخدم يختار متى يرسلها
        };
        
        // دالة إرسال الصورة المحددة
        const sendSelectedImage = async () => {
            if (!selectedImageFile.value || !currentChat.value) {
                console.warn('❌ No image selected or no chat open');
                return;
            }
            
            const chatId = currentChat.value.chatId || currentChat.value.id;
            if (!chatId) {
                console.warn('❌ No chatId found');
                return;
            }
            
            try {
                console.log('📤 Sending selected image...');
                const result = await sendImage(chatId, selectedImageFile.value);
                if (result) {
                    console.log('✅ Image sent successfully');
                    // مسح المعاينة والملف
                    cancelImagePreview();
                } else {
                    alert('Failed to send image');
                }
            } catch (error) {
                console.error('❌ Error sending image:', error);
                alert('Error sending image: ' + error.message);
            }
        };
        
        // إلغاء معاينة الصورة
        const cancelImagePreview = () => {
            imagePreview.value = null;
            selectedImageFile.value = null;
            if (imageInput.value) {
                imageInput.value.value = '';
            }
        };
        
        // بدء التسجيل الصوتي (مع معالجة الأحداث)
        const handleStartRecording = async (event) => {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            console.log('🎤 handleStartRecording called');
            
            if (!currentChat.value) {
                console.warn('❌ No chat selected');
                alert('Please select a conversation first');
                return;
            }
            
            if (isRecording.value) {
                console.warn('⚠️ Already recording');
                return;
            }
            
            // تعيين flag قبل البدء
            isRecording.value = true;
            isRecordingStarted.value = false;
            recordingStartTime.value = Date.now();
            
            console.log('🎤 Starting recording...');
            try {
                await startRecording();
            } catch (error) {
                console.error('❌ Error in handleStartRecording:', error);
                isRecording.value = false;
                isRecordingStarted.value = false;
            }
        };
        
        // معالجة mouseleave (لا نوقف التسجيل إذا كان المستخدم لا يزال يضغط)
        const handleMouseLeave = (event) => {
            // لا نفعل شيء - نترك المستخدم يضغط حتى لو خرج الماوس من الزر
            console.log('🖱️ Mouse left button area, but continuing recording if active');
        };
        
        // إيقاف التسجيل الصوتي (مع معالجة الأحداث)
        const handleStopRecording = (event) => {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            console.log('🛑 handleStopRecording called');
            console.log('🛑 isRecording:', isRecording.value);
            console.log('🛑 isRecordingStarted:', isRecordingStarted.value);
            
            // التحقق من أن التسجيل بدأ فعلياً (على الأقل 100ms)
            const recordingDuration = Date.now() - recordingStartTime.value;
            if (recordingDuration < 100) {
                console.warn('⚠️ Recording too short, ignoring stop');
                return;
            }
            
            if (!isRecording.value || !isRecordingStarted.value) {
                console.log('⚠️ Not recording or not started yet, ignoring stop');
                return;
            }
            
            console.log('🛑 Stopping recording...');
            try {
                stopRecording();
            } catch (error) {
                console.error('❌ Error in handleStopRecording:', error);
            }
        };
        
        // بدء التسجيل الصوتي
        const startRecording = async () => {
            if (!currentChat.value) {
                console.warn('❌ No currentChat.value');
                return;
            }
            
            try {
                console.log('🎤 Requesting microphone access...');
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                console.log('✅ Microphone access granted');
                
                mediaStream.value = stream; // حفظ stream reference
                
                // تحديد نوع MIME المتاح
                let mimeType = 'audio/webm';
                if (!MediaRecorder.isTypeSupported('audio/webm')) {
                    mimeType = 'audio/mp4';
                    if (!MediaRecorder.isTypeSupported('audio/mp4')) {
                        mimeType = ''; // استخدام الافتراضي
                    }
                }
                
                const options = mimeType ? { mimeType } : {};
                const recorder = new MediaRecorder(stream, options);
                audioChunks.value = [];
                
                console.log('🎤 MediaRecorder created with options:', options);
                
                recorder.ondataavailable = (event) => {
                    console.log('📦 Audio data available:', event.data.size, 'bytes');
                    if (event.data.size > 0) {
                        audioChunks.value.push(event.data);
                    }
                };
                
                recorder.onstop = async () => {
                    console.log('🛑 Recording stopped, processing audio...');
                    console.log('📦 Total chunks:', audioChunks.value.length);
                    
                    // إيقاف الميكروفون أولاً
                    if (mediaStream.value) {
                        mediaStream.value.getTracks().forEach(track => {
                            track.stop();
                            console.log('🔇 Microphone track stopped');
                        });
                        mediaStream.value = null;
                    }
                    
                    // تنظيف mediaRecorder
                    mediaRecorder.value = null;
                    isRecording.value = false;
                    isRecordingStarted.value = false;
                    recordingStartTime.value = 0;
                    
                    if (audioChunks.value.length === 0) {
                        console.warn('⚠️ No audio chunks recorded');
                        audioChunks.value = [];
                        return;
                    }
                    
                    const audioBlob = new Blob(audioChunks.value, { type: mimeType || 'audio/webm' });
                    console.log('✅ Audio blob created:', audioBlob.size, 'bytes');
                    
                    if (audioBlob.size === 0) {
                        console.warn('⚠️ Audio blob is empty');
                        audioChunks.value = [];
                        return;
                    }
                    
                    // إرسال الصوت
                    const chatId = currentChat.value?.chatId || currentChat.value?.id;
                    if (chatId) {
                        const audioFile = new File([audioBlob], `voice_${Date.now()}.webm`, { type: mimeType || 'audio/webm' });
                        console.log('📤 Sending audio file:', audioFile.name, audioFile.size, 'bytes');
                        try {
                            const result = await sendAudio(chatId, audioFile);
                            if (result) {
                                console.log('✅ Audio sent successfully');
                            } else {
                                console.error('❌ Failed to send audio');
                                alert('Failed to send audio message');
                            }
                        } catch (error) {
                            console.error('❌ Error sending audio:', error);
                            alert('Error sending audio: ' + error.message);
                        }
                    } else {
                        console.error('❌ No chatId found');
                        alert('No chat selected');
                    }
                    
                    audioChunks.value = [];
                };
                
                recorder.onerror = (error) => {
                    console.error('❌ MediaRecorder error:', error);
                    isRecording.value = false;
                };
                
                recorder.start(100); // جمع البيانات كل 100ms
                console.log('✅ Recording started');
                mediaRecorder.value = recorder;
                isRecordingStarted.value = true; // التسجيل بدأ فعلياً
                
                // تأكيد أن isRecording لا يزال true
                if (!isRecording.value) {
                    isRecording.value = true;
                }
            } catch (error) {
                console.error('❌ Error accessing microphone:', error);
                isRecording.value = false;
                alert('Could not access microphone. Please check permissions.\n\nError: ' + error.message);
            }
        };
        
        // إيقاف التسجيل الصوتي
        const stopRecording = () => {
            console.log('🛑 stopRecording called');
            console.log('🛑 mediaRecorder.value:', mediaRecorder.value);
            console.log('🛑 isRecording.value:', isRecording.value);
            console.log('🛑 mediaStream.value:', mediaStream.value);
            
            if (mediaRecorder.value && isRecording.value) {
                console.log('🛑 Stopping MediaRecorder...');
                console.log('🛑 MediaRecorder state:', mediaRecorder.value.state);
                
                try {
                    if (mediaRecorder.value.state === 'recording') {
                        console.log('🛑 MediaRecorder is recording, stopping...');
                        mediaRecorder.value.stop();
                    } else if (mediaRecorder.value.state === 'paused') {
                        console.log('🛑 MediaRecorder is paused, resuming then stopping...');
                        mediaRecorder.value.resume();
                        mediaRecorder.value.stop();
                    } else {
                        console.log('🛑 MediaRecorder state is:', mediaRecorder.value.state);
                    }
                } catch (error) {
                    console.error('❌ Error stopping recorder:', error);
                    // محاولة إيقاف stream مباشرة
                    if (mediaStream.value) {
                        mediaStream.value.getTracks().forEach(track => {
                            track.stop();
                            console.log('🔇 Track stopped directly');
                        });
                        mediaStream.value = null;
                    }
                }
                
                // لا نضع isRecording.value = false هنا لأن onstop سيتم استدعاؤه ويفعل ذلك
                // لا نضع mediaRecorder.value = null هنا لأن onstop سيتم استدعاؤه
            } else {
                // إذا كان التسجيل لا يزال نشطاً لكن mediaRecorder null، أوقف stream مباشرة
                if (isRecording.value && mediaStream.value) {
                    console.log('🛑 Stopping stream directly (no mediaRecorder)...');
                    mediaStream.value.getTracks().forEach(track => {
                        track.stop();
                        console.log('🔇 Track stopped');
                    });
                    mediaStream.value = null;
                    isRecording.value = false;
                } else if (isRecording.value) {
                    console.warn('⚠️ isRecording is true but no mediaRecorder or mediaStream');
                    isRecording.value = false;
                }
            }
        };
        
        // دالة البحث
        const handleSearch = () => {
            if (!searchQuery.value || searchQuery.value.trim() === '') {
                filteredConversations.value = conversations.value;
            } else {
                filteredConversations.value = searchConversations(searchQuery.value);
            }
        };
        
        // مراقبة conversations لتحديث filteredConversations
        watch(conversations, (newConversations) => {
            if (!searchQuery.value || searchQuery.value.trim() === '') {
                filteredConversations.value = newConversations;
            } else {
                filteredConversations.value = searchConversations(searchQuery.value);
            }
        }, { immediate: true });

        return {
            conversations,
            currentChat,
            messages,
            unreadCount,
            openChat,
            closeChat,
            newMessage,
            handleSendMessage,
            handleCreateConversation,
            testReceiverId,
            testReceiverName,
            testReceiverAvatar,
            canSendMessage,
            fetchConversations,
            searchQuery,
            filteredConversations,
            handleSearch,
            // للصور
            imageInput,
            imagePreview,
            triggerImageInput,
            handleImageSelect,
            cancelImagePreview,
            openImagePreview,
            // للصوت
            isRecording,
            handleStartRecording,
            handleStopRecording,
            startRecording,
            stopRecording,
        };
    },
    data() {
        return {
            showMessages: false,
            showNotifications: false,
            activeChatUser: null,
            defaultNotifImg: "/assets/media/svg/brand-logos/logo.png",
            notifications: [],
            showDrawer: false,
           
            isLoggedIn: !!localStorage.getItem("token"),

            notificationsEnabled: false,

        };
    },
    computed: {
        locale() {
            return this.$route.params.locale || "en";
        },
       
        user() {
            return this.$store.getters.getUser;
        },
        
        otherLocale() {
            return this.locale === "en" ? "ar" : "en";
        },
        translations() {
            return this.$store.getters.getTranslations || {};
        },


        
       unreadNotificationsCount() {
        let count = 0;

        Object.values(this.notifications).forEach(group => {
          group.forEach(notif => {
            if (notif.read === 0) {
              count++;
            }
          });
        });
        return count;
       },


       
    },

    mounted() {
        this.$store.dispatch("fetchSetting");
        this.$store.dispatch("fetchUser");

        this.getNotifications();

        setInterval(() => {
            this.getNotifications();
          }, 50000);

    },

   
    methods: {
        openMessagesModal() {
            if (!requireLogin(this.$store)) {
                return;
            }
            console.log('🔵 Opening messages modal...');
            console.log('🔵 this.fetchConversations:', this.fetchConversations);
            console.log('🔵 typeof this.fetchConversations:', typeof this.fetchConversations);
            console.log('🔵 this.conversations:', this.conversations);
            
            this.showMessages = true;
            
            // استدعاء fetchConversations بعد فتح المودال
            this.$nextTick(() => {
                console.log('🟢 After nextTick - Calling fetchConversations...');
                console.log('🟢 this.fetchConversations:', this.fetchConversations);
                
                if (this.fetchConversations && typeof this.fetchConversations === 'function') {
                    console.log('✅ fetchConversations is a function, calling it now...');
                    try {
                        this.fetchConversations();
                        console.log('✅ fetchConversations called successfully');
                    } catch (error) {
                        console.error('❌ Error calling fetchConversations:', error);
                    }
                } else {
                    console.error('❌ fetchConversations is NOT available!', {
                        fetchConversations: this.fetchConversations,
                        type: typeof this.fetchConversations,
                        isUndefined: this.fetchConversations === undefined,
                        isNull: this.fetchConversations === null
                    });
                }
            });
        },

        formatDate(val) {
            if (!val) return '';
            const d = new Date(val);
            if (isNaN(d)) return val; // لو القيمة نص جاهز، اعرضها كما هي
            return d.toLocaleString(); // بدك relative؟ استعمل مكتبة dayjs منNow لاحقاً
        },
 
        logout() {
            this.isLoading = true;
            const locale = this.locale || localStorage.getItem("locale") || "ar";

            authApi({
                method: "POST",
                url: "auth/logOut",
            })
                .catch((error) => {
                    console.log("Logout failed:", error);
                })
                .finally(() => {
                    // امسح التوكنات فقط مع الإبقاء على اللغة
                    localStorage.removeItem("authToken");
                    localStorage.removeItem("token");
                    localStorage.removeItem("user");
                    localStorage.setItem("locale", locale);
                    if (this.$store?.commit) {
                        this.$store.commit("setUser", null);
                    }
                    this.isLoggedIn = false;
                    this.showDrawer = false;
                    this.isLoading = false;
                    this.$router.push({ path: `/${locale}` });
                });
        },

        shareApp() {
            if (navigator.share) {
              navigator.share({
                title: 'Check out this app!',
                text: 'I found this awesome app, check it out!',
                url: window.location.href,
              })
              .then(() => console.log('App shared successfully'))
              .catch((error) => console.error('Error sharing', error));
            } else {
              alert('Sharing is not supported on this device/browser.');
            }
        },

        

        switchLocale() {

            let newLocale = this.otherLocale;
            import("@/axiosUser").then(({ setLanguage }) => {
                setLanguage(newLocale);
            });


            
            let currentPath = this.$route.path.replace(/^\/(en|ar)/, "");
            if (currentPath === "") {
                currentPath = "/";
            }
            


            let updatedPath = `/${newLocale}${currentPath}`;
            if (updatedPath.slice(-1) === "/") {
                updatedPath = updatedPath.slice(0, -1);
            }
            this.$router.push({ path: updatedPath, query: this.$route.query });
            this.$store.dispatch("loadTranslations", newLocale);
            localStorage.setItem("locale", newLocale);

            


            if (newLocale === "ar") {
                document.documentElement.setAttribute("dir", "rtl");
                document.documentElement.setAttribute("lang", "ar");
                document.documentElement.style.direction = "rtl";
                document.body.classList.add("rtl");
            } else {
                document.documentElement.setAttribute("dir", "ltr");
                document.documentElement.setAttribute("lang", "en");
                document.documentElement.style.direction = "ltr";
                document.body.classList.remove("rtl");
            }

        },

 

        
        goToMyPage() {
            if (!requireLogin(this.$store)) {
                this.showDrawer = false;
                return;
            }
            this.$router.push({ path: `/${this.locale}/my-page` });
            this.showDrawer = false;
        },
        goToPayCommission() {
            if (!requireLogin(this.$store)) {
                this.showDrawer = false;
                return;
            }
            this.$router.push({ path: `/${this.locale}/pay-commission` });
            this.showDrawer = false;
        },
        goToAddAd() {
            if (!requireLogin(this.$store)) {
                this.showDrawer = false;
                return;
            }
            this.$router.push({ path: `/${this.locale}/add-ad` });
            this.showDrawer = false;
        },

        goToChatUserProfile() {
            const chat = this.currentChat;
            if (!chat?.otherId) {
                return;
            }
            navigateToUserProfile(
                this.$router,
                this.locale,
                this.user,
                { id: chat.otherId, name: chat.name }
            );
            this.showMessages = false;
            this.closeChat();
        },


        swalFunction(type, text) {
            Swal.fire({
                text: text,
                icon: type,
                buttonsStyling: false,
                confirmButtonText: "Ok, got it!",
                customClass: {
                    confirmButton: "btn btn-primary",
                },
            });
        },
 

        async getNotifications() {
           
            if (!localStorage.getItem('token') || localStorage.getItem('token') == '') {
                this.notifications = [];
                return;
            }

            this.isLoading = true;

            await authApi({
                    method: "get",
                    url: "basic/getNotifications",
                    params: {
                        pagination: 0,
                    },
                })
                .then((response) => { 
                    this.notifications = response.data.items;
                    console.log(this.notifications)
                    this.isLoading = false;
                })
                .catch((error) => {
                    // this.swalFunction("error", error);
                    console.log('hi error notifications')
                    this.isLoading = false;
                });
        },

 

        async openNotifications() {
            if (!requireLogin(this.$store)) {
                return;
            }
            return this.readNotification();
        },

        async readNotification(){

            this.showNotifications = true
            this.isLoading = true;

            await authApi({
                    method: "get",
                    url: "basic/readNotification",
                    params: {
                    }, 
                })
                .then((response) => { 
                    this.getNotifications()
                    this.isLoading = false;
                })
                .catch((error) => {
                    this.swalFunction("error", error);
                    this.isLoading = false;
                });

        },




    },

    watch: {
        user: {
            immediate: true,
                handler(newVal) {
                    if (newVal) {
                        this.notificationsEnabled = Boolean(newVal.notifications_enabled);
                }
            },
        },
        showMessages(newVal) {
            if (newVal) {
                // عند فتح نافذة الرسائل، جلب المحادثات
                console.log('Messages modal opened, fetching conversations...');
                this.fetchConversations();
            }
        },
    },
};
</script>

<style scoped>
.custom-header-bg {
    background: #e5aa2a;
}
.header-logo-link {
    display: flex;
    align-items: center;
}
.header-logo-img {
    height: 56px;
    width: auto;
    border-radius: 8px;
    /* background: #1a2733; */
    padding: 8px 0px;
}
.header-btn {
    border: none;
    outline: none;
    background: #1a2733;
    color: #e5aa2a;
    font-weight: 500;
    font-size: 1rem;
    border-radius: 8px;
    padding: 10px 28px;
    margin-left: 10px;
    transition: background 0.2s, color 0.2s;
    cursor: pointer;
    min-width: 100px;
    text-align: center;
}
.header-btn:hover {
    background: #222f3a;
    color: #ffd24d;
}
.header-lang-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    background: transparent;
    color: #1a2733;
    border: 2px solid #1a2733;
    margin-left: 0;
    margin-right: 0px;
    min-width: 120px;
    padding: 8px 8px;
}
.header-lang-btn:hover {
    background: #1a2733;
    color: #e5aa2a;
}
.header-lang-caret {
    font-size: 0.8em;
    margin-left: 6px;
}
.header-login-btn {
    background: #1a2733;
    color: #e5aa2a;
}
.messages-modal {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
    padding: 32px 24px 24px 24px;
    max-height: 90vh;
    overflow-y: auto;
    overflow-x: hidden;
    position: relative;
    max-width: 100%;
    box-sizing: border-box;
}
.chat-messages {
    min-height: 300px;
    max-height: 350px;
    overflow-y: auto;
    overflow-x: hidden;
    width: 100%;
    max-width: 100%;
    box-sizing: border-box;
}
/* صف الرسالة: كل طرف بجهة */
.chat-message-row {
    display: flex;
    margin-bottom: 8px;
    width: 100%;
    max-width: 100%;
    min-width: 0;
    box-sizing: border-box;
}
.chat-message-me {
    justify-content: flex-end;
}
.chat-message-them {
    justify-content: flex-start;
}
[dir="rtl"] .chat-message-me {
    justify-content: flex-start;
}
[dir="rtl"] .chat-message-them {
    justify-content: flex-end;
}

.chat-bubble {
    display: inline-block;
    padding: 12px 16px;
    border-radius: 18px;
    margin: 0;
    max-width: 75%;
    min-width: 0;
    box-sizing: border-box;
    text-align: start;
}
.chat-message-image-wrap {
    margin-bottom: 5px;
    max-width: 100%;
    overflow: hidden;
    border-radius: 8px;
}
.chat-message-image {
    max-width: 100%;
    width: auto;
    height: auto;
    max-height: 220px;
    object-fit: contain;
    border-radius: 8px;
    cursor: pointer;
    display: block;
}
.audio-message {
    display: block !important;
    width: 100%;
}
.audio-message audio {
    display: block !important;
    width: 100%;
    max-width: 250px;
}
/* رسائلي: يمين + خلفية ذهبية خفيفة شفافة */
.chat-bubble-me {
    background: rgba(229, 170, 42, 0.35);
    color: #1a2733;
    border-bottom-right-radius: 4px;
}
[dir="rtl"] .chat-bubble-me {
    border-bottom-right-radius: 16px;
    border-bottom-left-radius: 4px;
}
/* رسائل الطرف الآخر: يسار + بدون خلفية (حد خفيف فقط) */
.chat-bubble-them {
    background: transparent;
    color: #1a1a1a;
    border: 1px solid rgba(0,0,0,0.08);
    border-bottom-left-radius: 6px;
}
.chat-bubble-wrap {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 2px;
    max-width: 85%;
    min-width: 0;
    box-sizing: border-box;
}
.chat-message-them .chat-bubble-wrap {
    align-items: flex-start;
}
.chat-time {
    font-size: 0.7rem;
    opacity: 0.85;
}
[dir="rtl"] .chat-bubble-them {
    border-bottom-left-radius: 18px;
    border-bottom-right-radius: 6px;
}
.chat-user-hover {
    transition: background 0.2s, box-shadow 0.2s;
    cursor: pointer;
}
.chat-user-hover:hover {
    background: #ededed !important;
    box-shadow: 0 4px 16px rgba(229, 170, 42, 0.1);
}
.messages-badge {
    position: absolute;
    top: -4px;
    right: -4px;
    background: #e5aa2a;
    color: #1a2733;
    border-radius: 50%;
    font-size: 0.85rem;
    min-width: 22px;
    height: 22px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    box-shadow: 0 2px 8px rgb(21 20 20 / 30%);
    z-index: 2;
    padding: 0 6px;
}
@media (max-width: 600px) {
    .header-logo-img {
        height: 40px;
        padding: 4px 8px;
    }
    .header-btn {
        font-size: 0.9rem;
        padding: 8px 12px;
        min-width: 80px;
    }
    .header-lang-btn {
        min-width: 80px;
    }
}
.notifications-modal {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
    padding: 32px 24px 24px 24px;
    max-height: 90vh;
    overflow-y: auto;
    position: relative;
}
.notifications-list {
    margin-top: 16px;
}
.notifications-time-separator {
    text-align: center;
    color: #aaa;
    font-size: 1rem;
    margin: 10px 0 10px 0;
    position: relative;
}
.notifications-time-separator span {
    background: #fff;
    padding: 0 16px;
    position: relative;
    z-index: 1;
}
.notifications-time-separator:before {
    content: "";
    display: block;
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 1px;
    background: #eee;
    z-index: 0;
}
.notification-card {
    display: flex;
    align-items: center;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(80, 80, 80, 0.07);
    padding: 16px 20px;
    margin-bottom: 8px;
    border-left: 5px solid #ffc800;
    transition: box-shadow 0.2s;
}
.notification-card:hover {
    box-shadow: 0 4px 16px rgba(229, 170, 42, 0.13);
}
.notification-img {
    width: 56px;
    height: 56px;
    border-radius: 10px;
    object-fit: cover;
    margin-right: 18px;
    background: #f6e3b1;
}
.notification-content {
    flex: 1;
}

html[dir="rtl"] .notification-content {
    margin-right: 5px;
}

/* لو الصفحة انجليزية (LTR) */
html[dir="ltr"] .notification-content {
    margin-left: 5px; /* لو بدك تعمل العكس */
}


.notification-title {
    font-weight: 600;
    font-size: 1.08rem;
    color: #222;
    margin-bottom: 4px;
}
.notification-time {
    color: #888;
    font-size: 0.95rem;
}
.side-drawer-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(0, 0, 0, 0.18);
    z-index: 2000;
}
.side-drawer {
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    width: 340px;
    background: #fff;
    box-shadow: 2px 0 16px rgba(0, 0, 0, 0.13);
    z-index: 2100;
    display: flex;
    flex-direction: column;
    transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}
.side-drawer-rtl {
    left: auto;
    right: 0;
    box-shadow: -2px 0 16px rgba(0, 0, 0, 0.13);
}
.drawer-header {
    background: #e5aa2a;
    padding: 32px 0 18px 0;
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
}
.drawer-close-btn {
    position: absolute;
    top: 12px;
    right: 16px;
    background: transparent;
    border: none;
    font-size: 1.5rem;
    color: #fff;
    cursor: pointer;
}
.drawer-profile {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-top: 8px;
}
.drawer-avatar {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #fff;
    margin-bottom: 8px;
}
.drawer-username {
    color: #fff;
    font-weight: bold;
    font-size: 1.1rem;
}
.drawer-menu {
    padding: 18px 0 0 0;
    flex: 1;
    overflow: scroll;
}
.drawer-menu-item {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px 28px;
    font-size: 1.08rem;
    color: #1a2733;
    cursor: pointer;
    transition: background 0.18s;
}
.drawer-menu-item:hover {
    background: #f6e3b1;
}
.drawer-menu-item i {
    font-size: 1.2em;
    min-width: 22px;
}
.drawer-logout {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 18px 28px;
    color: #1a2733;
    font-weight: 500;
    font-size: 1.08rem;
    border-top: 1px solid #eee;
    cursor: pointer;
}
.drawer-logout i {
    font-size: 1.2em;
}
/* Switch toggle for notification */
.switch {
    position: relative;
    display: inline-block;
    width: 38px;
    height: 22px;
    margin-left: auto;
}
.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}
.slider.round {
    border-radius: 34px;
}
.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: 0.4s;
    border-radius: 22px;
}
.switch input:checked + .slider {
    background-color: #e5aa2a;
}
.slider:before {
    position: absolute;
    content: "";
    height: 16px;
    width: 16px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: 0.4s;
    border-radius: 50%;
}
.switch input:checked + .slider:before {
    transform: translateX(16px);
}
@media (max-width: 600px) {
    .side-drawer {
        width: 90vw;
        min-width: 0;
    }
}
.drawer-menu-item i,
.drawer-logout i {
    color: #1a2733 !important;
}
</style>
