<template>
    <div id="kt_header" class="header">
        <!--begin::Container-->
        <div class="container-fluid d-flex flex-stack">
            <!--begin::Brand-->
            <div class="d-flex align-items-center me-5">
                <!--begin::Aside toggle-->
                <div
                    class="d-lg-none btn btn-icon btn-active-color-white w-30px h-30px ms-n2 me-3"
                    id="kt_aside_toggle"
                >
                    <i class="ki-duotone ki-abstract-14 fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
                <!--end::Aside  toggle-->
                <!--begin::Logo-->

                <router-link :to="`/${locale}/dashboard`">
                    <img
                        alt="Logo"
                        :src="setting ? setting.logo : 'Loading...'"
                        class="h-25px h-lg-60px"
                    />
                </router-link>

                <!--end::Logo-->
                <!--begin::Nav-->

                <!--end::Nav-->
            </div>
            <!--end::Brand-->
            <!--begin::Topbar-->
            <div class="d-flex align-items-center flex-shrink-0">
                <!--begin::Search-->
                <div
                    id="kt_header_search"
                    class="header-search d-flex align-items-stretch"
                    data-kt-search-keypress="true"
                    data-kt-search-min-length="2"
                    data-kt-search-enter="enter"
                    data-kt-search-layout="menu"
                    data-kt-menu-trigger="auto"
                    data-kt-menu-overflow="false"
                    data-kt-menu-permanent="true"
                    data-kt-menu-placement="bottom-end"
                >
                    <!--begin::Search toggle-->
                    <div
                        class="d-flex align-items-center"
                        data-kt-search-element="toggle"
                        id="kt_header_search_toggle"
                    ></div>
                    <!--end::Search toggle-->
                    <!--begin::Menu-->
                    <div
                        data-kt-search-element="content"
                        class="menu menu-sub menu-sub-dropdown p-7 w-325px w-md-375px"
                    >
                 
                    </div>
                    <!--end::Menu-->
                </div>
                <!--end::Search-->

                <!--begin::Chat-->
                <div class="d-flex align-items-center ms-1 position-relative">
                    <button
                        class="btn btn-icon btn-color-white bg-hover-white bg-hover-opacity-10 w-30px h-30px h-40px w-40px position-relative"
                        @click="openMessagesModal"
                        title="Messages"
                    >
                        <i class="ki-duotone ki-message-text-2 fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        <span v-if="unreadMessagesCount > 0"
                              class="badge badge-circle badge-danger position-absolute translate-middle top-0 start-100 animation-blink"
                              style="min-width: 18px; height: 18px; padding: 0 4px; font-size: 10px; line-height: 18px; font-weight: bold; z-index: 10;">
                            {{ unreadMessagesCount > 99 ? '99+' : unreadMessagesCount }}
                        </span>
                    </button>
                </div>
                <!--end::Chat-->

                <!--begin::Theme mode-->
                <div class="d-flex align-items-center ms-1">
                    <!--begin::Menu toggle-->
                    <a
                        href="#"
                        class="btn btn-icon btn-color-white bg-hover-white bg-hover-opacity-10 w-30px h-30px h-40px w-40px"
                        data-kt-menu-trigger="{default:'click', lg: 'hover'}"
                        data-kt-menu-attach="parent"
                        data-kt-menu-placement="bottom-end"
                    >
                        <i
                            class="ki-duotone ki-night-day theme-light-show fs-1"
                        >
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                            <span class="path4"></span>
                            <span class="path5"></span>
                            <span class="path6"></span>
                            <span class="path7"></span>
                            <span class="path8"></span>
                            <span class="path9"></span>
                            <span class="path10"></span>
                        </i>
                        <i class="ki-duotone ki-moon theme-dark-show fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </a>
                    <!--begin::Menu toggle-->
                    <!--begin::Menu-->
                    <div
                        class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-title-gray-700 menu-icon-gray-500 menu-active-bg menu-state-color fw-semibold py-4 fs-base w-150px"
                        data-kt-menu="true"
                        data-kt-element="theme-mode-menu"
                    >
                        <!--begin::Menu item-->
                        <div class="menu-item px-3 my-0">
                            <a
                                href="#"
                                class="menu-link px-3 py-2"
                                data-kt-element="mode"
                                data-kt-value="light"
                            >
                                <span class="menu-icon" data-kt-element="icon">
                                    <i class="ki-duotone ki-night-day fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                        <span class="path5"></span>
                                        <span class="path6"></span>
                                        <span class="path7"></span>
                                        <span class="path8"></span>
                                        <span class="path9"></span>
                                        <span class="path10"></span>
                                    </i>
                                </span>
                                <span class="menu-title">Light</span>
                            </a>
                        </div>
                        <!--end::Menu item-->
                        <!--begin::Menu item-->
                        <div class="menu-item px-3 my-0">
                            <a
                                href="#"
                                class="menu-link px-3 py-2"
                                data-kt-element="mode"
                                data-kt-value="dark"
                            >
                                <span class="menu-icon" data-kt-element="icon">
                                    <i class="ki-duotone ki-moon fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </span>
                                <span class="menu-title">Dark</span>
                            </a>
                        </div>
                        <!--end::Menu item-->
                        <!--begin::Menu item-->
                        <div class="menu-item px-3 my-0">
                            <a
                                href="#"
                                class="menu-link px-3 py-2"
                                data-kt-element="mode"
                                data-kt-value="system"
                            >
                                <span class="menu-icon" data-kt-element="icon">
                                    <i class="ki-duotone ki-screen fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                    </i>
                                </span>
                                <span class="menu-title">System</span>
                            </a>
                        </div>
                        <!--end::Menu item-->
                    </div>
                    <!--end::Menu-->
                </div>
                <!--end::Theme mode-->

                <!-- Language -->
                <div class="d-flex align-items-center ms-1" id="kt_header_user_menu_toggle" >
                    <!--begin::User info-->
                    <div
                        class="btn btn-flex align-items-center bg-hover-white bg-hover-opacity-10 py-2 px-2 px-md-3"
                        data-kt-menu-trigger="click"
                        data-kt-menu-attach="parent"
                        data-kt-menu-placement="bottom-end">
                     

                        <!--end::Name-->
                        <!--begin::Symbol-->
                        <div class="symbol symbol-30px symbol-md-40px" style="color:white;">
                            <img class="w-15px h-15px rounded-1 ms-2" :src="'/assets/media/flags/united-states.svg'" alt="" /> {{locale}}
                        </div>
                        <!--end::Symbol-->
                    </div>
                    <!--end::User info-->
                    <!--begin::User account menu-->
                    <div
                        class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-275px"
                        data-kt-menu="true"
                    >
              
                        <div class="menu-item px-5">
                            <a
                                href="#" @click="switchLocale(otherLocale)"
                                class="menu-link px-5 gap"
                                ><img class="w-15px h-15px rounded-1 ms-2 " :src="'/assets/media/flags/united-states.svg'" alt="" /> {{otherLocale}}</a
                            >
                        </div>
                   
                      
                        <div class="separator my-2"></div>


                        <div class="menu-item px-5">
                            <a
                                href="#" @click="switchLocale(otherLocale)"
                                class="menu-link px-5 gap"
                                ><img class="w-15px h-15px rounded-1 ms-2 " :src="'/assets/media/flags/saudi-arabia.svg'" alt="" /> {{otherLocale}}</a
                            >
                        </div>
                      
                     
                    </div>


                </div>


                <!--begin::User-->
                <div
                    class="d-flex align-items-center ms-1"
                    id="kt_header_user_menu_toggle"
                >
                    <!--begin::User info-->
                    <div
                        class="btn btn-flex align-items-center bg-hover-white bg-hover-opacity-10 py-2 px-2 px-md-3"
                        data-kt-menu-trigger="click"
                        data-kt-menu-attach="parent"
                        data-kt-menu-placement="bottom-end"
                    >
                        <!--begin::Name-->
                        <div
                            class="d-none d-md-flex flex-column align-items-end justify-content-center me-2 me-md-4"
                        >
                            <span
                                class=" fs-8 fw-semibold lh-1 mb-1"
                                >{{ user ? user.name : "Loading..." }}</span
                            >
                            <span class="text-white fs-8 fw-bold lh-1">{{
                                user ? user.email : "Loading..."
                            }}</span>
                        </div>

                        <!--end::Name-->
                        <!--begin::Symbol-->
                        <div class="symbol symbol-30px symbol-md-40px">
                            <img
                                :src="user ? user.logo : 'Loading...'"
                                alt="image"
                            />
                        </div>
                        <!--end::Symbol-->
                    </div>
                    <!--end::User info-->
                    <!--begin::User account menu-->
                    <div
                        class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-275px"
                        data-kt-menu="true"
                    >
                        <!--begin::Menu item-->
                        <div class="menu-item px-3">
                            <div
                                class="menu-content d-flex align-items-center px-3"
                            >
                                <!--begin::Avatar-->
                                <div class="symbol symbol-50px me-5">
                                    <img
                                        alt="Logo"
                                        :src="user ? user.logo : 'Loading...'"
                                    />
                                </div>
                                <!--end::Avatar-->
                                <!--begin::Username-->
                                <div class="d-flex flex-column">
                                    <div
                                        class="fw-bold d-flex align-items-center fs-5"
                                        v-if="user && user.company_id == null"
                                    >
                                        Pro
                                        <span
                                            class="badge badge-light-success fw-bold fs-8 px-2 py-1 ms-2"
                                            >{{
                                                user ? user.name : "Loading..."
                                            }}</span
                                        >
                                    </div>
                                    <a
                                        href="#"
                                        class="fw-semibold text-muted text-hover-primary fs-7"
                                        >{{
                                            user ? user.email : "Loading..."
                                        }}</a
                                    >
                                </div>
                                <!--end::Username-->
                            </div>
                        </div>
                        <!--end::Menu item-->
                        <!--begin::Menu separator-->
                        <div class="separator my-2"></div>
                        <!--end::Menu separator-->
                        <!--begin::Menu item-->
                        <div class="menu-item px-5">
                            <a
                                href="#"
                                class="menu-link px-5"
                                @click="openMyAccount(user.id)"
                                >My Profile</a
                            >
                        </div>
                   
                      
                        <div class="separator my-2"></div>
                      
                        <!--end::Menu item-->
                        <!--begin::Menu item-->
                        <div class="menu-item px-5">
                            <a @click="logout" href="#" class="menu-link px-5"
                                >Sign Out</a
                            >
                        </div>
                        <!--end::Menu item-->
                    </div>
                    <!--end::User account menu-->
                </div>




                 



                <!--end::User -->
            </div>
            <!--end::Topbar-->
        </div>
        <!--end::Container-->
    </div>
    
    <!-- Messages Modal -->
    <div
        v-if="showMessages"
        class="modal-backdrop-custom"
        @click.self="showMessages = false"
        style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 9999; display: flex; align-items: center; justify-content: center;"
    >
        <div
            class="modal-custom messages-modal bg-white rounded shadow-lg"
            style="max-width: 900px; width: 90%; max-height: 90vh; overflow: hidden; display: flex; flex-direction: column;"
        >
            <div class="p-4 border-bottom">
                <div class="d-flex align-items-center justify-content-between">
                    <h3 class="fw-bold mb-0">Messages</h3>
                    <button class="btn btn-sm btn-light" @click="showMessages = false">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <div class="p-4 border-bottom">
                <input
                    v-model="searchQuery"
                    type="text"
                    class="form-control"
                    placeholder="Search conversations..."
                    @input="handleSearch"
                />
            </div>
            
            <div class="flex-grow-1 overflow-auto p-4" style="min-height: 300px;">
                <div v-if="!currentChat">
                    <div v-if="filteredConversations.length === 0 && searchQuery" class="text-center text-muted p-4">
                        <i class="fas fa-search fa-2x mb-2"></i>
                        <p>No conversations found matching "{{ searchQuery }}"</p>
                    </div>
                    <div v-else-if="filteredConversations.length === 0" class="text-center text-muted p-4">
                        <i class="fas fa-comments fa-2x mb-2"></i>
                        <p>No conversations yet</p>
                    </div>
                    <div v-else>
                        <div
                            v-for="conv in filteredConversations"
                            :key="conv.id"
                            class="d-flex align-items-center gap-3 p-3 mb-2 rounded hover-bg-light cursor-pointer"
                            @click="openChat(conv)"
                            style="cursor: pointer; transition: background 0.2s;"
                            @mouseenter="$event.target.style.background = '#f5f5f5'"
                            @mouseleave="$event.target.style.background = ''"
                        >
                            <div style="position: relative">
                                <img
                                    :src="conv.avatar"
                                    class="rounded-circle"
                                    style="width: 56px; height: 56px; object-fit: cover;"
                                />
                                <span
                                    v-if="conv.unread"
                                    class="badge bg-warning text-dark position-absolute"
                                    style="top: -8px; left: -8px;"
                                >{{ conv.unread }}</span>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold d-flex justify-content-between">
                                    <span>{{ conv.name }}</span>
                                    <span class="text-muted small ms-2">{{ conv.time }}</span>
                                </div>
                                <div class="text-muted small">{{ conv.text }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div v-else>
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <button class="btn btn-sm btn-light" @click="closeChat()">
                            <i class="fas fa-arrow-left"></i>
                        </button>
                        <img
                            :src="currentChat?.avatar"
                            class="rounded-circle"
                            style="width: 48px; height: 48px; object-fit: cover;"
                        />
                        <span class="fw-bold fs-5">{{ currentChat?.name }}</span>
                    </div>
                    
                    <div
                        class="chat-messages border rounded p-3 mb-3"
                        style="min-height: 300px; max-height: 400px; overflow-y: auto;"
                    >
                        <div
                            v-for="(msg, idx) in messages"
                            :key="msg.id || idx"
                            :class="msg.from === 'me' ? 'text-end mb-3' : 'text-start mb-3'"
                        >
                            <div
                                :class="[
                                    'd-inline-block p-2 rounded',
                                    msg.from === 'me' ? 'bg-primary text-white' : 'bg-light text-dark',
                                ]"
                                style="max-width: 70%;"
                            >
                                <!-- عرض الصورة -->
                                <div v-if="msg.kind === 'image' && msg.mediaUrl" style="margin-bottom: 5px;">
                                    <img
                                        :src="msg.mediaUrl"
                                        style="max-width: 250px; max-height: 250px; border-radius: 8px; cursor: pointer; display: block;"
                                        @click="openImagePreview(msg.mediaUrl)"
                                        alt="Image"
                                    />
                                    <div v-if="msg.text && msg.text !== '📷 Image'" class="small mt-1">{{ msg.text }}</div>
                                </div>
                                <!-- عرض الصوت -->
                                <div v-else-if="msg.kind === 'audio'" class="audio-message" style="margin-bottom: 5px;">
                                    <div v-if="msg.mediaUrl">
                                        <audio
                                            :key="`audio-${msg.id}`"
                                            controls
                                            preload="metadata"
                                            style="max-width: 250px; min-width: 200px; display: block !important; width: 100%; outline: none; visibility: visible; opacity: 1;"
                                        >
                                            <source :src="msg.mediaUrl" type="audio/webm">
                                            <source :src="msg.mediaUrl" type="audio/mpeg">
                                            <source :src="msg.mediaUrl" type="audio/wav">
                                            <source :src="msg.mediaUrl" type="audio/ogg">
                                            Your browser does not support the audio element.
                                        </audio>
                                        <div v-if="msg.text && msg.text !== '🎤 Voice message'" class="small mt-1">{{ msg.text }}</div>
                                    </div>
                                    <div v-else class="text-warning small p-2 bg-warning bg-opacity-10 rounded">
                                        <i class="fas fa-exclamation-triangle"></i> Audio file not found
                                    </div>
                                </div>
                                <!-- عرض النص -->
                                <span v-else>{{ msg.text }}</span>
                            </div>
                            <div class="small text-muted mt-1" v-if="msg.time">
                                {{ msg.time }}
                            </div>
                        </div>
                    </div>
                    
                    <!-- معاينة الصورة -->
                    <div v-if="imagePreview && currentChat" class="mb-2 position-relative d-inline-block">
                        <img :src="imagePreview" style="max-width: 200px; max-height: 200px; border-radius: 8px; display: block;" />
                        <button
                            class="btn btn-sm btn-danger position-absolute"
                            @click="cancelImagePreview"
                            style="top: -10px; right: -10px; width: 30px; height: 30px; border-radius: 50%; padding: 0; display: flex; align-items: center; justify-content: center; z-index: 10;"
                            type="button"
                        >
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <div class="d-flex align-items-center gap-2">
                        <!-- زر الصورة -->
                        <button
                            class="btn btn-sm btn-light"
                            @click="triggerImageInput"
                            type="button"
                            title="Send Image"
                        >
                            <i class="fas fa-image"></i>
                        </button>
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
                            class="btn btn-sm btn-light"
                            @mousedown.prevent="handleStartRecording"
                            @touchstart.prevent="handleStartRecording"
                            @contextmenu.prevent
                            type="button"
                            title="Hold to record voice"
                        >
                            <i class="fas fa-microphone"></i>
                        </button>
                        <button
                            v-else
                            class="btn btn-sm btn-danger"
                            @click="handleStopRecording"
                            type="button"
                            title="Stop recording"
                        >
                            <i class="fas fa-stop"></i>
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
                            class="btn btn-primary"
                            @click="handleSendMessage"
                            :disabled="(!newMessage || newMessage.trim() === '') && !imagePreview"
                            type="button"
                        >
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                    
                    <!-- مؤشر التسجيل -->
                    <div v-if="isRecording" class="recording-indicator mt-2 p-2 bg-light rounded d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-circle fa-pulse text-danger me-2"></i>
                            <span class="text-danger fw-bold">Recording...</span>
                        </div>
                        <button
                            class="btn btn-danger btn-sm"
                            @click="handleStopRecording"
                            type="button"
                        >
                            <i class="fas fa-stop me-1"></i>
                            Stop & Send
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { mapActions, mapState } from "vuex";
import { onUnmounted, ref, nextTick, watch } from "vue";
import { api } from "../../../axios";
import { setLanguage } from "@/axios";
import { useChat } from '@/useChat';
 
export default {
    name: "Header",

    setup() {
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
        } = useChat();

        const showMessages = ref(false);
        const newMessage = ref('');
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
        const mediaStream = ref(null);
        const isRecordingStarted = ref(false);
        const recordingStartTime = ref(0);

        const handleSendMessage = async () => {
            if (!currentChat?.value) {
                alert('Please select a conversation');
                return;
            }
            
            const chatId = currentChat.value.chatId || currentChat.value.id;
            if (!chatId) {
                alert('Chat ID is missing');
                return;
            }
            
            let messageSent = false;

            // إذا كانت هناك صورة محددة، أرسلها أولاً
            if (selectedImageFile.value) {
                try {
                    const result = await sendImage(chatId, selectedImageFile.value);
                    if (result) {
                        cancelImagePreview();
                        messageSent = true;
                    } else {
                        alert('Failed to send image');
                    }
                } catch (error) {
                    console.error('Error sending image:', error);
                    alert('Error sending image: ' + error.message);
                }
            }

            // ثم أرسل الرسالة النصية إذا كانت موجودة
            if (newMessage.value && newMessage.value.trim()) {
                try {
                    const result = await sendMessage(chatId, newMessage.value);
                    if (result) {
            newMessage.value = '';
                        messageSent = true;
                    } else {
                        alert('Failed to send message');
                    }
                } catch (error) {
                    console.error('Error sending message:', error);
                    alert('Error sending message: ' + error.message);
                }
            }

            if (messageSent) {
            await nextTick();
                const chatBox = document.querySelector('.chat-messages');
                if (chatBox) {
                    chatBox.scrollTop = chatBox.scrollHeight;
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
            
            if (!file.type.startsWith('image/')) {
                alert('Please select an image file');
                return;
            }
            
            if (file.size > 10 * 1024 * 1024) {
                alert('Image size should be less than 10MB');
                return;
            }
            
            selectedImageFile.value = file;
            
            const reader = new FileReader();
            reader.onload = (e) => {
                imagePreview.value = e.target.result;
            };
            reader.readAsDataURL(file);
        };
        
        // إلغاء معاينة الصورة
        const cancelImagePreview = () => {
            imagePreview.value = null;
            selectedImageFile.value = null;
            if (imageInput.value) {
                imageInput.value.value = '';
            }
        };
        
        // بدء التسجيل الصوتي
        const handleStartRecording = async (event) => {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            if (!currentChat.value) {
                alert('Please select a conversation first');
                return;
            }
            
            if (isRecording.value) {
                return;
            }
            
            isRecording.value = true;
            recordingStartTime.value = Date.now();
            isRecordingStarted.value = false;

            try {
                await startRecording();
            } catch (error) {
                console.error('Error in handleStartRecording:', error);
                isRecording.value = false;
                isRecordingStarted.value = false;
            }
        };
        
        // إيقاف التسجيل الصوتي
        const handleStopRecording = (event) => {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            const minRecordingDuration = 100;
            const duration = Date.now() - recordingStartTime.value;

            if (!isRecording.value || !isRecordingStarted.value || duration < minRecordingDuration) {
                if (mediaStream.value) {
                    mediaStream.value.getTracks().forEach(track => track.stop());
                    mediaStream.value = null;
                }
                isRecording.value = false;
                isRecordingStarted.value = false;
                mediaRecorder.value = null;
                audioChunks.value = [];
                return;
            }
            
            try {
                stopRecording();
            } catch (error) {
                console.error('Error in handleStopRecording:', error);
            }
        };

        // بدء التسجيل الصوتي
        const startRecording = async () => {
            if (!currentChat.value) {
                isRecording.value = false;
                return;
            }
            
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                mediaStream.value = stream;
                
                let mimeType = 'audio/webm';
                if (!MediaRecorder.isTypeSupported('audio/webm')) {
                    mimeType = 'audio/mp4';
                    if (!MediaRecorder.isTypeSupported('audio/mp4')) {
                        mimeType = '';
                    }
                }
                
                const options = mimeType ? { mimeType } : {};
                const recorder = new MediaRecorder(stream, options);
                audioChunks.value = [];
                
                recorder.ondataavailable = (event) => {
                    if (event.data.size > 0) {
                        audioChunks.value.push(event.data);
                    }
                };
                
                recorder.onstop = async () => {
                    if (mediaStream.value) {
                        mediaStream.value.getTracks().forEach(track => track.stop());
                        mediaStream.value = null;
                    }

                    isRecording.value = false;
                    isRecordingStarted.value = false;
                    mediaRecorder.value = null;
                    recordingStartTime.value = 0;
                    
                    if (audioChunks.value.length === 0) {
                        audioChunks.value = [];
                        return;
                    }
                    
                    const audioBlob = new Blob(audioChunks.value, { type: mimeType || 'audio/webm' });
                    
                    if (audioBlob.size === 0) {
                        audioChunks.value = [];
                        return;
                    }
                    
                    const chatId = currentChat.value?.chatId || currentChat.value?.id;
                    if (chatId) {
                        const audioFile = new File([audioBlob], `voice_${Date.now()}.webm`, { type: mimeType || 'audio/webm' });
                        try {
                            const result = await sendAudio(chatId, audioFile);
                            if (!result) {
                                alert('Failed to send audio message');
                            }
                        } catch (error) {
                            console.error('Error sending audio:', error);
                            alert('Error sending audio: ' + error.message);
                        }
                    }
                    
                    audioChunks.value = [];
                };
                
                recorder.onerror = (error) => {
                    console.error('MediaRecorder error:', error);
                    isRecording.value = false;
                    isRecordingStarted.value = false;
                    if (mediaStream.value) {
                        mediaStream.value.getTracks().forEach(track => track.stop());
                        mediaStream.value = null;
                    }
                    alert('Error during recording: ' + error.message);
                };
                
                recorder.start(100);
                mediaRecorder.value = recorder;
                isRecordingStarted.value = true;
            } catch (error) {
                console.error('Error accessing microphone:', error);
                isRecording.value = false;
                isRecordingStarted.value = false;
                alert('Could not access microphone. Please check permissions.\n\nError: ' + error.message);
            }
        };
        
        // إيقاف التسجيل الصوتي
        const stopRecording = () => {
            if (mediaRecorder.value && isRecording.value) {
                try {
                    if (mediaRecorder.value.state === 'recording') {
                        mediaRecorder.value.stop();
                    } else if (mediaRecorder.value.state === 'paused') {
                        mediaRecorder.value.resume();
                        mediaRecorder.value.stop();
                    }
                } catch (error) {
                    console.error('Error stopping recorder:', error);
                    if (mediaStream.value) {
                        mediaStream.value.getTracks().forEach(track => track.stop());
                        mediaStream.value = null;
                    }
                    isRecording.value = false;
                    isRecordingStarted.value = false;
                    mediaRecorder.value = null;
                    audioChunks.value = [];
                    recordingStartTime.value = 0;
                }
            } else if (isRecording.value) {
                if (mediaStream.value) {
                    mediaStream.value.getTracks().forEach(track => track.stop());
                    mediaStream.value = null;
                }
                isRecording.value = false;
                isRecordingStarted.value = false;
                mediaRecorder.value = null;
                audioChunks.value = [];
                recordingStartTime.value = 0;
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

        onUnmounted(() => {
            localStorage.removeItem("profile");
        });

        return {
            conversations,
            currentChat,
            messages,
            unreadCount,
            openChat,
            closeChat,
            createConversation,
            showMessages,
            newMessage,
            handleSendMessage,
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
        };
    },

    data() {
        return {};
    },

    computed: {
        setting() {
            return this.$store.getters.getSetting;
        },

        user() {
            return this.$store.getters.getUser;
        },

        locale() {
            return this.$route.params.locale;
        },
        otherLocale() {
            return this.locale === "en" ? "ar" : "en";
        },

        openChatWithUser() {
            return this.$store.state.openChatWithUser;
        },
        
        // للتأكد من أن unreadCount reactive
        unreadMessagesCount() {
            return this.unreadCount || 0;
        },
    },

    mounted() {
        this.$store.dispatch("fetchSetting");
        this.$store.dispatch("fetchUser");
    },

    watch: {
        showMessages(newVal) {
            if (newVal) {
                console.log('Messages modal opened, fetching conversations...');
                this.fetchConversations();
            }
        },
        
        // مراقبة openChatWithUser من Vuex store
        openChatWithUser: {
            handler(newUser) {
                if (newUser && newUser.id) {
                    console.log('🔄 Opening chat with user:', newUser);
                    this.openChatWithUserMethod(newUser);
                    // مسح الحالة بعد الاستخدام
                    this.$store.commit('setOpenChatWithUser', null);
                }
            },
            immediate: false
        }
    },
 
    methods: {
        openMessagesModal() {
            this.showMessages = true;
            this.$nextTick(() => {
                if (this.fetchConversations && typeof this.fetchConversations === 'function') {
                    this.fetchConversations();
                }
            });
        },
        
        async openChatWithUserMethod(userData) {
            console.log('📩 openChatWithUser called with:', userData);
            
            // فتح نافذة الرسائل
            this.showMessages = true;
            
            // انتظر قليلاً ثم جلب المحادثات
            await this.$nextTick();
            if (this.fetchConversations && typeof this.fetchConversations === 'function') {
                this.fetchConversations();
            }
            
            // انتظر حتى يتم جلب المحادثات
            await new Promise(resolve => setTimeout(resolve, 500));
            
            // البحث عن محادثة موجودة
            const existingConv = this.conversations.find(conv => 
                conv.otherId === `user_${userData.id}` || 
                conv.otherId === userData.id.toString() ||
                (conv.name === userData.name && conv.otherId && conv.otherId.toString().includes(userData.id.toString()))
            );
            
            if (existingConv) {
                console.log('✅ Found existing conversation:', existingConv);
                this.openChat(existingConv);
            } else {
                // إنشاء محادثة جديدة
                console.log('📝 Creating new conversation with user:', userData);
                try {
                    const chatId = await this.createConversation(
                        userData.id,
                        userData.name,
                        userData.avatar || '/default-avatar.png'
                    );
                    
                    if (chatId) {
                        console.log('✅ Conversation created with ID:', chatId);
                        // انتظر قليلاً ثم جلب المحادثات مرة أخرى
                        await new Promise(resolve => setTimeout(resolve, 500));
                        if (this.fetchConversations && typeof this.fetchConversations === 'function') {
                            this.fetchConversations();
                        }
                        
                        // انتظر ثم افتح المحادثة
                        await new Promise(resolve => setTimeout(resolve, 500));
                        const newConv = this.conversations.find(conv => 
                            conv.chatId === chatId || conv.id === chatId
                        );
                        
                        if (newConv) {
                            console.log('✅ Opening new conversation:', newConv);
                            this.openChat(newConv);
                        } else {
                            console.warn('⚠️ Conversation created but not found in list');
                        }
                    } else {
                        console.error('❌ Failed to create conversation');
                        alert('Failed to create conversation. Please try again.');
                    }
                } catch (error) {
                    console.error('❌ Error creating conversation:', error);
                    alert('Error creating conversation: ' + error.message);
                }
            }
        },
        


         async logout() {
            this.isLoading = true;
            try {
                await axios.post("AdminAuth/logOut");
            } catch (error) {
                console.log("Logout failed:", error);
            } finally {
                // localStorage.clear();
                // this.$router.push({ path: `/${this.locale}` });
                localStorage.removeItem("authToken");
                let url = window.location.origin;
                window.open(url + "/" + this.locale + "/admin/login", "_self");
                this.isLoading = false;
            }
        },


        openMyAccount(ID) {
            if (this.$route.name == "admins") {
                localStorage.setItem("profile", ID);
                window.location.reload();
            }
            let url = window.location.origin;
            localStorage.setItem("profile", ID);
            this.$router.push(
                "/" + this.locale + "/dashboard/user_management/admins"
            );
        },



        // async switchLocale(otherLocale) {
        //     // const flash = document.createElement("div");
        //     // flash.classList.add("language-switch-flash");
        //     // document.body.appendChild(flash);

        //     // setTimeout(() => {
        //     //     flash.remove();
        //     // }, 400);

        // setLanguage(otherLocale);

        //     let currentPath = this.$route.path.replace(/^\/(en|ar)/, "");
        //     if (currentPath === "") {
        //         currentPath = "/";
        //     } 

        //     this.locale = otherLocale;
        //     localStorage.setItem("locale", this.locale);

        //     await this.$store.dispatch("loadTranslations", otherLocale);

        //     document.dir = otherLocale === "ar" ? "rtl" : "ltr";
        //     document.body.classList.toggle("rtl", otherLocale === "ar");

        //     const rtlHref = "/assets/css/style.bundle.rtl.css";
        //     const ltrHref = "/assets/css/style.bundle.css";

        //     const existingLink = document.getElementById("main-style");
        //     if (existingLink) {
        //         // existingLink.href = otherLocale === 'ar' ? rtlHref : ltrHref;
        //         const version = new Date().getTime(); // لتغيير الرابط كل مرة
        //         existingLink.href =
        //             (otherLocale === "ar" ? rtlHref : ltrHref) +
        //             `?v=${version}`;
        //     } else {
        //         const link = document.createElement("link");
        //         link.id = "main-style";
        //         link.rel = "stylesheet";

        //         const version = new Date().getTime(); // لتغيير الرابط كل مرة
        //         link.href =
        //             (otherLocale === "ar" ? rtlHref : ltrHref) +
        //             `?v=${version}`;

        //         link.onload = () => {
        //             console.log("RTL CSS Loaded!");
        //         };
        //         document.head.appendChild(link);
        //     }

        //     let updatedPath = `/${otherLocale}${currentPath}`;
        //     if (updatedPath.slice(-1) === "/") {
        //         updatedPath = updatedPath.slice(0, -1);
        //     }
        //     this.$router.push({
        //       path: updatedPath,
        //       query: this.$route.query
        //     });
        // },




        switchLocale(otherLocale) {
            localStorage.setItem("locale", otherLocale);

            const currentPath = window.location.pathname.replace(/^\/(en|ar)/, "");
            const newPath = `/${otherLocale}${currentPath}`;
            const newUrl = `${newPath}${window.location.search}`;
                window.location.href = newUrl;

        },


        
    },
};
</script>
<style type="text/css" scoped>
    
    #kt_header{
        background: rgb(214, 162, 48) !important;
    }
    .gap{
        gap: 10px !important;
    }
    
    .audio-message audio {
        min-width: 200px;
        width: 100%;
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }
    
    .hover-bg-light:hover {
        background-color: #f5f5f5 !important;
    }
    
    .cursor-pointer {
        cursor: pointer;
    }
</style>