<script setup>
import { ref, onMounted } from 'vue'
import api from '@/configs/api'
import orderStatus from '@/components/orderStatus.vue'
import { useRoute, useRouter } from 'vue-router'
import loading__dot from '../../../components/loading/loading__dot.vue'
import loading__loader_circle from '../../../components/loading/loading__loader-circle.vue'
import { formatPrice } from '@/utils/formatPrice.js'
import message from '@/utils/messageState'
const route = useRoute()
const router = useRouter()
const goBack = () => {
    router.back()
}
const order = ref(null)
const is_loading = ref(true)
const getOrder = async () => {
    const orderId = route.params.id
    try {
        is_loading.value = true

        const res = await api.get(`/orders/${orderId}/user`)
        if (res.status === 200) {
            order.value = res.data.order
            is_loading.value = false
        }
    } catch (error) {
        console.log(error)
    }
}

const statusRefundMap = {
    pending: 'Chờ xử lý',
    approved: 'Đã duyệt',
    rejected_by_shop: 'Shop từ chối',
    escalated: 'Chờ CSKH xử lý',
    rejected_final: 'Từ chối (kết thúc)',
    processed: 'Đã xử lý',
}

const loading_refund = ref(false)
const updateRefund = async (refund_id, status) => {
    try {
        loading_refund.value = true;
        const res = await api.put(`refunds/${refund_id}`, { status })
        if (res.status == 200) {
            order.value.refund_status = res.data
            order.value.order_details.forEach(detail => {
                if (detail.refund && detail.refund.refund_id === refund_id) {
                    detail.refund.status = res.data;
                }
            });
            message.emit("show-message", { title: "Thông Báo", text: "Đã gửi yêu cầu cho CSKH", type: "success" })
        }
    } catch (error) {
        console.log(error);
    } finally {
        loading_refund.value = false;
    }
}
onMounted(async () => {
    await getOrder()
})
</script>
<template>
    <div class="profile__right position-relative">
        <div v-if="is_loading" class="d-flex align-items-center justify-content-center p-4 text-center flex-column"
            style="min-height: 500px;">
            <loading__dot />
            <p class="mt-2">Đang tải dữ liệu đơn hàng...</p>
        </div>
        <div v-else>
            <div class="position-sticky mt-lg-2 top-0 mx-lg-1 d-none d-lg-block bg-light py-2">
                <div class="fw-bold align-items-center d-flex gap-2" @click="goBack()" style="cursor: pointer;">
                    <div class="text-decoration-none text-color">
                        <i class="fs-20 ri-arrow-left-line"></i>
                    </div>
                    Trở về
                </div>
            </div>
            <div class="p-2 bg-white mx-1 shadow-sm">
                <h3 class="fw-bold fs-14">Thông tin vận chuyển</h3>
                <div class="d-flex gap-2 pb-2">
                    <i class="ri-caravan-line"></i>
                    <div class="d-flex flex-column justify-content-start fs-14">
                        <p class="m-0 text-success fw-medium">
                            <orderStatus :status="order.order_status" />
                        </p>
                        <span class="text-secondary">{{ order.created_date }}</span>
                    </div>
                </div>
            </div>
            <div class="p-2 bg-white mx-1 border-top shadow-sm">
                <h3 v-if="order.order_status == 7" class="fw-bold fs-14">Địa chỉ lấy hàng</h3>
                <h3 v-else class="fw-bold fs-14">Địa chỉ nhận hàng</h3>
                <div class="d-flex gap-2 pb-2">
                    <i class="ri-map-pin-line"></i>
                    <div class="d-flex flex-column justify-content-start fs-14 fw-medium">
                        <p class="m-0 text-black">{{ order.customer_name }} | {{ order.customer_phone }}</p>
                        <span class="text-secondary">{{ order.customer_address }}</span>
                    </div>
                </div>
            </div>
            <div class="p-3 bg-white mx-1 mt-2 shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-main radius-2">Yêu thích</span>
                        <strong class="shop-name">{{ order.shop_name }}</strong>
                    </div>
                </div>
                <router-link :to="{
                    name: 'product-detail',
                    params: { slug: Dorder.slug, id: Dorder.product_id }
                }" v-for="Dorder in order.order_details" class="d-flex border-top py-2">
                    <img :src="`/imgs/products/${Dorder.image}`" class="rounded me-3 border radius-2"
                        :alt="`${Dorder.product_name}`" style="width: 80px; height: 80px; object-fit: contain;">
                    <div class="flex-grow-1">
                        <div class="fw-medium text-truncate-1-line">{{ Dorder.product_name }}</div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="fs-14">{{ Dorder.attributes }}</div>
                            <div class="fs-14">x{{ Dorder.quantity }}</div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <p v-if="Dorder.refund" class="fs-14 fw-medium">
                                Hoàn trả:
                                <span class="fs-16 text-color">
                                    {{ formatPrice(Number(Dorder.refund.refund_amount)) }}
                                    ({{ statusRefundMap[Dorder.refund.status] }})
                                </span>
                            </p>
                            <p v-else class="fs-14 fw-medium" :class="{
                                'text-warning': order.order_status == 7 && ['pending', 'approved', 'rejected_by_shop', 'escalated'].includes(order.refund_status),
                                'text-success': order.order_status == 7 && ['processed', 'rejected_final'].includes(order.refund_status)
                            }">
                                <template
                                    v-if="order.order_status == 7 && ['pending', 'approved', 'rejected_by_shop', 'escalated'].includes(order.refund_status)">
                                    Chờ xử lý
                                </template>
                                <template
                                    v-else-if="order.order_status == 7 && ['processed', 'rejected_final'].includes(order.refund_status)">
                                    Đã xử lý
                                </template>
                            </p>
                            <!-- <p v-else></p> -->
                            <div class="small my-2 text-end text-color fw-medium">
                                {{ formatPrice(Number(Dorder.price)) }}
                            </div>
                        </div>
                    </div>
                </router-link>
                <div class="border-top pt-3">
                    <div class="d-flex justify-content-between align-items-center my-2">
                        <span class="fs-12 fw-medium">Tổng tiền hàng</span>
                        <span class="fs-14 fw-medium">
                            {{ formatPrice(Number(order.product_total)) }}
                        </span>
                    </div>
                    <div v-if="order.shipping_fee" class="d-flex justify-content-between align-items-center my-2">
                        <span class="fs-12 fw-medium">Phí vận chuyển</span>
                        <span class="fs-14 fw-medium">
                            {{ formatPrice(Number(order.shipping_fee)) }}
                        </span>
                    </div>
                    <div v-if="order.shipping_discount" class="d-flex justify-content-between align-items-center my-2">
                        <span class="fs-12 fw-medium">Ưu đãi phí vận chuyển</span>
                        <span class="fs-14 fw-medium">
                            - {{ formatPrice(Number(order.shipping_discount)) }}
                        </span>
                    </div>
                    <div v-if="order.platform_discount" class="d-flex justify-content-between align-items-center my-2">
                        <span class="fs-12 fw-medium">Voucher từ EZShop</span>
                        <span class="fs-14 fw-medium">
                            - {{ formatPrice(Number(order.platform_discount)) }}
                        </span>
                    </div>
                    <div v-if="order.shop_discount" class="d-flex justify-content-between align-items-center my-2">
                        <span class="fs-12 fw-medium">Voucher từ Shop</span>
                        <span class="fs-14 fw-medium">
                            - {{ formatPrice(Number(order.shop_discount)) }}
                        </span>
                    </div>
                </div>
                <div class="border-top pt-3">
                    <div class="d-flex justify-content-end align-items-center mb-2">
                        <span class="me-2 fw-medium">Tổng đơn hàng:</span>
                        <span v-if="order.total_refund"
                            class="text-secondary fw-medium fs-18 text-decoration-line-through">
                            {{ formatPrice(Number(order.total_amount)) }}
                        </span>
                        <span v-else class="text-danger fw-medium fs-18">
                            {{ formatPrice(Number(order.total_amount)) }}
                        </span>
                    </div>
                    <div v-if="order.total_refund > 0" class="d-flex justify-content-end align-items-center mb-2">
                        <span class="me-2 fw-medium">Tổng tiền hoàn lại:</span>
                        <span class="text-danger fw-medium fs-18">
                            {{ formatPrice(Number(order.total_refund)) }}
                        </span>
                    </div>
                    <div v-if="order.order_status == 7 && order.refund_status == 'rejected_by_shop'"
                        class="d-flex justify-content-end align-items-center mb-2">
                        <button @click="updateRefund(order.refund_id, 'escalated')"
                            class="white-btn px-3 py-1 fs-14 text-center d-flex justify-content-center align-items-center"
                            style="width: 160px; height: 33px;">
                            <loading__loader_circle v-if="loading_refund" size="20px" border="3px" />
                            <span v-else>Khiếu Nại CSKH</span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="p-2 bg-white mx-1 mt-2 shadow-sm">
                <div class="d-flex align-items-center justify-content-between w-100 pb-2">
                    <h3 class="fw-bold fs-14">Mã đơn hàng</h3>
                    <div class="d-flex gap-1">
                        <span class="fs-12 text-secondary fw-medium">{{ order.order_id }}</span>
                        <button class="white-btn fs-12 px-1 fw-medium">SAO CHÉP</button>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between w-100 pb-2">
                    <p class="m-0 fs-14 fw-medium">Phương thức thanh toán</p>
                    <p class="m-0 fs-14 fw-medium">{{ order.payment_method }}</p>
                </div>
            </div>
        </div>
    </div>
</template>