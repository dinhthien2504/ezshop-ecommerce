<template>
    <div class="admin__content">
        <div
            class="p-3 bg-white border-bottom text-grey fs-14 fw-medium d-flex align-items-center justify-content-between">
            <p class="m-0">
                Quản Lý Đơn Hàng
            </p>
            <div class="d-flex align-items-center gap-2">
                <div class="dropdown">
                    <button type="button" class="white-btn px-2 py-1 fs-14 dropdown-toggle radius-2 fw-medium"
                        data-bs-toggle="dropdown">
                        Trạng Thái
                    </button>
                    <ul class="dropdown-menu radius-2 shadow">
                        <li v-for="(item, index) in orderStatuses" :key="index" @click="setStatus(item.value)">
                            <a class="dropdown-item fw-medium cursor-pointer fs-14"
                                :class="{ active: filters.order_status == item.value }">
                                {{ item.label }}
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="dropdown">
                    <button type="button" class="white-btn px-2 py-1 fs-14 dropdown-toggle radius-2 fw-medium"
                        data-bs-toggle="dropdown">
                        Hiển Thị
                    </button>
                    <ul class="dropdown-menu radius-2">
                        <li v-for="option in [10, 25, 50, 100, 500]" :key="option" @click="setPerPage(option)">
                            <a
                                :class="['dropdown-item fw-medium cursor-pointer fs-14', { active: filters.per_page === option }]">
                                {{ option }}
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="admin__search">
                    <button type="button" class="search__button">
                        <i class="ri-search-2-line"></i>
                    </button>
                    <form @submit.prevent="onSearch">
                        <div class="d-flex align-items-center gap-1 position-relative">
                            <button type="submit" style="height: 38px" class="main-btn py-1 px-3 ">
                                <i class="ri-search-2-line"></i>
                            </button>
                            <input type="text" v-model="filters.search_code_order" placeholder="Tìm kiếm theo mã đơn"
                                class="form-control radius-2" />
                            <button v-if="filters.search_code_order" type="button" @click="clearSearch"
                                class="btn position-absolute top-50 end-0 translate-middle-y me-2 p-0 border-0 bg-transparent">
                                <i class="ri-close-line"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div v-if="loading.fetch" style="height: 60vh;" class="d-flex align-items-center justify-content-center">
            <loading_dot size="30px" border="4px" />
        </div>
        <div v-else class="p-10">
            <no_data v-if="orders.length == 0" />
            <div v-else class="table-responsive fade-in">
                <table class="table border">
                    <thead class="table-active">
                        <tr class="lh-lg align-middle">
                            <th class="fs-14 fw-semibold min-w-100">Mã ĐH</th>
                            <th class="fs-14 fw-semibold min-w-100">Người Mua</th>
                            <th class="fs-14 fw-semibold min-w-100">Shop</th>
                            <th class="fs-14 fw-semibold min-w-150">Tổng Tiền</th>
                            <th class="fs-14 fw-semibold min-w-150">Thanh Toán</th>
                            <th class="fs-14 fw-semibold min-w-150">Ngày Đặt</th>
                            <th class="fs-14 fw-semibold min-w-150">Trạng Thái</th>
                            <th class="fs-14 fw-semibold text-center min-w-100">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="order in orders" :key="order.id" class="align-middle fw-medium">
                            <td>#{{ order.order_id }}</td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span>{{ order.user?.user_name }}</span>
                                    <small class="text-muted">ID: {{ order.user.id }}</small>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span>{{ order.shop_name || ('Shop #' + order.shop_id) }}</span>
                                    <small class="text-muted">ID: {{ order.shop_id }}</small>
                                </div>
                            </td>
                            <td>{{ formatPrice(order.total_amount) }}</td>
                            <td>
                                <span v-html="paymentBadge(order.payment_status)"></span>
                            </td>
                            <td>{{ formatDateTime(order.created_date) }}</td>
                            <td>
                                <span v-html="statusBadge(order.order_status)"></span>
                            </td>
                            <td class="align-middle text-center py-3">
                                <div class="more-wrapper">
                                    <button class="more-btn one-button">
                                        <i class="ri-more-fill"></i>
                                    </button>
                                    <div class="more-popup">
                                        <button @click="render_order_details(order)" class="bg-info">
                                            <i class="ri-eye-line"></i>
                                            Xem
                                        </button>
                                        <button v-if="order.refund && order.refund.status == 'escalated'"
                                            @click="render_order_details(order)" class="bg-secondary text-white">
                                            <i class="ri-refund-2-line"></i>
                                            Hoàn trả
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <pagination :meta="meta" @changePage="onChangePage" />
        </div>
    </div>
    <div v-if="is_watching_order"
        class="modal-background container-fluid d-flex justify-content-center align-items-center">
        <div class="modal-ezeshop bg-white shadow-sm px-4 pt-4 pb-2">
            <!-- Header -->
            <div class="modal-ezeshop__top d-flex justify-content-between align-items-center border-bottom">
                <div class="modal-ezeshop__name fs-18 fw-500">Đơn hàng chi tiết</div>
                <div @click="is_watching_order = false" class="modal-ezeshop__cancel cursor-pointer">
                    <i class="fa-solid fa-xmark fs-18"></i>
                </div>
            </div>

            <!-- Main -->
            <div class="modal-ezeshop__main w-100 overflow-y-auto">
                <div class="p-3 bg-white rounded radius-2 m-1" style="overflow-y: auto; height: 65vh;">
                    <!-- Trạng thái -->
                    <div class="d-flex justify-content-end align-items-center mb-2">
                        <div class="d-flex justify-content-end small fw-medium gap-2">
                            Trạng Thái Đơn Hàng:
                            <span class="fw-semibold" :class="{
                                'text-success': order.order_status == 2 || order.order_status == 5,
                                'text-warning': order.order_status == 1,
                                'text-info': order.order_status == 3,
                                'text-primary': order.order_status == 4,
                                'text-danger': order.order_status == 6,
                                'text-dark': order.order_status == 7
                            }">
                                {{ orderStatuses[order.order_status].label }}
                            </span>
                        </div>
                    </div>

                    <!-- Thông tin khách -->
                    <div class="p-3 bg-light border-top fs-14">
                        <div class="fw-medium"><strong>Khách hàng:</strong> {{ order.customer_name }}</div>
                        <div class="fw-medium"><strong>SĐT:</strong> {{ order.customer_phone }}</div>
                        <div class="fw-medium"><strong>Địa chỉ:</strong> {{ order.customer_address }}</div>
                        <div class="fw-medium"><strong>Phương thức thanh toán:</strong> {{ order.payment_method }}</div>
                    </div>

                    <!-- Danh sách sản phẩm -->
                    <div class="border-top py-3" v-for="order_detail in order.order_details" :key="order_detail.id">
                        <router-link class="d-flex" :to="{
                            name: 'product-detail',
                            params: { slug: order_detail.slug, id: order_detail.product_id }
                        }">
                            <img :src="`/imgs/products/${order_detail.image}`" class="rounded me-3 border radius-2"
                                alt="ảnh sản phẩm" style="width: 80px; height: 80px; object-fit: contain;">
                            <div class="flex-grow-1">
                                <div class="fw-medium text-truncate-1-line">{{ order_detail.product_name }}</div>
                                <div class="text-muted small mb-1">{{ order_detail.attributes }}</div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="text-muted small">x{{ order_detail.quantity }}</div>
                                    <div class="text-end w-100 text-danger fw-medium">
                                        {{ formatPrice(Number(order_detail.price)) }}
                                    </div>
                                </div>
                            </div>
                        </router-link>

                        <!-- Nếu có refund items -->
                        <div v-if="order_detail.refund_items" class="p-2 bg-light mt-2 rounded border fs-14">
                            <h6 class="fw-bold mb-2">Yêu cầu hoàn hàng/hoàn tiền</h6>

                            <div class="mb-3 border-bottom pb-2 fw-medium">
                                <!-- Thông tin tiền -->
                                <p class="m-0">
                                    <strong>Số lượng trả: </strong>
                                    {{ order_detail.refund_items.qty }}
                                </p>
                                <!-- Lý do -->
                                <p class="m-0"><strong>Lý do: </strong> {{
                                    reasonMap[order_detail.refund_items.reason_type]
                                    }}</p>
                                <p v-if="order_detail.refund_items.reason_detail" class="m-0">
                                    <strong>Chi tiết: </strong> {{ order_detail.refund_items.reason_detail }}
                                </p>

                                <!-- Ảnh minh chứng -->
                                <div v-if="order_detail.refund_items.evidences && order_detail.refund_items.evidences.length"
                                    class="mt-1">
                                    <p class="m-0"><strong>Bằng chứng: </strong></p>
                                    <div class="d-flex gap-2 flex-wrap mt-1">
                                        <img v-for="(img, idx) in order_detail.refund_items.evidences" :key="idx"
                                            :src="`/imgs/refunds/${img}`" class="rounded border refund-thumb"
                                            :alt="`evidence-${idx}`" @click="openLightbox(`/imgs/refunds/${img}`)"
                                            tabindex="0" @keydown.enter.prevent="openLightbox(`/imgs/refunds/${img}`)"
                                            style="width: 70px; height: 70px; object-fit: cover; cursor: zoom-in;">
                                    </div>
                                </div>

                                <!-- Lightbox overlay -->
                                <div v-if="lightbox.show" class="lightbox-overlay" @click.self="closeLightbox">
                                    <button type="button" class="lightbox-close btn btn-sm btn-light"
                                        @click="closeLightbox" aria-label="Close">✕</button>
                                    <img :src="lightbox.src" class="lightbox-img" :alt="'preview'">
                                </div>

                                <p class="m-0"><strong>Người chịu phí: </strong>
                                    <span v-if="order_detail.refund_items.shipping_payer == 'platform'">Sàn</span>
                                    <span v-else-if="order_detail.refund_items.shipping_payer == 'seller'">Shop</span>
                                    <span v-else>Người mua</span>
                                </p>

                                <p class="m-0"><strong>Tiền sản phẩm: </strong> {{
                                    formatPrice(order_detail.refund_items.subtotal) }}</p>
                                <p class="m-0"><strong>Chiết khấu: </strong> -{{
                                    formatPrice(order_detail.refund_items.discount_allocated) }}
                                </p>
                                <!-- Phí ship -->
                                <p class="m-0"><strong>Phí ship hoàn trả:</strong> {{
                                    formatPrice(order_detail.refund_items.shipping_fee) }}
                                </p>
                                <p class="m-0 fw-semibold"><strong>Hoàn lại: </strong>
                                    <span class="text-color fs-18">
                                        {{ formatPrice(order_detail.refund_items.refund_amount) }}
                                    </span>
                                </p>
                                <!-- Trạng thái -->
                                <p class="fw-medium">
                                    Trạng thái:
                                    <span :class="{
                                        'text-warning': order_detail.refund_items.status == 'pending',
                                        'text-success': ['approved', 'processed'].includes(order_detail.refund_items.status),
                                        'text-danger': order_detail.refund_items.status == 'rejected'
                                    }">
                                        {{ statusRefundMap[order_detail.refund_items.status] }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <p v-else class="fs-14 fw-medium mt-2" :class="{
                            'text-warning': order.order_status == 7 && ['pending', 'approved', 'rejected_by_shop', 'escalated'].includes(order.refund.status),
                            'text-success': order.order_status == 7 && ['processed', 'rejected_final'].includes(order.refund.status)
                        }">
                            <template
                                v-if="order.order_status == 7 && ['pending', 'approved', 'rejected_by_shop', 'escalated'].includes(order.refund.status)">
                                Chờ xử lý đơn hoàn
                            </template>
                            <template
                                v-else-if="order.order_status == 7 && ['processed', 'rejected_final'].includes(order.refund.status)">
                                Đã xử lý
                            </template>
                        </p>
                    </div>

                    <!-- Tổng tiền -->
                    <div class="border-top pt-3 mt-3">
                        <div class="border-top pt-3">
                            <div class="d-flex justify-content-end align-items-center mb-2">
                                <span class="me-2 fw-medium">Tổng đơn hàng:</span>
                                <span v-if="order.refund && order.refund.total_refund > 0"
                                    class="text-secondary fw-medium fs-18 text-decoration-line-through">
                                    {{ formatPrice(Number(order.total_amount)) }}
                                </span>
                                <span v-else class="text-danger fw-medium fs-18">
                                    {{ formatPrice(Number(order.total_amount)) }}
                                </span>
                            </div>
                            <div v-if="order.refund && order.refund.total_refund > 0"
                                class="d-flex justify-content-end align-items-center mb-2">
                                <span class="me-2 fw-medium">Tổng tiền hoàn lại:</span>
                                <span class="text-danger fw-medium fs-18">
                                    {{ formatPrice(Number(order.refund.total_refund)) }}
                                </span>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end align-items-center mb-2">
                            <span class="me-2 fw-medium">Trạng thái thanh toán:</span>
                            <span :class="order.payment_status == 'paid' ? 'text-success' : 'text-warning'">
                                {{ order.payment_status == 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="modal-ezeshop__bottom d-flex justify-content-end w-100 align-items-center border-top">
                <div class="modal-ezeshop__bottom d-flex justify-content-end mt-4">
                    <button class="white-btn px-2 fs-14" style="width: 80px; height: 35px;"
                        @click="is_watching_order = false">Đóng</button>
                    <button class="white-btn fs-14 px-2 ms-2 d-flex align-items-center justify-content-center"
                        @click="updateRefund(order.refund.id, 'rejected_final')"
                        v-if="order.order_status == 7 && order.refund.status == 'escalated'"
                        style="width: 170px; height: 35px;">
                        <loading__loader v-if="loading_refund.rejected_final" size="20px" border="3px" />
                        <span v-else>Từ Chối Hoàn Tiền</span>
                    </button>
                    <button class="main-btn fs-14 px-2 ms-2 d-flex align-items-center justify-content-center"
                        @click="updateRefund(order.refund.id, 'processed')"
                        v-if="order.order_status == 7 && order.refund.status == 'escalated'"
                        style="width: 170px; height: 35px;">
                        <loading__loader v-if="loading_refund.processed" size="20px" border="3px" color="#fff" />
                        <span v-else>Đồng Ý Hoàn Tiền</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import { onMounted, ref, reactive } from 'vue'
import api from '@/configs/api'
import message from '@/utils/messageState'
import loading_dot from '@/components/loading/loading__dot.vue'
import loading__loader from '@/components/loading/loading__loader-circle.vue'
import pagination from '@/components/pagination.vue'
import no_data from '@/components/no_data.vue'
import { formatDateTime } from '@/utils/formatDate'
import { formatPrice } from '@/utils/formatPrice'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()

const loading = ref({
    fetch: false
})
const orderStatuses = [
    { label: 'Tất cả', value: '' },
    { label: 'Chờ xác nhận', value: '1' },
    { label: 'Đã xác nhận', value: '2' },
    { label: 'Chờ giao hàng', value: '3' },
    { label: 'Đã giao hàng', value: '4' },
    { label: 'Hoàn tất', value: '5' },
    { label: 'Đã hủy', value: '6' },
    { label: 'Trả hàng / Hoàn tiền', value: '7' },
]

const setStatus = (status) => {
    filters.value.order_status = status
    fetchOrders()
}
const setPerPage = (perPage) => {
    filters.value.per_page = perPage
    fetchOrders()
}
const onChangePage = (page) => {
    filters.value.page = page
    fetchOrders()
}
const onSearch = () => {
    filters.value.page = 1
    fetchOrders()
}
const clearSearch = () => {
    filters.value.page = 1
    filters.value.search_code_order = ''
    fetchOrders()
}
const orders = ref([])
const currentPage = ref(1)
const meta = ref({})
const filters = ref({
    page: currentPage.value,
    per_page: 10,
    order_status: 7,
    search_code_order: route.query.search_code_order || ''
})

const fetchOrders = async () => {
    loading.value.fetch = true
    try {
        const res = await api.get("orders", {
            params: filters.value,
        })
        orders.value = res.data.orders
        meta.value = res.data.meta
    } catch (error) {
        console.error("Lỗi lấy đơn hàng:", error);
    } finally {
        loading.value.fetch = false
    }
}

const paymentBadge = (status) => {
    switch (status) {
        case 'paid':
            return `<span class="badge bg-success radius-2 px-3 py-2 d-inline-flex align-items-center shadow-sm">
                        <i class="ri-bank-card-line me-1"></i> Đã thanh toán
                    </span>`;
        case 'unpaid':
            return `<span class="badge bg-danger radius-2 px-3 py-2 d-inline-flex align-items-center shadow-sm">
                        <i class="ri-error-warning-line me-1"></i> Chưa thanh toán
                    </span>`;
        case 'refunded':
            return `<span class="badge bg-warning radius-2 px-3 py-2 d-inline-flex align-items-center shadow-sm">
                         <i class="ri-bank-card-line me-1"></i> Đã hoàn tiền
                    </span>`;
        default:
            return `<span class="badge bg-secondary radius-2 px-3 py-2 d-inline-flex align-items-center shadow-sm">
                        <i class="ri-question-line me-1"></i> Không xác định
                    </span>`;
    }
}


const statusBadge = (status) => {
    switch (String(status)) {
        case '1':
            return `<span class="badge bg-warning radius-2">
                        <i class="ri-time-line me-1"></i> Chờ xác nhận
                    </span>`;
        case '2':
            return `<span class="badge bg-primary radius-2">
                        <i class="ri-check-line me-1"></i> Đã xác nhận
                    </span>`;
        case '3':
            return `<span class="badge bg-info radius-2">
                        <i class="ri-truck-line me-1"></i> Chờ giao hàng
                    </span>`;
        case '4':
            return `<span class="badge bg-success radius-2">
                        <i class="ri-truck-fill me-1"></i> Đã giao hàng
                    </span>`;
        case '5':
            return `<span class="badge bg-success radius-2">
                        <i class="ri-check-double-line me-1"></i> Hoàn tất
                    </span>`;
        case '6':
            return `<span class="badge bg-danger radius-2">
                        <i class="ri-close-circle-line me-1"></i> Đã hủy
                    </span>`;
        case '7':
            return `<span class="badge bg-secondary radius-2">
                        <i class="ri-refund-2-line me-1"></i> Trả hàng / Hoàn tiền
                    </span>`;
        default:
            return `<span class="badge bg-light text-dark radius-2">
                        <i class="ri-question-line me-1"></i> Không xác định
                    </span>`;
    }
}
const is_watching_order = ref(false)
const order = ref(null)
const render_order_details = (Order) => {
    order.value = Order
    is_watching_order.value = true
}
const reasonMap = {
    seller_fault: "Người bán sai",
    buyer_fault: "Người mua sai",
    shipping_fault: "Đơn vị vận chuyển sai",
    other: "Lý do khác"
}
const statusRefundMap = {
    pending: 'Chờ xử lý',
    approved: 'Đã duyệt',
    rejected_by_shop: 'Shop từ chối',
    escalated: 'Chờ CSKH xử lý',
    rejected_final: 'Từ chối (kết thúc)',
    processed: 'Đã xử lý',
}
const loading_refund = ref({
    rejected_final: false,
    processed: false,
})
const updateRefund = async (refund_id, status) => {
    try {
        loading_refund.value[status] = true;
        const res = await api.put(`refunds/${refund_id}`, { status })
        if (res.status == 200) {
            const inx = orders.value.findIndex(item => item.refund.id === refund_id);
            if (inx !== -1) {
                orders.value[inx].refund.status = res.data;
                // Cập nhật refund_items trong từng order_detail
                orders.value[inx].order_details.forEach(detail => {
                    if (detail.refund_items && detail.refund_items.refund_id === refund_id) {
                        detail.refund_items.status = res.data;
                    }
                });
            }
            message.emit("show-message", { title: "Thông Báo", text: "Cập nhật trạng thái hoàn tiền thành công.", type: "success" })
        }
    } catch (error) {
        console.log(error)
    } finally {
        loading_refund.value[status] = false
    }
}
const lightbox = reactive({
    show: false,
    src: ''
});

const openLightbox = (src) => {
    lightbox.src = src;
    lightbox.show = true;
    document.body.style.overflow = 'hidden'
}

const closeLightbox = () => {
    lightbox.show = false;
    lightbox.src = '';
    document.body.style.overflow = ''
}

const onKeydown = (e) => {
    if (e.key === 'Escape' && lightbox.show) closeLightbox()
}

onMounted(() => {
    if (route.query.search_code_order) {
        filters.value.search_code_order = route.query.search_code_order
    }

    router.replace({ query: {} })
    fetchOrders()
    window.addEventListener('keydown', onKeydown)
})
</script>
<style scoped>
.dropdown .active {
    background-color: var(--text-color);
    color: var(--white-color) !important;
}

.lightbox-overlay {
    position: fixed;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(0, 0, 0, 0.75);
    z-index: 100;
    padding: 20px;
    animation: lightboxFade .12s ease-out;
}

.lightbox-img {
    max-width: 82vw;
    max-height: 82vh;
    object-fit: contain;
    border-radius: 8px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.6);
    transform: scale(1);
    transition: transform .18s cubic-bezier(.2, .8, .2, 1);
    cursor: zoom-out;
}

.lightbox-img:hover {
    transform: scale(1.02);
}

.lightbox-close {
    position: absolute;
    top: 18px;
    right: 18px;
    z-index: 2010;
    border-radius: 999px;
    padding: .25rem .5rem;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
}

.refund-thumb {
    transition: transform .12s ease, box-shadow .12s ease;
}

.refund-thumb:hover {
    transform: scale(1.06);
    box-shadow: 0 6px 14px rgba(0, 0, 0, 0.15);
    z-index: 2;
}

@keyframes lightboxFade {
    from {
        opacity: 0
    }

    to {
        opacity: 1
    }
}
</style>