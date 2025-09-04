<template>
    <header style="z-index: 100;"
        class="admin__header-top d-flex align-items-center justify-content-between px-2 shadow-sm">
        <button id="menu-toggle" @click.stop="toggleMenu" class="d-md-none">
            <i class="ri-menu-line fs-24"></i>
        </button>
        <div class="d-flex align-items-center gap-2 fs-22 logo">
            <p class="m-0 main-color fw-semibold">EZShop</p>
            <span class="text-dark fw-medium">ADMIN</span>
        </div>
        <div class="d-flex align-items-center gap-4">
            <div class="position-relative notification-container">
                <div class="position-relative cursor-pointer" @click="show_notifications = !show_notifications">
                    <i class="fs-24 ri-notification-3-line"></i>
                    <span class="notification-badge" v-if="unread_count > 0">{{ unread_count }}</span>
                </div>

                <!-- Notifications Dropdown -->
                <div class="notifications__dropdown position-absolute bg-white shadow-lg" v-show="show_notifications">
                    <div class="notifications__header px-3 py-2 border-bottom">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-semibold">Thông báo</h6>
                            <span class="fs-12 text-muted">{{ notifications.length }} thông báo</span>
                        </div>
                    </div>

                    <div class="notifications__content" style="max-height: 400px; overflow-y: auto;">
                        <!-- Notification Items -->
                        <div class="notification__item px-3 py-2 border-bottom" v-for="notification in notifications"
                            :key="notification.id" :class="{ 'unread': !notification.is_read }"
                            @click="markAsRead(notification)">
                            <a :href="`${notification.link ? notification.link : '#'}`" class="d-flex align-items-start">
                                <div class="notification__icon me-2">
                                    <i class="fa-solid fa-circle-info text-danger fs-16"></i>
                                </div>
                                <div class="notification__content flex-grow-1">
                                    <h6 class="notification__title fs-14 fw-semibold mb-1">{{ notification.title }}</h6>
                                    <p class="notification__message fs-13 text-muted mb-1">{{ notification.message }}
                                    </p>
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

                    <div class="notifications__footer px-3 py-2 border-top text-center" v-if="notifications.length > 0">
                        <a href="#" class="text-danger fs-13" @click="markAllAsRead">Đánh dấu tất cả đã đọc</a>
                    </div>
                </div>
            </div>
            <div class="position-relative account">
                <a class="fw-medium cursor-pointer dropdown-toggle">
                    <img class="avatar me-2" :src="avatar ? `/imgs/users/${avatar}` : '/imgs/user-default.jpg'"
                        alt="User Avatar">
                    <span class="name">{{ user_name }}</span>
                </a>
                <div class="subaccount position-absolute bg-white">
                    <div class="d-flex justify-content-around align-items-center flex-column">
                        <img class="avatar my-2" :src="avatar ? `/imgs/users/${avatar}` : '/imgs/avtDefault.png'"
                            alt="User Avatar">
                        <span class="fs-16 fw-medium">{{ user_name }}</span>
                    </div>
                    <hr class="text-secondary">
                    <div class="d-flex flex-column gap-2">
                        <router-link class="fs-16 text-dark fw-medium" to="/admin/ho-so">
                            <i class="ri-settings-2-line"></i>
                            Hồ sơ
                        </router-link>
                        <router-link class="fs-16 text-dark fw-medium" to="/">
                            <i class="ri-home-line"></i>
                            Trang người dùng
                        </router-link>
                    </div>
                    <hr class="text-secondary">
                    <a @click="logout" class="fs-16 text-dark fw-medium" href="#">
                        <i class="ri-logout-circle-r-line"></i>
                        Đăng xuất
                    </a>
                </div>
            </div>
        </div>
    </header>
</template>
<script setup>
const props = defineProps({
    toggleMenu: {
        type: Function,
        required: true
    }
})

import api from '@/configs/api'
import { user_name_auth, avatar_user, clear_user } from '@/utils/authState'
import message from "@/utils/messageState"
import { useRouter } from 'vue-router'
import { ref, onMounted } from 'vue'
import { formatNotificationTime } from '@/utils/formatNotiTime.js'
import { clearPermissionsCache } from '@/utils/permissionUtils'

const router = useRouter()

const user_name = user_name_auth
const avatar = avatar_user

// Notifications
const show_notifications = ref(false)
const notifications = ref([])
const unread_count = ref(0)
const getNotification = async () => {
    try {
        const res = await api.get('/notifications/admin')
        notifications.value = res.data.notifications
        unread_count.value = res.data.unread_count
    } catch (error) {
        console.error('Lỗi khi lấy thông báo:', error)
    }
}

const logout = async () => {
    try {
        const res = await api.post('/logout');
        clear_user();
        clearPermissionsCache();
        message.emit("show-message", { title: "Thông báo", text: res.data.message, type: "success" })
        router.push("/")
    } catch (error) {
        console.error(error);
        message.emit("show-message", { title: "Lỗi", text: "Đăng xuất thất bại.", type: "error" })
    } finally {
        clear_user()
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
            send_type: 'to_admin'
        })
        getNotification()
        // show_notifications.value = false
    } catch (error) {
        console.error('Lỗi khi đánh dấu tất cả thông báo là đã đọc:', error)
    }
}

onMounted(() => {
    getNotification()
    // Close notifications dropdown when clicking outside
    document.addEventListener('click', (event) => {
        const notificationElement = event.target.closest('.notification-container')
        if (!notificationElement) {
            show_notifications.value = false
        }
    })
})

</script>

<style scoped>
/* Notifications Styles */
.notification-container {
    position: relative;
}

.cursor-pointer {
    cursor: pointer;
}

.notification-badge {
    position: absolute;
    top: 0px;
    right: -5px;
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
    right: -15px;
    width: 380px;
    border-radius: 5px;
    border: 1px solid #e1e5e9;
    z-index: 99999;
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