<template>
    <header class="container-fluid p-0 d-none d-lg-block">
        <div class="background-top-header">
            <div class="container">
                <div class="header__top d-flex align-items-center justify-content-between">
                    <div class="partner-center d-flex align-items-center me-16">
                        <div class="partner-center__seller me-16">
                            <router-link to="/kenh-nguoi-ban/dang-ky"><a href="" class="text-white d-flex"><i
                                        class="fas fa-user-tie fs-20 me-10"></i>
                                    <span class="fs-14">Kênh người bán</span>
                                </a></router-link>
                        </div>

                        <!-- <div class="partner-center__take-care me-16">
                            <a href="" class="text-white d-flex"><i class="fas fa-headset fs-20 me-10"></i>
                                <span class="fs-14">Chăm sóc khách hàng</span>
                            </a>
                        </div>

                        <div class="partner-center__supplier">
                            <a href="" class="text-white d-flex"><i class="fas fa-boxes-stacked fs-20 me-10"></i>
                                <span class="fs-14">Nhà cung cấp</span>
                            </a>
                        </div> -->
                    </div>

                    <div class="user-actions d-flex align-items-center">
                        <div class="user-actions__notifications me-16 position-relative">
                            <a href="" class="text-white d-flex"
                                @click.prevent="show_notifications = !show_notifications">
                                <div class="position-relative me-10">
                                    <i class="fa-solid fa-bell fas fa-user-tie fs-20"></i>
                                    <span class="notification-badge" v-if="unread_count > 0">{{ unread_count }}</span>
                                </div>
                                <span class="fs-14">Thông báo</span>
                            </a>

                            <!-- Notifications Dropdown -->
                            <div class="notifications__dropdown position-absolute bg-white shadow-lg"
                                v-show="show_notifications">
                                <div class="notifications__header px-3 py-2 border-bottom">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0 fw-semibold">Thông báo</h6>
                                        <span class="fs-12 text-muted">{{ notifications.length }} thông báo</span>
                                    </div>
                                </div>

                                <div class="notifications__content" style="max-height: 400px; overflow-y: auto;">
                                    <!-- Notification Items -->
                                    <div class="notification__item px-3 py-2 border-bottom"
                                        v-for="notification in notifications" :key="notification.id"
                                        :class="{ 'unread': !notification.is_read }" @click="markAsRead(notification)">
                                        <a :href="`${notification.link ? notification.link : '#'}`"
                                            class="d-flex align-items-start">
                                            <div class="notification__icon me-2">
                                                <i class="fa-solid fa-circle-info text-danger fs-16"></i>
                                            </div>
                                            <div class="notification__content flex-grow-1">
                                                <h6 class="notification__title fs-14 fw-semibold mb-1">{{
                                                    notification.title }}</h6>
                                                <p class="notification__message fs-13 text-muted mb-1">{{
                                                    notification.message }}</p>
                                                <small class="notification__time text-muted fs-12">{{
                                                    formatNotificationTime(notification.created_at) }}</small>
                                            </div>
                                            <div class="notification__status">
                                                <div class="unread-dot" v-if="!notification.is_read"></div>
                                            </div>
                                        </a>
                                    </div>

                                    <!-- Empty State -->
                                    <div class="text-center py-4" v-if="notifications.length === 0">
                                        <i class="fa-solid fa-bell-slash fs-48 text-muted mb-2"></i>
                                        <p class="text-muted fs-14">Không có thông báo nào</p>
                                    </div>
                                </div>

                                <div class="notifications__footer px-3 py-2 border-top text-center"
                                    v-if="notifications.length > 0">
                                    <a href="#" class="text-danger fs-13" @click="markAllAsRead">Đánh dấu tất cả đã
                                        đọc</a>
                                </div>
                            </div>
                        </div>

                        <!-- <div class="user-actions__support me-16">
                            <a href="" class="text-white d-flex">
                                <i class="fa-solid fa-circle-question fs-20 me-10"></i>
                                <span class="fs-14">Hỗ trợ</span>
                            </a>
                        </div> -->

                        <div class="user-actions__auth position-relative" v-if="user_name">
                            <div class="auth__profile text-white d-flex">
                                <div class="auth__user--avatar me-10">
                                    <img :src="avatar ? `/imgs/users/${avatar}` : '/imgs/user-default.jpg'" alt="" />
                                </div>

                                <span class="auth__user--name fs-14 text-white">{{ user_name }}
                                </span>
                            </div>

                            <div class="auth__menu position-absolute">
                                <router-link :to="{ name: 'user-profile' }"
                                    class="menu__item fs-15 fw-medium mb-10 d-block">Tài khoản của tôi</router-link>
                                <router-link :to="{ name: 'order-history' }"
                                    class="menu__item fs-15 fw-medium mb-10 d-block">Đơn mua</router-link>
                                <div v-if="role && role.id" @click="goToAdmin"
                                    class="menu__item fs-15 fw-medium mb-10 d-block">Trang quản trị</div>
                                <span class="menu__item fs-15 fw-medium d-block" @click="logout">Đăng xuất</span>
                            </div>
                        </div>

                        <div class="user-actions__auth" v-else>
                            <RouterLink to="/dang-nhap" class="auth__login fs-14 text-white">Đăng nhập</RouterLink>
                            <span class="text-white"> | </span>
                            <RouterLink to="/dang-ky" class="auth__register fs-14 text-white">Đăng ký</RouterLink>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="background-bottom-header">
            <div class="container">
                <div class="header__bottom d-flex justify-content-between align-items-center">
                    <router-link to="/" class="logo">
                        <img :src="`/imgs/${logo}`" alt="" />
                    </router-link>
                    <form @submit.prevent="handleTextSearch" class="header__bottom--search">
                        <input type="text" v-model="keywordInput" id="search" placeholder="TÌM KIẾM..."
                            class="fw-medium" />
                        <!-- <span class="fs-22 text-grey me-2 cursor-pointer position-absolute" @click="triggerFileInput"
                            style="right: 70px;">
                            <i class="ri-camera-line"></i>
                            <input hidden ref="fileInput" type="file" accept="image/*" @change="handleFileChange" />
                        </span> -->
                        <button type="submit" class="position-absolute">
                            <i class="material-icons">search</i>
                        </button>
                    </form>

                    <div class="header__bottom--cart px-4 py-3">
                        <router-link to="/gio-hang" class="cart__icon">
                            <i class="fa-solid fa-cart-shopping fs-24 text-white"></i>
                        </router-link>

                        <div v-if="carts.length > 0" class="cart__menu pe-10 ps-10">
                            <span class="fs-14 fw-medium">Sản phẩm mới thêm</span>

                            <div class="cart__menu--product">
                                <div v-for="c in carts"
                                    class="product__item mx-1 mt-16 mb-16 d-flex justify-content-between">
                                    <div class="product__img--name d-flex">
                                        <div class="img me-10">
                                            <img :src="`/imgs/products/${c.image}`" :alt="c.name" />
                                        </div>
                                        <div class="name fs-16 fw-semibold">{{ c.name }}</div>
                                    </div>

                                    <div class="price fs-14">{{ Number(c.price).toLocaleString('vi-VN') + ' ₫' }}
                                    </div>
                                </div>
                            </div>
                            <router-link :to="{
                                name: 'cart'
                            }" class="justify-content-end d-flex">
                                <button class="cart__menu--watch main-btn px-3 py-2 fs-12 mt-2 mb-10">
                                    Xem giỏ hàng
                                </button>
                            </router-link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <div class="header-mobile-container container p-0">
        <div class="header-mobile d-flex d-lg-none align-items-center justify-content-between gap-2 pe-16 ps-16">
            <div class="header-mobile__search col-10">
                <form @submit.prevent="handleTextSearch">
                    <input type="text" v-model="keywordInput" id="search_mobile" class="flex-grow-1"
                        placeholder="TÌM KIẾM" />
                    <!-- <span class="fs-22 text-grey me-2 cursor-pointer" @click="triggerFileInput">
                        <i class="ri-camera-line"></i>
                        <input hidden ref="fileInput" type="file" accept="image/*" @change="handleFileChange" />
                    </span> -->
                    <button type="submit">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </form>
            </div>

            <div class="header-mobile__cart col-1 me-2">
                <a href="#" @click="handleMobileNavigation('cart')">
                    <i class="fa-solid fa-cart-shopping fs-24 text-white"></i>
                </a>
            </div>
            <!-- <div class="header-mobile__chat col-1">
                <a href=""><i class="fa-solid fa-comment fs-24 text-white"></i></a>
            </div> -->
        </div>
    </div>
    <!-- End Header -->

    <div class="menu-mobile-container container position-fixed bottom-0 d-flex d-lg-none start-0 end-0 p-0"
        v-show="show_menu">
        <div class="menu-mobile justify-content-between bg-white d-flex align-items-center w-100 ps-16 pe-16 shadow">
            <div class="menu-mobile__item">
                <router-link :to="{
                    name: 'home'
                }" class="text-decoration-none d-flex flex-column align-items-center active">
                    <i class="fa-solid fa-house fs-20"></i>
                    <span class="fs-12 mt-1">Trang chủ</span>
                </router-link>
            </div>

            <div class="menu-mobile__item ">
                <router-link to="/kenh-nguoi-ban/dang-ky"
                    class="text-decoration-none d-flex flex-column align-items-center">
                    <i class="fa-solid fa-user-tie fs-20"></i>
                    <span class="fs-12 mt-1">Kênh bán</span>
                </router-link>
            </div>

            <div class="menu-mobile__item ">
                <a href="#" class="text-decoration-none d-flex flex-column align-items-center"
                    @click="handleMobileNavigation('notification')">
                    <i class="fa-solid fa-bell fs-20"></i>
                    <span class="fs-12 mt-1">Thông báo</span>
                </a>
            </div>

            <div class="menu-mobile__item ">
                <a href="#" class="text-decoration-none d-flex flex-column align-items-center"
                    @click="handleMobileNavigation('profile')">
                    <i class="fa-solid fa-user fs-20"></i>
                    <span class="fs-12 mt-1">Tài khoản</span>
                </a>
            </div>
        </div>
    </div>
    <!-- Menu-mobile  -->

    <!-- Chat  -->
    <div class="chat-icon position-fixed text-white rounded-circle d-flex justify-content-center align-items-center shadow bg-main"
        style="width: 56px; height: 56px; z-index: 99999; cursor: pointer; bottom: 90px; right: 16px;"
        @click="get_conversations();">
        <i class="fa-solid fa-comments fs-20"></i>
    </div>

    <Transition name="slide-in-left">
        <div class="sidebar__modal __chat position-fixed bg-white shadow-sm user-select-none" v-if="show_sidebar_chat">
            <div class="__title w-100 border-bottom px-3 py-2 d-flex justify-content-between align-items-center">
                <div class="__title-text fs-5 main-color fw-semibold fs-18">Chat</div>
                <div class="__task d-flex align-items-center fs-14">
                    <div class="__item d-none d-md-block"><i class="fa-solid fa-rotate text-grey cursor-pointer"></i>
                    </div>
                    <div class="__seperation mx-2 d-none d-md-block" style="color: rgb(228, 228, 228);">|</div>
                    <div class="__item">
                        <button class="btn-close-chat d-md-none" @click="show_sidebar_chat = false">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    <div @click="show_sidebar_chat = false" class="__item d-none d-md-block"><i
                            class="fa-solid fa-angles-right text-grey cursor-pointer"></i></div>
                </div>
            </div>

            <div class="__content w-100 overflow-y-auto">
                <div class="__chat">
                    <div class="__search-box ms-3 my-2">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" name="" id="" placeholder="Tìm theo tên khách hàng" class="fs-14">
                    </div>

                    <div class="__item d-flex align-items-center gap-2 px-3 py-2 bg-grey"
                        v-for="conversation in conversations" :key="conversation.id"
                        @click="open_conversation(conversation.id);">
                        <div class="__avatar" style="width: 40px; height: 40px;">
                            <img src="/imgs/user-default.jpg" alt=""
                                style="width: 100%; height: 100%; border-radius: 50%; object-fit: contain;">
                        </div>

                        <div class="__info w-100">
                            <div class="__top d-flex justify-content-between align-items-center">
                                <div class="__name fs-14 fw-semibold">{{ conversation.shop_name ? conversation.shop_name
                                    : conversation.user_name }}</div>
                                <div class="__time fs-12"
                                    :class="{ 'fw-semibold': !conversation.is_read, 'text-grey': conversation.is_read }">
                                    {{ conversation.last_message_time }}</div>
                            </div>
                            <!-- <div class="__message fs-13 text-grey ">
                                {{ conversation.last_message }}</div> -->
                            <div class="__message fs-13 mobile-text-truncate"
                                :class="{ 'fw-semibold': !conversation.is_read, 'text-grey': conversation.is_read }"
                                style="width: 35ch; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                {{ conversation.last_message }}</div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </Transition>

    <Transition name="slide-in-left">
        <div class="__conversation shadow-sm position-fixed d-flex flex-column" v-if="show_conversation"
            @click.self="close_conversation">
            <div
                class="__head w-100 d-flex justify-content-between align-items-center px-3 py-2 border-bottom bg-white">
                <div class="__info d-flex align-items-center gap-2">
                    <div class="__avatar" style="width: 30px; height: 30px;">
                        <img src="/imgs/user-default.jpg" alt=""
                            style="width: 100%; height: 100%; border-radius: 50%; object-fit: contain;">
                    </div>
                    <div class="__name fs-16 fw-semibold">{{ receiver?.shop_name || receiver?.user_name }}</div>
                </div>

                <div class="__task">
                    <button class="btn-close-chat" @click="close_conversation">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>

            <div class="__commune w-100 position-relative d-flex flex-column px-2 px-md-3 py-2 chat-messages-container">
                <div class="__bubble mb-2 d-flex flex-column shadow-sm" v-for="msg in messages" :key="msg.id"
                    :class="msg.is_self ? '__self' : '__other', msg.is_self ? 'align-self-end' : 'align-self-start'">
                    <div class="__text" v-if="msg.type == 'text'">{{ msg.content }}</div>

                    <div class="__product px-2 py-1 mb-2 d-flex flex-column gap-2" v-else-if="msg.type == 'product'">
                        <div class="__title border-bottom fw-500 text-grey pb-2 fs-13">Bạn đang trao đổi về sản phẩm này
                        </div>

                        <div class="__content d-flex gap-2">
                            <div class="__img mobile-product-img">
                                <img :src="`/imgs/products/${msg.content.image}`" alt="">
                            </div>

                            <div class="__info fw-semibold d-flex flex-column gap-1">
                                <div class="__name fs-13 mobile-product-name">{{ msg.content.name }}</div>
                                <div class="__price d-flex gap-2 fs-12 align-items-center">
                                    <div class="__min-price text-grey" style="text-decoration: 2px line-through gray;">
                                        {{ msg.content.minPrice.toLocaleString('vi-VN') }}đ</div>
                                    <div class="__max-price main-color ">{{ msg.content.maxPrice.toLocaleString('vi-VN')
                                        }}đ</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="__bottom bg-white d-flex flex-column flex-grow-1">
                <div class="__sender flex-grow-1 d-flex align-items-center px-2 px-md-3 py-2">
                    <textarea class="form-control flex-grow-1 me-2 fs-14" rows="1" placeholder="Nhập nội dung tin nhắn"
                        style="resize: none; border: none; outline: none; box-shadow: none;"
                        v-model="new_message"></textarea>
                    <button class="btn btn-primary d-flex align-items-center px-2 px-md-3" @click="send_message"
                        :disabled="!new_message.trim()">
                        <i class="fa-solid fa-paper-plane fs-14"></i>
                    </button>
                </div>


            </div>
        </div>
    </Transition>
    <div v-if="loading_search"
        class="modal-background container-fluid p-0 d-flex justify-content-center align-items-center">
        <div class="modal-ezeshop bg-white d-flex align-items-center p-5 flex-column gap-3">
            <p class="fw-medium">Đang tìm kiếm sản phẩm...</p>
            <loading__loader_circle border="4px" size="30px" />
        </div>
    </div>
    <!-- Chat  -->
</template>

<script setup>
import api from '@/configs/api'
import message from "@/utils/messageState"
import { user_name_auth, avatar_user, user_role, clear_user } from '@/utils/authState'
import { useConfig } from '@/composables/useConfig'

import { onMounted, onUnmounted, ref, watch, inject, reactive, nextTick } from 'vue'
import { RouterLink } from 'vue-router'
import loading__loader_circle from '@/components/loading/loading__loader-circle.vue'
import { useRouter, useRoute } from 'vue-router'
import { formatNotificationTime } from '@/utils/formatNotiTime.js'
import { usePermissions } from '@/composables/usePermissions'
import { clearPermissionsCache } from '@/utils/permissionUtils'
const { hasPermission } = usePermissions()

const router = useRouter()
const route = useRoute()
const user_name = user_name_auth
const avatar = avatar_user
const role = user_role

const { logoHeader, initConfig } = useConfig()
const logo = logoHeader

const logout = async () => {
    try {
        const res = await api.post('/logout');
        clear_user()
        clearPermissionsCache();
        message.emit("show-message", { title: "Thông báo", text: res.data.message, type: "success" })
        router.push("/")
    } catch (error) {
        console.error(error);
        message.emit("show-message", { title: "Lỗi", text: "Đăng xuất thất bại.", type: "error" })
    } finally {
        carts.value = []
    }
}

const getUserRole = async () => {
    try {
        const res = await api.get('/role/user')
        if (res.data.role) {
            user_role.value = res.data.role
        }
    } catch (error) {
        console.log('Không thể lấy thông tin role:', error)
        user_role.value = null
    }
}

const carts = ref([])
const getCart = async () => {
    try {
        const res = await api.get('/carts/user-header')
        if (res.status == 200) {
            carts.value = res.data.carts
        }
    } catch (error) {
        console.log(error);
    }
}
const cartUpdated = inject('cartUpdated')

const fileInput = ref(null)

/**
    Scroll mobile menu
 */
const show_menu = ref(true)
let last_scroll_top = 0

const handle_scroll = () => {
    const current_scroll_top = window.pageXOffset || document.documentElement.scrollTop

    if (current_scroll_top > last_scroll_top && current_scroll_top > 10) {
        // Lướt xuống
        show_menu.value = false
    } else {
        // Lướt lên
        show_menu.value = true
    }

    last_scroll_top = current_scroll_top <= 0 ? 0 : current_scroll_top
}

/**
    chat
 */

//variables
const chat_product = inject('chat_product')
const show_sidebar_chat = ref(false)
const show_conversation = ref(false)

const conversations = ref([])
const conversation_id = ref(null)
const messages = ref([])
const new_message = ref('')
const messageContainer = ref(null)
const receiver_id = ref(null)
const receiver_type = ref('')
const receiver = ref(null);

const sent_product = reactive({})

const conversation_channels = {}

//methods
const open_conversation = (id) => {
    conversation_id.value = id
    show_sidebar_chat.value = true
    show_conversation.value = true
    get_messages(id)
    mark_conversation_as_read(id)

    const convo = conversations.value.find(c => c.id === id)
    if (convo) {
        record_receiver(convo.receiver_id, convo.receiver_type)
    }
}

const close_conversation = () => {
    show_conversation.value = false
    conversation_id.value = null
}

const formatTime = (iso) => {
    const d = new Date(iso);
    return d.getHours().toString().padStart(2, '0') + ':' +
        d.getMinutes().toString().padStart(2, '0');
};

const get_conversations = async () => {
    const token = localStorage.getItem('token');
    if (!token) {
        message.emit("show-message", { title: "Thông báo", text: "Vui lòng đăng nhập để sử dụng tính năng chat.", type: "warning" })
        return
    }
    show_sidebar_chat.value = !show_sidebar_chat.value
    try {
        const res = await api.get('/conversation')
        conversations.value = res.data.conversations.map(c => {

            if (c.last_message_type === 'product') {
                c.last_message = '[Sản phẩm]'
            }
            return c
        })

        listen_all_conversations()
    } catch (err) {
        console.error('Lỗi lấy danh sách cuộc trò chuyện:', err)
    }
}

const get_messages = async (conversationId) => {
    try {
        const response = await api.get(`/conversation/${conversationId}/messages`)

        // messages.value = response.data.messages
        receiver.value = response.data.receiver;

        messages.value = response.data.messages.map(msg => {
            if (msg.type === 'product') {
                try {
                    msg.content = JSON.parse(msg.content);
                } catch (error) {
                    console.error('Không parse được product:', msg.content, e);
                }
            }

            return { ...msg, created_at: msg.created_at }
        })
        // console.log('Messages:', messages.value);
    } catch (error) {
        console.error('Error fetching messages:', error)
    }
}

const mark_conversation_as_read = async (conversationId) => {
    try {
        const res = await api.post(`/conversation/${conversationId}/mark-as-read`)
        console.log(res.data)
        const convo = conversations.value.find(c => c.id === conversationId)
        if (convo) {
            convo.is_read = true
        }
    } catch (error) {
        console.error('Lỗi đánh dấu cuộc trò chuyện đã đọc:', error)
    }
}

const record_receiver = (receiverID, receiverType) => {
    receiver_id.value = receiverID
    receiver_type.value = receiverType
}

const send_message = async () => {
    if (!new_message.value.trim()) return;

    // Lấy socket id đang kết nối Pusher
    const socketId = window.Echo.connector.pusher.connection.socket_id;

    try {
        const res = await api.post('/chat', {
            conversation_id: conversation_id.value,
            message: new_message.value,
            receiver_id: receiver_id.value,
            receiver_type: receiver_type.value,
        }, {
            headers: { 'X-Socket-Id': socketId }
        });

        // Push message lên UI ngay lập tức
        const msg = {
            ...res.data.message,
            is_self: true,
        };

        messages.value.push({
            ...msg,
            created_at: msg.created_at,  // giữ ISO để format bên dưới
        });

        scrollToBottom()

        // Cập nhật preview list
        const convo = conversations.value.find(c => c.id === conversation_id.value);
        if (convo) {
            convo.last_message = msg.content;
            convo.last_message_time = formatTime(msg.created_at);
        }

        new_message.value = '';

    } catch (error) {
        console.error('Error sending message:', error);
    }
};

const ensure_conversation = async (receiver_id, receiver_type) => {
    const res = await api.post('/chat', {
        receiver_id,
        receiver_type,
        message: '',
        type: 'text'
    });
    return res.data.conversation_id;
};

const send_product_message = async () => {
    const product = chat_product.product_to_send;

    let convo = conversations.value.find(
        c => c.receiver_id === chat_product.receiver_id
    );
    let convo_id = null;

    if (!convo) {
        convo_id = await ensure_conversation(chat_product.receiver_id, chat_product.receiver_type);
        await get_conversations();
        convo = conversations.value.find(c => c.id === convo_id);
    } else {
        convo_id = convo.id;
    }

    show_sidebar_chat.value = true;
    open_conversation(convo_id);

    const message = JSON.stringify({
        name: product.name,
        image: product.image,
        minPrice: product.minPrice,
        maxPrice: product.maxPrice,
        link: '/san-pham/' + product.slug,
    });

    if (!message || !message.trim()) return;

    const socketId = window.Echo.connector.pusher.connection.socket_id;

    try {
        const res = await api.post('/chat', {
            conversation_id: convo_id,
            message,
            type: 'product',
            receiver_id: chat_product.receiver_id,
            receiver_type: chat_product.receiver_type,
        }, {
            headers: {
                'X-Socket-Id': socketId,
            },
        });

        const msg = {
            ...res.data.message,
            is_self: true,
        };

        if (msg.type === 'product') {
            try {
                msg.content = JSON.parse(msg.content);
            } catch (e) {
                console.error('Không parse được product:', msg.content, e);
            }
        }

        messages.value.push({
            ...msg,
            created_at: msg.created_at,
        });

        scrollToBottom();

        const convo = conversations.value.find(c => c.id === convo_id);
        if (convo) {
            convo.last_message = msg.type == 'product' ? '[Sản phẩm]' : msg.content;
            convo.last_message_time = formatTime(msg.created_at);
        }

    } catch (error) {
        console.error('Lỗi khi gửi tin nhắn:', error);
    }
}

const listen_all_conversations = () => {
    conversations.value.forEach(convo => {
        if (conversation_channels[convo.id]) return;

        const channel = window.Echo.private(`chat.${convo.id}`)
            .listen('.MessageSent', ({ message }) => {
                if (conversation_id.value !== convo.id) return;

                // luôn false vì broadcast dùng toOthers()
                messages.value.push({
                    ...message,
                    is_self: false,
                    content: message.type === 'product' ? JSON.parse(message.content) : message.content,
                    created_at: message.created_at,
                });

                scrollToBottom()

                // cập nhật preview
                const target = conversations.value.find(c => c.id === convo.id);
                if (target) {
                    // Nếu là sản phẩm thì luôn hiển thị [Sản phẩm]
                    if (message.type === 'product') {
                        target.last_message = '[Sản phẩm]';
                    } else {
                        // Nếu là text thì hiển thị nội dung
                        target.last_message = message.content;
                    }
                    target.last_message_time = formatTime(message.created_at);
                }

            });

        conversation_channels[convo.id] = channel;
    });
};

const open_chat_for_user = async (receiver_id, receiver_type) => {
    let convo = conversations.value.find(c => c.receiver_id === receiver_id)
    let convo_id = null

    if (!convo) {
        convo_id = await ensure_conversation(receiver_id, receiver_type)
        await get_conversations()
        convo = conversations.value.find(c => c.id === convo_id)
    } else {
        convo_id = convo.id
    }

    show_sidebar_chat.value = true
    open_conversation(convo_id)
}

watch(() => chat_product.receiver_id, async (newVal) => {
    if (newVal) {
        // mở chat khi có người nhận
        const convo_id = await open_chat_for_user(
            chat_product.receiver_id,
            chat_product.receiver_type
        )

        // nếu có sản phẩm thì gửi sau khi chat mở
        if (chat_product.product_to_send) {
            send_product_message(chat_product.product_to_send, convo_id)
            chat_product.product_to_send = null
        }

        chat_product.receiver_id = null
        chat_product.receiver_type = null
    }
})



const scrollToBottom = () => {
    nextTick(() => {
        if (messageContainer.value) {
            messageContainer.value.scrollTop = messageContainer.value.scrollHeight
        }
    })
}

// Notifications
const show_notifications = ref(false)
const notifications = ref([])
const unread_count = ref(0)

const getNotification = async () => {
    try {
        const res = await api.get('/notifications/user')
        notifications.value = res.data.notifications
        unread_count.value = res.data.unread_count
    } catch (error) {
        console.error('Lỗi khi lấy thông báo:', error)
    }
}

// Notifications methods
const markAsRead = async (notification) => {
    try {
        await api.post(`/notifications/read/${notification.id}`)
        getNotification()
        show_notifications.value = false
        // router.push(notification.link);
    } catch (error) {
        console.error('Lỗi khi đánh dấu thông báo là đã đọc:', error)
    }
}

const markAllAsRead = async () => {
    try {
        await api.post('/notifications/read-all', {
            send_type: 'to_user'
        })
        getNotification()
        // show_notifications.value = false
    } catch (error) {
        console.error('Lỗi khi đánh dấu tất cả thông báo là đã đọc:', error)
    }
}

watch(messages, async () => {
    await nextTick()
    if (messageContainer.value) {
        messageContainer.value.scrollTop = messageContainer.value.scrollHeight
    }
})

/**
    Watch
 */
watch(cartUpdated, () => {
    getCart()
})

/**
    Mount
 */
onMounted(() => {
    const token = localStorage.getItem('token')
    if (token) {
        getCart()
        getUserRole()
        getNotification()
    }
    initConfig()
    window.addEventListener('scroll', handle_scroll)

    // Close notifications dropdown when clicking outside
    document.addEventListener('click', (event) => {
        const notificationElement = event.target.closest('.user-actions__notifications')
        if (!notificationElement) {
            show_notifications.value = false
        }
    })
})

onUnmounted(() => {
    window.removeEventListener('scroll', handle_scroll)
})

/**
    Watch
 */
const triggerFileInput = () => {
    fileInput.value?.click()
}

const loading_search = ref(false)
const searchKeywords = ref([])
const handleFileChange = async (event) => {
    const file = event.target.files[0]
    if (file) {
        const formData = new FormData()
        formData.append('image', file)
        try {
            loading_search.value = true
            const res = await api.post('/products/search-by-image', formData)
            const labelsFromImage = res.data.map(item => item.label_vi)
            searchKeywords.value = labelsFromImage
            goToSearchPage()
        } catch (err) {
            console.error('Lỗi khi gửi ảnh:', err)
        } finally {
            loading_search.value = false
        }
    }
}

const keywordInput = ref(route.query.keywords)

const handleTextSearch = () => {
    if (!keywordInput.value) {
        message.emit("show-message", { title: "Thông báo", text: "Vui lòng nhập từ khóa tìm kiếm.", type: "warning" })
        return
    }
    const words = keywordInput.value
        .split(',')
        .map(w => w.trim())
        .filter(Boolean)

    searchKeywords.value = words
    goToSearchPage()
}

const goToSearchPage = () => {
    const joined = searchKeywords.value.join(',')
    router.push({
        name: 'products',
        query: {
            keywords: joined
        }
    })
}

// Handle mobile navigation with authentication check
const handleMobileNavigation = (page) => {
    const token = localStorage.getItem('token')

    if (!token) {
        // Nếu chưa đăng nhập, chuyển hướng đến trang đăng nhập
        router.push({
            name: 'login',
            query: {
                redirect: getRedirectPath(page)
            }
        })
        return
    }

    // Nếu đã đăng nhập, chuyển đến trang tương ứng
    switch (page) {
        case 'cart':
            router.push({ name: 'cart' })
            break
        case 'notification':
            router.push({ name: 'notification' })
            break
        case 'profile':
            router.push({ name: 'user-profile' })
            break
        default:
            break
    }
}

const getRedirectPath = (page) => {
    const redirectPaths = {
        'cart': '/gio-hang',
        'wishlist': '/yeu-thich',
        'notification': '/thong-bao',
        'profile': '/tai-khoan'
    }
    return redirectPaths[page] || '/'
}
watch(
    () => route.query.keywords,
    (newVal) => {
        keywordInput.value = newVal || ''
    }
)

const goToAdmin = () => {
    if (hasPermission('dashboard')) {
        router.push({ name: 'admin-dashboard' })
    } else {
        router.push({ name: 'admin-profile' })
    }
}

</script>

<style scoped>
.menu-mobile-container {
    transition: opacity 0.3s ease, transform 0.3s ease;
}

.menu-mobile-container[style*="display: none"] {
    opacity: 0;
    transform: translateY(100%);
}

.sidebar__modal {
    top: 100px;
    width: 400px;
    left: 0px;
    min-height: 90vh;
    z-index: 9999;
}

.__conversation {
    min-height: 550px;
    left: 410px;
    top: 100px;
    width: 450px;
    background-color: rgb(247, 247, 247);
    z-index: 9999;
}

.chat-messages-container {
    height: 450px !important;
    overflow-y: auto;
}

/* Mobile Responsive Chat */
@media (max-width: 768px) {
    .sidebar__modal {
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        min-height: 100vh;
        z-index: 10000;
    }

    .__conversation {
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        min-height: 100vh;
        z-index: 10001;
    }

    .chat-messages-container {
        height: calc(100vh - 120px) !important;
        overflow-y: auto;
    }

    .mobile-text-truncate {
        width: calc(100vw - 120px);
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .mobile-product-img {
        min-width: 40px;
        height: 40px;
    }

    .mobile-product-name {
        line-height: 1.2;
        max-height: 2.4em;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    .__self,
    .__other {
        max-width: 85%;
        font-size: 13px;
    }
}

/* Close Button Styles */
.btn-close-chat {
    background: none;
    border: none;
    color: #6c757d;
    font-size: 18px;
    padding: 8px;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    cursor: pointer;
}

.btn-close-chat:hover {
    background-color: #f8f9fa;
    color: #495057;
}

.btn-close-chat:active {
    background-color: #e9ecef;
    transform: scale(0.95);
}

@media (max-width: 768px) {
    .btn-close-chat {
        width: 44px;
        height: 44px;
        font-size: 20px;
        padding: 10px;
    }
}

/* Slide-in from left */
.slide-in-left-enter-from {
    transform: translateX(-100%);
}

.slide-in-left-enter-active {
    transition: all 0.3s ease;
}

.slide-in-left-enter-to {
    transform: translateX(0);
}

.slide-in-left-leave-from {
    transform: translateX(0);
}

.slide-in-left-leave-active {
    transition: all 0.3s ease;
}

.slide-in-left-leave-to {
    transform: translateX(-100%);
}

.__self {
    max-width: 70%;
    background-color: #e9faf1;
    padding: 10px 14px 5px 14px;
    border-radius: 10px 0px 10px 10px;
    font-size: 14px;
    line-height: 1.2;
    color: #212121;
}

.__other {
    max-width: 70%;
    background-color: #fdfdfd;
    padding: 10px 14px 5px 14px;
    border-radius: 0px 10px 10px 10px;
    font-size: 14px;
    line-height: 1.2;
    color: #212121;
}
.__img img {
    width: 50px;
    height: 50px;
    border-radius: 5px;
    object-fit: cover;
}
.__name {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Notifications Styles */
.user-actions__notifications {
    position: relative;
}

.notification-badge {
    position: absolute;
    top: 0px;
    right: -8px;
    background-color: #ff4757;
    color: white;
    border-radius: 50%;
    width: 18px;
    height: 18px;
    font-size: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}

.notifications__dropdown {
    top: 100%;
    right: 0;
    width: 380px;
    border-radius: 5px;
    border: 1px solid #e1e5e9;
    z-index: 9999;
    margin-top: 8px;
}

.notifications__dropdown::before {
    content: '';
    position: absolute;
    top: -8px;
    right: 20px;
    width: 0;
    height: 0;
    border-left: 8px solid transparent;
    border-right: 8px solid transparent;
    border-bottom: 8px solid white;
}

.notification__item {
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.notification__item:hover {
    background-color: #f8f9fa;
}

.notification__item.unread {
    background-color: #f0f8ff;
}

.notification__item.unread:hover {
    background-color: #e6f3ff;
}

.unread-dot {
    width: 8px;
    height: 8px;
    background-color: #d52220;
    border-radius: 50%;
}

.notification__title {
    color: #212529;
    margin-bottom: 4px;
}

.notification__message {
    color: #6c757d;
    line-height: 1.4;
    margin-bottom: 4px;
}

.notification__time {
    color: #adb5bd;
}

.notifications__footer a {
    text-decoration: none;
    font-weight: 500;
}

.notifications__footer a:hover {
    text-decoration: underline;
}
</style>