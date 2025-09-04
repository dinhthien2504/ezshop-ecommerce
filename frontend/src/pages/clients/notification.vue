<template>
    <div class="w-100 bg-white shadow-sm p-2 mb-2">
        <div class="d-flex justify-content-between align-items-center">
            <div class="ms-3 fw-medium align-items-center d-flex gap-2 m-1" @click="goBack()" style="cursor: pointer;">
                <div class="text-decoration-none text-color">
                    <i class="fs-20 ri-arrow-left-line"></i>
                </div>
                Trở về
            </div>
        </div>
    </div>
    <div class="profile__right overflow-hidden">
        <div v-if="loading.fetch" class="d-flex align-items-center justify-content-center p-4 text-center flex-column"
            style="min-height: 500px;">
            <loading__dot />
            <p class="mt-2">Đang tải dữ liệu...</p>
        </div>
        <div v-else class="border py-3 px-5 bg-white shadow-sm mx-1 mt-lg-3 mb-2" style="min-height: 70vh;">
            <div class="border-bottom pb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5>Thông Báo</h5>
                        <span class="fs-12 text-muted">{{ notifications.length }} thông báo
                            <span v-if="unreadCount > 0" class="text-danger fw-semibold">
                                ({{ unreadCount }} mới)
                            </span>
                        </span>
                    </div>
                    <div>
                        <button class="btn btn-sm btn-outline-danger rounded-pill px-3" @click="markAllAsRead"
                            v-if="unreadCount > 0" :disabled="loading.markAll">
                            <span v-if="loading.markAll" class="spinner-border spinner-border-sm me-2"
                                role="status"></span>
                            <i class="fa-solid fa-check-double me-1" v-if="!loading.markAll"></i>
                            {{ loading.markAll ? 'Đang xử lý...' : 'Đánh dấu tất cả đã đọc' }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Notifications List -->
            <div v-if="!loading.fetch && notifications.length === 0"
                class="d-flex align-items-center justify-content-center flex-column" style="min-height: 400px;">
                <i class="fa-solid fa-bell-slash fs-1 text-muted mb-3"></i>
                <p class="text-muted">Không có thông báo nào</p>
            </div>

            <div v-if="!loading.fetch && notifications.length > 0" class="notifications-list mt-3">
                <!-- Notification Items -->
                <div class="notification__item px-3 py-2 border-bottom cursor-pointer"
                    v-for="notification in notifications" :key="notification.id"
                    :class="{ 'unread': !notification.is_read }" @click="markAsRead(notification)">
                    <a :href="`${notification.link ? notification.link : '#'}`"
                        class="d-flex align-items-start text-decoration-none">
                        <div class="notification__icon me-2">
                            <i class="fa-solid fa-circle-info text-danger fs-16"></i>
                        </div>
                        <div class="notification__content flex-grow-1">
                            <h6 class="notification__title fs-14 fw-semibold mb-1">{{ notification.title }}</h6>
                            <p class="notification__message fs-13 text-muted mb-1">{{ notification.message }}</p>
                            <small class="notification__time text-muted fs-12">{{
                                formatNotificationTime(notification.created_at) }}</small>
                        </div>
                        <div class="notification__status">
                            <div class="unread-dot" v-if="!notification.is_read"></div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Load More / Infinite Scroll -->
            <div v-if="!loading.fetch && notifications.length > 0 && hasMoreNotifications"
                class="d-flex justify-content-center mt-4">
                <div v-if="loading.loadMore" class="text-center py-3">
                    <loading__dot />
                    <p class="mt-2 text-muted fs-14">Đang tải thêm thông báo...</p>
                </div>
                <div v-else ref="loadMoreTrigger" class="load-more-trigger"></div>
            </div>

            <!-- End of notifications -->
            <!-- <div v-if="!loading.fetch && notifications.length > 0 && !hasMoreNotifications" 
                class="text-center py-4">
                <i class="fa-solid fa-check-circle text-success fs-20 mb-2"></i>
                <p class="text-muted fs-14 mb-0">Bạn đã xem hết tất cả thông báo</p>
            </div> -->
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed, nextTick } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/configs/api'
import message from '@/utils/messageState'
import loading__dot from '@/components/loading/loading__dot.vue'
import { formatNotificationTime } from '@/utils/formatNotiTime.js'

const router = useRouter()

// Data
const notifications = ref([])
const loading = ref({
    fetch: false,
    markAll: false,
    markSingle: null,
    loadMore: false
})
const currentPage = ref(1)
const perPage = ref(10)
const hasMoreNotifications = ref(true)
const loadMoreTrigger = ref(null)
let observer = null

// Computed
const unreadCount = computed(() => {
    return notifications.value.filter(n => !n.is_read).length
})

// Methods
const fetchNotifications = async (page = 1, append = false) => {
    try {
        if (page === 1) {
            loading.value.fetch = true
        } else {
            loading.value.loadMore = true
        }

        const res = await api.get('/notifications/user', {
            params: {
                page: page,
                per_page: perPage.value
            }
        })

        if (res.status === 200) {
            const newNotifications = res.data.notifications || []

            if (append) {
                // Thêm vào danh sách hiện tại
                notifications.value = [...notifications.value, ...newNotifications]
            } else {
                // Thay thế danh sách
                notifications.value = newNotifications
            }

            // Kiểm tra có còn dữ liệu không
            const totalCount = res.data.total_count || 0
            const currentCount = notifications.value.length
            hasMoreNotifications.value = currentCount < totalCount

            currentPage.value = page
        }
    } catch (error) {
        console.error('Lỗi khi lấy danh sách thông báo:', error)
        message.emit('show-message', {
            title: 'Lỗi',
            text: 'Không thể tải danh sách thông báo',
            type: 'error'
        })
    } finally {
        loading.value.fetch = false
        loading.value.loadMore = false
    }
}

const loadMoreNotifications = async () => {
    if (loading.value.loadMore || !hasMoreNotifications.value) return

    await fetchNotifications(currentPage.value + 1, true)
}

const setupIntersectionObserver = () => {
    if (!loadMoreTrigger.value) return

    observer = new IntersectionObserver(
        (entries) => {
            const [entry] = entries
            if (entry.isIntersecting && hasMoreNotifications.value && !loading.value.loadMore) {
                loadMoreNotifications()
            }
        },
        {
            root: null,
            rootMargin: '100px',
            threshold: 0.1
        }
    )

    observer.observe(loadMoreTrigger.value)
}

const markAsRead = async (notification) => {
    if (notification.is_read) {
        // Nếu đã đọc, chỉ redirect
        if (notification.link) {
            router.push(notification.link)
        }
        return
    }

    try {
        loading.value.markSingle = notification.id
        await api.post(`/notifications/read/${notification.id}`)

        // Cập nhật trạng thái local
        notification.is_read = true

        // Redirect nếu có link
        if (notification.link) {
            router.push(notification.link)
        }
    } catch (error) {
        console.error('Lỗi khi đánh dấu thông báo đã đọc:', error)
    } finally {
        loading.value.markSingle = null
    }
}

const markAllAsRead = async () => {
    try {
        loading.value.markAll = true
        await api.post('/notifications/read-all', {
            send_type: 'to_user'
        })

        // Cập nhật tất cả notifications thành đã đọc
        notifications.value.forEach(notification => {
            notification.is_read = true
        })
    } catch (error) {
        console.error('Lỗi khi đánh dấu tất cả thông báo đã đọc:', error)
    } finally {
        loading.value.markAll = false
    }
}

const goBack = () => {
    router.back();
}

// Lifecycle
onMounted(async () => {
    await fetchNotifications()

    // Setup intersection observer sau khi component đã mount
    await nextTick()
    setupIntersectionObserver()
})

// Cleanup observer khi component unmount
onUnmounted(() => {
    if (observer) {
        observer.disconnect()
    }
})
</script>

<style scoped>
/* Notification Container */
.notifications-list {
    max-height: calc(100vh - 300px);
    overflow-y: auto;
}

/* Notification Item - Same as header.vue */
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

/* Unread dot - Same as header.vue */
.unread-dot {
    width: 8px;
    height: 8px;
    background-color: #d52220;
    border-radius: 50%;
}

/* Notification content - Same as header.vue */
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

/* Link styles */
.notification__item a {
    color: inherit;
}

.notification__item a:hover {
    color: inherit;
    text-decoration: none;
}

/* Button Styles */
.btn-outline-danger.rounded-pill {
    border-width: 2px;
    font-weight: 500;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.btn-outline-danger.rounded-pill:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
}

.btn-outline-danger.rounded-pill:disabled {
    opacity: 0.6;
    transform: none;
    box-shadow: none;
}

/* Spinner */
.spinner-border-sm {
    width: 0.875rem;
    height: 0.875rem;
}

/* Utility Classes */
.cursor-pointer {
    cursor: pointer;
}

/* Empty State */
.fa-bell-slash {
    opacity: 0.3;
}

/* Responsive */
@media (max-width: 768px) {
    .notification__item {
        padding: 12px 16px !important;
    }

    .notification__content {
        margin-right: 8px;
    }
}



/* Load More Trigger */
.load-more-trigger {
    height: 20px;
    width: 100%;
    background: transparent;
}
</style>