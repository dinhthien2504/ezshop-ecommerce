<template>
  <div
    class="__sidebar d-none d-md-flex bg-white position-relative min-vh-100 border-start align-items-center flex-column px-3 gap-4">
    <div class="sidebar__notification mt-3 position-relative">
      <div class="position-relative cursor-pointer" @click="show_notifications = !show_notifications">
        <i class="fa-solid fa-bell main-color fs-24"></i>
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
            :key="notification.id" :class="{ 'unread': !notification.is_read }" @click="markAsRead(notification)">
            <a :href="`${notification.link ? notification.link : '#'}`" class="d-flex align-items-start">
              <div class="notification__icon me-2">
                <i class="fa-solid fa-circle-info text-danger fs-16"></i>
              </div>
              <div class="notification__content flex-grow-1">
                <h6 class="notification__title fs-14 fw-semibold mb-1">{{ notification.title }}</h6>
                <p class="notification__message fs-13 text-muted mb-1">{{ notification.message }}</p>
                <small class="notification__time text-muted fs-12">{{ formatNotificationTime(notification.created_at)
                }}</small>
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

    <div class="sidebar__chat mt-3 position-relative">
      <i class="fa-solid fa-headset main-color fs-22"></i>
      <div class="sidebar__chat--text position-absolute fs-12 shadow-sm px-3 py-2">Chat với EzeShop</div>
    </div>

    <div class="sidebar__message mt-3">
      <i class="fa-regular fa-comment-dots main-color fs-22" @click="show_sidebar_chat = !show_sidebar_chat"></i>
    </div>
  </div>

  <div
    class="__mobile-sidebar d-flex d-md-none justify-content-around align-items-center bg-white shadow position-fixed bottom-0 start-0 end-0 py-2 border-top"
    v-show="show_mobile_menu">
    <div class="sidebar__notification text-center position-relative">
      <div class="position-relative cursor-pointer" @click="show_notifications = !show_notifications">
        <i class="fa-solid fa-bell main-color fs-24 sidebar__task"></i>
        <span class="notification-badge-mobile" v-if="unread_count > 0">{{ unread_count }}</span>
      </div>

      <!-- Mobile Notifications Dropdown -->
      <div class="notifications__dropdown--mobile position-fixed bg-white shadow-lg" v-show="show_notifications">
        <!-- Mobile Header -->
        <div class="mobile-header d-flex align-items-center justify-content-between px-4 py-3 bg-primary text-white">
          <div class="d-flex align-items-center">
            <i class="fa-solid fa-bell me-2 fs-18"></i>
            <h6 class="mb-0 fw-semibold">Thông báo</h6>
          </div>
          <button class="btn btn-link text-white p-0" @click="show_notifications = false">
            <i class="fa-solid fa-times fs-24"></i>
          </button>
        </div>

        <!-- Notification Counter -->
        <div class="notification-stats px-4 py-2 bg-light border-bottom">
          <small class="text-muted">
            {{ notifications.length }} thông báo
            <span v-if="unread_count > 0" class="text-danger fw-semibold ms-1">
              ({{ unread_count }} chưa đọc)
            </span>
          </small>
        </div>

        <!-- Content -->
        <div class="notifications__content--mobile">
          <!-- Mobile Notification Items -->
          <div class="notification__item--mobile" v-for="notification in notifications" :key="notification.id"
            :class="{ 'notification--unread': !notification.is_read }" @click="markAsRead(notification)">
            <div class="notification-card">
              <div class="d-flex">
                <div class="notification-icon">
                  <div class="icon-circle" :class="!notification.is_read ? 'bg-danger' : 'bg-secondary'">
                    <i class="fa-solid fa-bell text-white fs-14"></i>
                  </div>
                </div>
                <div class="notification-body flex-grow-1">
                  <h6 class="notification-title">{{ notification.title }}</h6>
                  <p class="notification-message">{{ notification.message }}</p>
                  <div class="notification-meta d-flex justify-content-between align-items-center">
                    <small class="notification-time">
                      <i class="fa-regular fa-clock me-1"></i>
                      {{ formatNotificationTime(notification.created_at) }}
                    </small>
                    <div v-if="!notification.is_read" class="notification-badge">
                      <span class="badge bg-danger rounded-pill">Mới</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Mobile Empty State -->
          <div class="empty-state text-center py-5" v-if="notifications.length === 0">
            <div class="empty-icon mb-3">
              <i class="fa-solid fa-bell-slash fs-1 text-muted"></i>
            </div>
            <h6 class="text-muted mb-2">Không có thông báo</h6>
            <p class="text-muted fs-14 mb-0">Bạn sẽ nhận được thông báo ở đây</p>
          </div>
        </div>

        <!-- Mobile Footer -->
        <div class="notifications__footer--mobile" v-if="notifications.length > 0">
          <button class="btn btn-primary w-100 rounded-0" @click="markAllAsRead">
            <i class="fa-solid fa-check-double me-2"></i>
            Đánh dấu tất cả đã đọc
          </button>
        </div>
      </div>
    </div>

    <div class="sidebar__chat text-center position-relative">
      <div class="cursor-pointer" @click="show_sidebar_chat = !show_sidebar_chat">
        <i class="fa-solid fa-headset main-color fs-22 sidebar__task"></i>
        <div class="sidebar__chat--text position-absolute fs-12 shadow-sm px-3 py-2 d-none d-sm-block">Chat với EzeShop
        </div>
      </div>
    </div>

    <div class="sidebar__menu text-center">
      <div class="cursor-pointer" @click="show_mobile_shop_menu = !show_mobile_shop_menu">
        <i class="fa-solid fa-bars main-color fs-22 sidebar__task"></i>
      </div>
    </div>

    <div class="sidebar__message text-center">
      <div class="cursor-pointer" @click="show_sidebar_chat = !show_sidebar_chat">
        <i class="fa-regular fa-comment-dots main-color fs-22 sidebar__task"></i>
      </div>
    </div>
  </div>

  <!-- Chat Modals - Outside desktop sidebar to work on mobile -->
  <Transition name="slide-in-left">
    <div class="sidebar__modal __chat position-fixed bg-white shadow-sm user-select-none" v-if="show_sidebar_chat">
      <div class="__title w-100 border-bottom px-3 py-2 d-flex justify-content-between align-items-center">
        <div class="__title-text fs-5 main-color fw-semibold fs-18">Chat</div>
        <div class="__task d-flex align-items-center fs-14">
          <button class="btn-close-chat p-0 border-0 bg-transparent me-2" type="button"
            @click="show_sidebar_chat = false">
            <i class="fa-solid fa-rotate text-grey"></i>
          </button>
          <div class="__seperation mx-2" style="color: rgb(228, 228, 228);">|</div>
          <button class="btn-close-chat p-0 border-0 bg-transparent" type="button" @click="show_sidebar_chat = false">
            <i class="fa-solid fa-angles-right text-grey"></i>
          </button>
        </div>
      </div>

      <div class="__content w-100 overflow-y-auto">
        <div class="__chat">
          <div class="__search-box ms-3 my-2">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" name="" id="" placeholder="Tìm theo tên khách hàng">
          </div>

          <div class="__item d-flex align-items-center gap-2 px-3 py-2 bg-grey" v-for="conversation in conversations"
            :key="conversation.id"
            @click="open_conversation(conversation.id); record_receiver(conversation.receiver_id, conversation.receiver_type)">
            <div class="__avatar" style="width: 40px; height: 40px;">
              <img src="/imgs/user-default.jpg" alt="logo-default" style="width: 100%; height: 100%; border-radius: 50%; object-fit: contain;">
            </div>

            <div class="__info w-100">
              <div class="__top d-flex justify-content-between align-items-center">
                <div class="__name fs-14 fw-semibold">{{ conversation.shop_name ? conversation.shop_name :
                  conversation.user_name }}</div>
                <div class="__time fs-12" :class="{ 'fw-semibold': !conversation.is_read, 'text-grey': conversation.is_read}">{{ conversation.last_message_time }}</div>
              </div>
              <div class="__message fs-13" :class="{ 'fw-semibold': !conversation.is_read, 'text-grey': conversation.is_read}"
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
      <div class="__head w-100 d-flex justify-content-between align-items-center px-3 py-2 border-bottom bg-white">
        <div class="__info d-flex align-items-center gap-2">
          <div class="__avatar" style="width: 30px; height: 30px;">
            <img src="/imgs/user-default.jpg" alt="logo-default" style="width: 100%; height: 100%; border-radius: 50%; object-fit: contain;">
          </div>
          <div class="__name">{{ receiver?.shop_name || receiver?.user_name }}</div>
        </div>

        <div class="__task">
          <button class="btn-close-chat p-0 border-0 bg-transparent" type="button" @click="close_conversation">
            <i class="fa-solid fa-xmark"></i>
          </button>
        </div>
      </div>

      <div class="__commune w-100 position-relative d-flex flex-column px-3 py-2"
        style="height: 400px; overflow-y: auto;" ref="messageContainer">
        <div class="__bubble mb-2 d-flex flex-column shadow-sm" v-for="msg in messages" :key="msg.id"
          :class="msg.is_self ? '__self' : '__other', msg.is_self ? 'align-self-end' : 'align-self-start'">
          <div class="__text" v-if="msg.type == 'text'">{{ msg.content }}</div>

          <div class="__product px-2 py-1 mb-2 d-flex flex-column gap-2" v-else-if="msg.type == 'product'">
            <div class="__title border-bottom fw-500 text-grey pb-2">Bạn đang trao đổi về sản phẩm này</div>

            <div class="__content d-flex gap-2">
              <div class="__img" style="min-width: 50px; height: 50px;">
                <img :src="`/imgs/products/${msg.content.image}`" alt=""
                  style="width: 100%; height: 100%; object-fit: cover; border-radius: 5px;">
              </div>

              <div class="__info fw-semibold d-flex flex-column gap-1">
                <div class="__name">{{ msg.content.name }}</div>
                <div class="__price d-flex gap-2 fs-14 align-items-center">
                  <div class="__min-price text-grey" style="text-decoration: 2px line-through gray;">
                    {{ msg.content.minPrice.toLocaleString('vi-VN') }}đ</div>
                  <div class="__max-price main-color ">{{ msg.content.maxPrice.toLocaleString('vi-VN') }}đ</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="__bottom bg-white d-flex flex-column flex-grow-1">
        <div class="__sender flex-grow-1 d-flex align-items-center px-3 py-2">
          <textarea class="form-control flex-grow-1 me-2" rows="1" placeholder="Nhập nội dung tin nhắn"
            style="resize: none; border: none; outline: none; box-shadow: none;" v-model="new_message"></textarea>
          <button class="btn btn-primary d-flex align-items-center" @click="send_message"
            :disabled="!new_message.trim()">
            <i class="fa-solid fa-paper-plane"></i>
          </button>
        </div>

      </div>
    </div>
  </Transition>

  <!-- Mobile Shop Menu Modal -->
  <Transition name="slide-in-left">
    <div class="mobile-shop-menu position-fixed bg-white shadow-sm user-select-none d-flex flex-column"
      v-if="show_mobile_shop_menu">
      <div
        class="menu-header w-100 border-bottom px-3 py-2 d-flex justify-content-between align-items-center flex-shrink-0">
        <div class="menu-title fs-5 main-color fw-semibold fs-18">Menu Quản lý</div>
        <button class="btn-close-menu p-0 border-0 bg-transparent" type="button" @click="show_mobile_shop_menu = false">
          <i class="fa-solid fa-xmark text-grey fs-20"></i>
        </button>
      </div>

      <div class="menu-content w-100 overflow-y-auto px-3 py-3 flex-grow-1">
        <div class="menu-section mb-4">
          <div class="section-title fw-semibold fs-16 mb-2">Quản lý đơn hàng</div>
          <div class="menu-links">
            <router-link to="/kenh-nguoi-ban/don-hang" class="menu-link d-block py-2 px-3 rounded mb-1"
              @click="show_mobile_shop_menu = false">
              <i class="fa-solid fa-box me-2"></i>Tất cả đơn hàng
            </router-link>
          </div>
        </div>

        <div class="menu-section mb-4">
          <div class="section-title fw-semibold fs-16 mb-2">Quản lý sản phẩm</div>
          <div class="menu-links">
            <router-link to="/kenh-nguoi-ban/san-pham" class="menu-link d-block py-2 px-3 rounded mb-1"
              @click="show_mobile_shop_menu = false">
              <i class="fa-solid fa-cube me-2"></i>Tất cả sản phẩm
            </router-link>
            <router-link to="/kenh-nguoi-ban/them-san-pham" class="menu-link d-block py-2 px-3 rounded mb-1"
              @click="show_mobile_shop_menu = false">
              <i class="fa-solid fa-plus me-2"></i>Thêm sản phẩm
            </router-link>
          </div>
        </div>

        <div class="menu-section mb-4">
          <div class="section-title fw-semibold fs-16 mb-2">Quản lý Thuộc tính</div>
          <div class="menu-links">
            <router-link to="/kenh-nguoi-ban/thuoc-tinh" class="menu-link d-block py-2 px-3 rounded mb-1"
              @click="show_mobile_shop_menu = false">
              <i class="fa-solid fa-tags me-2"></i>Tất cả thuộc tính
            </router-link>
          </div>
        </div>

        <div class="menu-section mb-4">
          <div class="section-title fw-semibold fs-16 mb-2">Khuyến mãi</div>
          <div class="menu-links">
            <router-link to="/kenh-nguoi-ban/khuyen-mai" class="menu-link d-block py-2 px-3 rounded mb-1"
              @click="show_mobile_shop_menu = false">
              <i class="fa-solid fa-percent me-2"></i>Tất cả khuyến mãi
            </router-link>
          </div>
        </div>

        <div class="menu-section mb-4">
          <div class="section-title fw-semibold fs-16 mb-2">Quản lý tài chính</div>
          <div class="menu-links">
            <router-link to="/kenh-nguoi-ban/doanh-thu" class="menu-link d-block py-2 px-3 rounded mb-1"
              @click="show_mobile_shop_menu = false">
              <i class="fa-solid fa-chart-line me-2"></i>Doanh thu
            </router-link>
            <router-link to="/kenh-nguoi-ban/tai-chinh" class="menu-link d-block py-2 px-3 rounded mb-1"
              @click="show_mobile_shop_menu = false">
              <i class="fa-solid fa-wallet me-2"></i>Số dư tài khoản
            </router-link>
          </div>
        </div>

        <div class="menu-section mb-4">
          <div class="section-title fw-semibold fs-16 mb-2">Cài đặt</div>
          <div class="menu-links">
            <router-link to="/kenh-nguoi-ban/ho-so" class="menu-link d-block py-2 px-3 rounded mb-1"
              @click="show_mobile_shop_menu = false">
              <i class="fa-solid fa-store me-2"></i>Thông tin shop
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </Transition>
</template>
<script setup>
import { ref, onMounted, onUnmounted, watch, nextTick } from 'vue'
import echo from '@/echo'
import api from '@/configs/api'
import { useRouter } from 'vue-router'
import { formatNotificationTime } from '@/utils/formatNotiTime.js'

const router = useRouter()

//variables
const show_sidebar_chat = ref(false)
const show_conversation = ref(false)
const show_mobile_shop_menu = ref(false)

const conversations = ref([])
const conversation_id = ref(null)
const messages = ref([])
const new_message = ref('')
const messageContainer = ref(null)
const receiver_id = ref(null)
const receiver_type = ref('')
const receiver = ref(null);

const conversation_channels = {}

/**
    Scroll mobile menu
 */
const show_mobile_menu = ref(true)
let last_scroll_top = 0

const handle_scroll = () => {
  const current_scroll_top = window.pageYOffset || document.documentElement.scrollTop

  if (current_scroll_top > last_scroll_top && current_scroll_top > 10) {
    // Lướt xuống
    show_mobile_menu.value = false
  } else {
    // Lướt lên
    show_mobile_menu.value = true
  }

  last_scroll_top = current_scroll_top <= 0 ? 0 : current_scroll_top
}

// Notifications
const show_notifications = ref(false)
const notifications = ref([])
const unread_count = ref(0)
const getNotification = async () => {
  try {
    const res = await api.get('/notifications/shop')
    notifications.value = res.data.notifications
    unread_count.value = res.data.unread_count
  } catch (error) {
    console.error('Lỗi khi lấy thông báo:', error)
  }
}

//methods
const open_conversation = (id) => {
  conversation_id.value = id
  show_conversation.value = true
  get_messages(id)
  mark_conversation_as_read(id)
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

const record_receiver = (receiverID, receiverType) => {
  receiver_id.value = receiverID
  receiver_type.value = receiverType
}

const send_message = async () => {
  if (!new_message.value.trim()) return;

  // Lấy socket id đang kết nối Pusher
  const socketId = echo.connector.pusher.connection.socket_id;

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

const listen_all_conversations = () => {
  conversations.value.forEach(convo => {
    if (conversation_channels[convo.id]) return;

    const channel = echo.private(`chat.${convo.id}`)
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
          target.last_message = message.content;
          target.last_message_time = formatTime(message.created_at);
        }
      });

    conversation_channels[convo.id] = channel;
  });
};


const scrollToBottom = () => {
  nextTick(() => {
    if (messageContainer.value) {
      messageContainer.value.scrollTop = messageContainer.value.scrollHeight
    }
  })
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
      send_type: 'to_shop'
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

onMounted(() => {
  const token = localStorage.getItem("token");
  if (token) {
    get_conversations()
    getNotification()
  }
  // Scroll handler for mobile menu
  window.addEventListener('scroll', handle_scroll)

  // Close notifications dropdown when clicking outside (both desktop and mobile)
  document.addEventListener('click', (event) => {
    const notificationElement = event.target.closest('.sidebar__notification')
    const mobileDropdown = event.target.closest('.notifications__dropdown--mobile')

    // Don't close if clicking inside mobile dropdown content
    if (!notificationElement && !mobileDropdown) {
      show_notifications.value = false
    }
  })

  // Handle mobile back button or escape key
  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && show_notifications.value) {
      show_notifications.value = false
    }
  })
})

onUnmounted(() => {
  window.removeEventListener('scroll', handle_scroll)
})


</script>

<style scoped>
/* Desktop chat modal */
.container-shop>.__sidebar .sidebar__modal {
  width: 400px;
  right: 50px;
  min-height: 90vh;
}

.container-shop>.__sidebar .__conversation {
  min-height: 550px;
  right: 460px;
  top: 100px;
  width: 450px;
  background-color: rgb(247, 247, 247);
}

/* Default chat modal styles (for both desktop and mobile) */
.sidebar__modal.__chat {
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

/* Mobile responsive chat modal */
@media (max-width: 768px) {
  .sidebar__modal.__chat {
    top: 0;
    left: 0;
    width: 100vw !important;
    height: 100vh !important;
    min-height: 100vh;
    z-index: 10000;
  }

  .__conversation {
    top: 0;
    left: 0;
    width: 100vw !important;
    height: 100vh !important;
    min-height: 100vh;
    z-index: 10001;
  }

  .mobile-shop-menu {
    top: 0;
    left: 0;
    width: 100vw !important;
    height: 100vh !important;
    min-height: 100vh;
    z-index: 10002;
    max-height: 100vh;
    overflow: hidden;
  }
}

/* Mobile Shop Menu Styles */
.mobile-shop-menu {
  top: 0;
  width: 350px;
  left: 0;
  height: 100vh;
  z-index: 9999;
  max-height: 100vh;
}

.menu-header {
  background-color: #f8f9fa;
  flex-shrink: 0;
}

.menu-content {
  background-color: #ffffff;
  flex: 1;
  overflow-y: auto;
  overflow-x: hidden;
  -webkit-overflow-scrolling: touch;
}

.btn-close-menu {
  min-width: 40px;
  min-height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 4px;
  transition: all 0.2s ease;
  cursor: pointer;
}

.btn-close-menu:hover {
  background-color: #e9ecef !important;
}

.btn-close-menu:active {
  transform: scale(0.95);
}

@media (max-width: 768px) {
  .btn-close-menu {
    min-width: 44px;
    min-height: 44px;
  }

  .mobile-shop-menu {
    width: 100vw !important;
    height: 100vh !important;
    max-height: 100vh;
  }
}

.menu-content {
  background-color: #ffffff;
  flex: 1;
  overflow-y: auto;
  overflow-x: hidden;
  -webkit-overflow-scrolling: touch;
}

.menu-section {
  margin-bottom: 1.5rem;
}

.section-title {
  color: #d52220;
  border-bottom: 2px solid #d52220;
  padding-bottom: 4px;
}

.menu-link {
  color: #495057;
  text-decoration: none;
  transition: all 0.2s ease;
  min-height: 44px;
  display: flex;
  align-items: center;
}

.menu-link:hover {
  color: #d52220;
  border-color: #d52220;
  transform: translateX(5px);
}

.menu-link:active {
  background-color: #e9ecef;
}

.menu-link i {
  color: #d52220;
  width: 20px;
}

/* Slide transition animations */
.slide-in-left-enter-active,
.slide-in-left-leave-active {
  transition: transform 0.3s ease, opacity 0.3s ease;
}

.slide-in-left-enter-from {
  transform: translateX(-100%);
  opacity: 0;
}

.slide-in-left-leave-to {
  transform: translateX(-100%);
  opacity: 0;
}

/* Mobile menu button styles */
.btn-mobile-menu {
  min-width: 44px;
  min-height: 44px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 4px;
  transition: all 0.2s ease;
  cursor: pointer;
}

.btn-mobile-menu:hover {
  background-color: #f8f9fa !important;
  color: #d52220 !important;
}

.btn-mobile-menu:active {
  transform: scale(0.95);
  background-color: #e9ecef !important;
}

@media (max-width: 768px) {
  .mobile-only {
    display: block !important;
  }

  .mobile-shop-menu {
    width: 100vw !important;
    min-height: 100vh !important;
  }
}

@media (min-width: 769px) {
  .mobile-only {
    display: none !important;
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

/* Slide-in from right */
.slide-in-right-enter-from {
  transform: translateX(100%);
  /* opacity: 0; */
}

.slide-in-right-enter-active {
  transition: all 0.3s ease;
}

.slide-in-right-enter-to {
  transform: translateX(0);
}

.slide-in-right-leave-from {
  transform: translateX(0);
}

.slide-in-right-leave-active {
  transition: all 0.3s ease;
}

.slide-in-right-leave-to {
  transform: translateX(100%);
}

.__self {
  max-width: 70%;
  background-color: #caffe4;
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

/* Notifications Styles */
.sidebar__notification {
  position: relative;
}

.notification-badge {
  position: absolute;
  top: -5px;
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

.notification-badge-mobile {
  position: absolute;
  top: -8px;
  right: -8px;
  background-color: #ff4757;
  color: white;
  border-radius: 50%;
  width: 16px;
  height: 16px;
  font-size: 9px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
}

.notifications__dropdown {
  top: 0;
  right: 50px;
  width: 380px;
  border-radius: 5px;
  border: 1px solid #e1e5e9;
  z-index: 99999;
}

.notifications__dropdown::before {
  content: '';
  position: absolute;
  top: 10px;
  right: -8px;
  width: 0;
  height: 0;
  border-top: 8px solid transparent;
  border-bottom: 8px solid transparent;
  border-left: 8px solid white;
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

.cursor-pointer {
  cursor: pointer;
}

/* Chat close button styling for better touch targets */
.btn-close-chat {
  min-width: 40px;
  min-height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 4px;
  transition: all 0.2s ease;
  cursor: pointer;
}

.btn-close-chat:hover {
  background-color: #f8f9fa !important;
}

.btn-close-chat:active {
  transform: scale(0.95);
}

@media (max-width: 768px) {
  .btn-close-chat {
    min-width: 44px;
    min-height: 44px;
  }
}

/* Mobile sidebar touch targets */
.sidebar__task {
  padding: 8px;
  border-radius: 4px;
  transition: all 0.2s ease;
}

.__mobile-sidebar .cursor-pointer {
  padding: 12px;
  border-radius: 8px;
  transition: all 0.2s ease;
  min-width: 44px;
  min-height: 44px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.__mobile-sidebar .cursor-pointer:active {
  transform: scale(0.95);
}

/* Ensure mobile sidebar stays above other elements */
.__mobile-sidebar {
  z-index: 1000;
  transition: opacity 0.3s ease, transform 0.3s ease;
}

.__mobile-sidebar[style*="display: none"] {
  opacity: 0;
  transform: translateY(100%);
}

/* Mobile touch targets - no hover for touch devices */
.__mobile-sidebar .cursor-pointer {
  padding: 12px;
  border-radius: 8px;
  transition: all 0.2s ease;
  min-width: 44px;
  min-height: 44px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.__mobile-sidebar .cursor-pointer:active {
  transform: scale(0.95);
  background-color: #e9ecef;
}

/* Mobile Notifications Optimization */
.notifications__dropdown--mobile {
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  width: 100vw;
  height: 100vh;
  z-index: 99999;
  border-radius: 0;
  border: none;
  display: flex;
  flex-direction: column;
}

/* Mobile Header */
.mobile-header {
  background: linear-gradient(135deg, #d52220 0%, #d52220 100%);
  box-shadow: 0 2px 10px rgba(0, 123, 255, 0.3);
}

.mobile-header .btn-link {
  text-decoration: none;
  opacity: 0.9;
  transition: opacity 0.2s ease;
}

.mobile-header .btn-link:hover {
  opacity: 1;
}

/* Notification Stats */
.notification-stats {
  background-color: #f8f9fa;
  border-bottom: 1px solid #e9ecef;
}

/* Content Container */
.notifications__content--mobile {
  flex: 1;
  overflow-y: auto;
  -webkit-overflow-scrolling: touch;
  background-color: #f5f5f5;
}

/* Notification Cards */
.notification__item--mobile {
  margin-bottom: 12px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.notification-card {
  background: white;
  padding: 16px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  border: 1px solid #e9ecef;
  transition: all 0.3s ease;
}

.notification__item--mobile:active .notification-card {
  transform: scale(0.98);
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.12);
}

.notification--unread .notification-card {
  border-left: 4px solid #ff4757;
  background: linear-gradient(135deg, #f8fbff 0%, #ffffff 100%);
  box-shadow: 0 4px 12px rgba(0, 123, 255, 0.15);
}

/* Notification Icon */
.notification-icon {
  margin-right: 12px;
}

.icon-circle {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

/* Notification Body */
.notification-body {
  min-width: 0;
}

.notification-title {
  text-align: start;
  font-size: 15px;
  font-weight: 600;
  color: #212529;
  margin-bottom: 8px;
  line-height: 1.3;
}

.notification-message {
  text-align: start;
  font-size: 14px;
  color: #6c757d;
  margin-bottom: 10px;
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
}

.notification-meta {
  margin-top: 8px;
}

.notification-time {
  color: #adb5bd;
  font-size: 12px;
  font-weight: 500;
}

.notification-badge .badge {
  font-size: 10px;
  padding: 4px 8px;
  font-weight: 600;
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0% {
    transform: scale(1);
  }

  50% {
    transform: scale(1.05);
  }

  100% {
    transform: scale(1);
  }
}

/* Empty State */
.empty-state {
  margin: 40px 0;
}

.empty-icon {
  opacity: 0.4;
}

/* Mobile Footer */
.notifications__footer--mobile {
  background: linear-gradient(to top, #ffffff 0%, #f8f9fa 100%);
  border-top: 1px solid #e9ecef;
  padding: 16px;
  box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
}

.notifications__footer--mobile .btn {
  padding: 12px 24px;
  font-weight: 600;
  font-size: 14px;
  border-radius: 8px;
  background: var(--main-color);
  border: none;
  box-shadow: 0 4px 12px rgba(255, 71, 87, 0.3);
  transition: all 0.3s ease;
}

.notifications__footer--mobile .btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(255, 71, 87, 0.4);
}

.notifications__footer--mobile .btn:active {
  transform: translateY(0);
}

/* Responsive adjustments */
@media (max-width: 576px) {
  .notification-card {
    padding: 14px;
    border-radius: 10px;
  }

  .icon-circle {
    width: 36px;
    height: 36px;
  }

  .notification-title {
    font-size: 14px;
  }

  .notification-message {
    font-size: 13px;
  }
}
</style>