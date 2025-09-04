<template>
    <div class="__main ">
        <div class="__title mt-3 bg-white px-4 d-flex align-items-center border-bottom">
            <span class="fs-14 fw-semibold text-grey">Đơn Hàng</span>
        </div>
        <div class="__content bg-white px-4 py-2 d-flex align-items-center border-bottom shadow-sm">
            <div class="__order-management w-100">
                <div class="__type d-flex flex-wrap">
                    <div class="__item fs-14 fw-semibold text-grey me-4 my-2"
                        :class="{ active: selected_order_status == status.value }" v-for="status in order_status"
                        @click="get_orders(status.value)">{{ status.label }}</div>
                </div>

                <!-- <div class="__task d-flex flex-wrap mt-3">
                    <div class="__search-box me-3">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" name="" id="" placeholder="Tìm kiếm sản phẩm...">
                    </div>

                    <div class="__select me-3">
                        <div class="__selected" @click="sort_drowdown = !sort_drowdown">Mặc định <i
                                class="fa-solid fa-caret-down"></i></div>

                        <div class="__choice bg-white py-2 mt-2 shadow-sm" :class="{ active: sort_drowdown }">
                            <div class="__title">Giá trị đề xuất </div>

                            <div class="__box-item mt-1">
                                <div class="__item">Cao tới thấp</div>
                                <div class="__item">Thấp tới cao</div>
                            </div>
                        </div>
                    </div>

                </div> -->

                <div class="__count fw-500 fs-14 mt-3"> <span>{{ orders.length }}</span> Đơn hàng</div>
                <div v-if="is_loading"
                    class="d-flex align-items-center justify-content-center p-4 text-center flex-column bg-white"
                    style="min-height: 500px;">
                    <loading__dot />
                    <p class="mt-2">Đang tải dữ liệu đơn hàng...</p>
                </div>
                <div v-else class="__order mt-2">
                    <no_data class="fade-in" v-if="orders.length == 0" />
                    <div class="table-responsive table-bordered fade-in" v-else>
                        <table class="table fs-14">
                            <thead class="table-light">
                                <tr class="fw-500 align-middle">
                                    <td scope="col" style="min-width: 320px;">Sản phẩm</td>
                                    <td scope="col" style="min-width: 200px;">Tổng đơn hàng</td>
                                    <td scope="col" style="min-width: 120px;">Trạng thái</td>
                                    <td scope="col" style="min-width: 180px;">Thanh toán</td>
                                    <td scope="col" style="min-width: 110px;">Vận chuyển</td>
                                    <td scope="col" style="min-width: 100px;">Thao tác</td>
                                </tr>
                            </thead>
                            <template v-for="order in orders" :key="order.id">
                                <tbody style="height: 20px;">
                                    <tr class="border-start-0 border-end-0"></tr>
                                </tbody>
                                <tbody style="height: 60px;" class="table-light border-top">
                                    <tr class="border-top">
                                        <td class="align-middle">
                                            <div class="d-flex ">
                                                <i class="fa-solid fa-circle-user fs-24 me-2"></i>
                                                <div class="__shop-name fw-500">{{ order.shop_name }}</div>
                                            </div>
                                        </td>
                                        <td colspan="4"></td>
                                        <td class="fw-semibold align-middle" style="min-width: 150px;">
                                            Mã đơn hàng: #{{ order.order_id }}
                                        </td>
                                    </tr>
                                </tbody>

                                <tbody>
                                    <tr class="align-middle">
                                        <td>
                                            <div class="d-flex">
                                                <img :src="`/imgs/products/${order.order_details[0].image}`" alt=""
                                                    class="__product-img"
                                                    style="max-width: 50px; height: 60px; object-fit: contain;">

                                                <div class="__product-info d-flex flex-column">
                                                    <span class="__product-name fw-semibold">{{
                                                        order.order_details[0].product_name }}</span>
                                                    <span class="__product-sku fs-12 text-grey fw-500 mt-3">SKU: {{
                                                        order.order_details[0].sku }}</span>
                                                    <span class="__product-id fs-12 text-grey fw-500">ID: {{
                                                        order.order_details[0].product_id }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="__total fw-semibold">
                                                {{ Number(order.total_amount).toLocaleString('vi-VN') + 'đ' }}</div>
                                            <div class="__payment-method fw-500 text-grey">{{ order.payment_method.title
                                            }}</div>

                                        </td>
                                        <td class="fw-500" :class="{
                                            'text-success': order.order_status == 2 || order.order_status == 5,
                                            'text-warning': order.order_status == 1,
                                            'text-info': order.order_status == 3,
                                            'text-primary': order.order_status == 4,
                                            'text-danger': order.order_status == 6,
                                            'text-dark': order.order_status == 7
                                        }">{{order_status.find(o => o.value == order.order_status).label}}</td>

                                        <td>
                                            <div class="d-flex">
                                                <div class="__dot-status"
                                                    style="width: 20px; height: 20px; border-radius: 50%;"
                                                    :style="{ backgroundColor: payment_status.find(p => p.value == order.payment_status).color }">
                                                </div>
                                                <div class="__status ms-3">
                                                    {{payment_status.find(p => p.value == order.payment_status).label
                                                    }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="fw-semibold">
                                            GHN
                                        </td>
                                        <td class="fs-12 fw-semibold cursor-pointer">
                                            <span class="text-primary me-1" @click="render_order_details(order)">
                                                Xem
                                            </span>

                                            <!-- Duyệt đơn -->
                                            <span v-if="order.order_status < 4" class="text-success"
                                                @click="update_order_status(order.order_id, order.order_status + 1)">
                                                | Duyệt
                                            </span>
                                            <!-- Duyệt đơn -->
                                            <span
                                                v-if="order.order_status == 5 || (order.refund && order.refund.status == 'processed')"
                                                class="text-success">
                                                | Hoàn thành
                                            </span>

                                            <!-- Đơn hoàn -->
                                            <span v-if="order.order_status == 7 && order.refund.status !== 'processed'"
                                                class="text-warning" @click="render_order_details(order)">
                                                | Hoàn tiền
                                            </span>

                                            <!-- Đơn huỷ -->
                                            <span v-if="order.order_status === 6" class="text-danger">
                                                | Đã huỷ
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </template>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-background container-fluid d-flex justify-content-center align-items-center"
        v-if="is_watching_order">
        <div class="modal-ezeshop bg-white shadow-sm px-4 pt-4 pb-2">
            <!-- Header -->
            <div class="modal-ezeshop__top d-flex justify-content-between align-items-center border-bottom">
                <div class="modal-ezeshop__name fs-18 fw-500">Đơn hàng chi tiết</div>
                <div class="modal-ezeshop__cancel cursor-pointer">
                    <i class="fa-solid fa-xmark fs-18" @click="close_modal"></i>
                </div>
            </div>

            <!-- Main -->
            <div class="modal-ezeshop__main w-100 overflow-y-auto">
                <div class="p-3 bg-white rounded radius-2 m-1" style="overflow-y: auto; height: 75vh;">
                    <!-- Trạng thái -->
                    <div class="d-flex justify-content-end align-items-center mb-2">
                        <div class="d-flex justify-content-end small fw-medium">
                            <span class="fw-semibold" :class="{
                                'text-success': order.order_status == 2 || order.order_status == 5,
                                'text-warning': order.order_status == 1,
                                'text-info': order.order_status == 3,
                                'text-primary': order.order_status == 4,
                                'text-danger': order.order_status == 6,
                                'text-dark': order.order_status == 7
                            }">
                                {{order_status.find(o => o.value == order.order_status).label}}
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
                        <div class="d-flex">
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
                        </div>

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
                                        <img v-for="img in order_detail.refund_items.evidences"
                                            :src="`/imgs/refunds/${img}`" class="rounded border"
                                            style="width: 70px; height: 70px; object-fit: cover;">
                                    </div>
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
                        @click="close_modal">Đóng</button>
                    <button class="main-btn px-2 ms-2" v-if="order.order_status < 4" style="width: 150px; height: 35px;"
                        @click="update_order_status(order.order_id, order.order_status + 1)">
                        Xác Nhận Đơn
                    </button>
                    <button class="white-btn px-2 ms-2 d-flex align-items-center justify-content-center"
                        @click="updateRefund(order.refund.id, 'rejected_by_shop')"
                        v-if="order.order_status == 7 && order.refund.status == 'pending'"
                        style="width: 170px; height: 35px;">
                        <loading__loader v-if="loading_refund.rejected_by_shop" size="20px" border="3px" />
                        <span v-else>Từ Chối Hoàn Tiền</span>
                    </button>
                    <button class="main-btn px-2 ms-2 d-flex align-items-center justify-content-center"
                        @click="updateRefund(order.refund.id, 'approved')"
                        v-if="order.order_status == 7 && order.refund.status == 'pending'"
                        style="width: 170px; height: 35px;">
                        <loading__loader v-if="loading_refund.approved" size="20px" border="3px" color="#fff" />
                        <span v-else>Đồng Ý Hoàn Tiền</span>
                    </button>
                    <button class="main-btn px-2 ms-2 d-flex align-items-center justify-content-center"
                        @click="updateRefund(order.refund.id, 'processed')"
                        v-if="order.order_status == 7 && order.refund.status == 'approved'"
                        style="width: 180px; height: 35px;">
                        <loading__loader v-if="loading_refund.processed" size="20px" border="3px" color="#fff" />
                        <span v-else>Hoàn Tất Hoàn Tiền</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <confirm__popup ref="confirm_popup" />
</template>

<script setup>
import { onMounted, ref } from 'vue';
import api from "@/configs/api"
import message from '@/utils/messageState';
import no_data from '../../../components/no_data.vue';
import loading__dot from '@/components/loading/loading__dot.vue'
import loading__loader from '@/components/loading/loading__loader-circle.vue'
import confirm__popup from '@/components/confirm.vue';
import { formatPrice } from '@/utils/formatPrice.js';
import useGoTo from '@/utils/useGoTo'
const { goTo } = useGoTo()

const confirm_popup = ref(null);
const is_loading = ref(false);

const is_watching_order = ref(false);
const open_modal = () => is_watching_order.value = true;
const close_modal = () => is_watching_order.value = false;

const sort_drowdown = ref(false)

//orders
const orders = ref([]);
const order_status = [
    // { value: 0, label: 'Tất Cả' },
    { value: 1, label: 'Chờ Xác Nhận' },
    { value: 2, label: 'Đã Xác Nhận' },
    { value: 3, label: 'Chờ Giao Hàng' },
    { value: 4, label: 'Đã Giao Hàng' },
    { value: 5, label: 'Hoàn Thành' },
    { value: 6, label: 'Đã Hủy' },
    { value: 7, label: 'Trả Hàng/Hoàn Tiền' }
]

const payment_status = [
    { value: 'unpaid', label: 'Chưa thanh toán', color: 'red' },
    { value: 'paid', label: 'Đã thanh toán', color: 'green' },
    { value: 'refunded', label: 'Đã hoàn tiền', color: 'blue' }
]

const selected_order_status = ref(null)

const get_orders = async (order_status = 1) => {
    try {
        is_loading.value = true;
        selected_order_status.value = order_status;

        const response = await api.get('/orders/shop', {
            params: {
                order_status: order_status
            }
        });
        orders.value = response.data;
    } catch (error) {
        console.error("Error fetching orders:", error);
        message.emit("show-message", { title: "Lỗi", text: "Không thể tải đơn hàng!", type: "error" });
    } finally {
        is_loading.value = false;
    }
};

//order details
const order = ref({})
const render_order_details = (Order) => {
    order.value = Order
    open_modal();
}

//update order status
const update_order_status = async (order_id, status) => {
    const confirm = await confirm_popup.value.open_confirm("Bạn có chắc chắn muốn cập nhật trạng thái đơn hàng này không?");
    if (!confirm) return;

    try {
        const response = await api.post(`/orders/${order_id}/update-status`, { status })
        message.emit("show-message", { title: "Thành công", text: "Cập nhật trạng thái đơn hàng thành công.", type: "success" });

        get_orders(selected_order_status.value);
    } catch (error) {
        console.error("Error updating order status:", error);
        message.emit("show-message", { title: "Lỗi", text: "Cập nhật trạng thái đơn hàng thất bại!", type: "error" });
    } finally {
        close_modal()
    }
};

const reasonMap = {
    seller_fault: "Người bán sai",
    buyer_fault: "Người mua sai",
    shipping_fault: "Đơn vị vận chuyển sai",
    other: "Lý do khác"
};
const statusRefundMap = {
    pending: 'Chờ xử lý',
    approved: 'Đã duyệt',
    rejected_by_shop: 'Shop từ chối',
    escalated: 'Chờ CSKH xử lý',
    rejected_final: 'Từ chối (kết thúc)',
    processed: 'Đã xử lý',
};
const loading_refund = ref({
    rejected_by_shop: false,
    approved: false,
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
        console.log(error);
    } finally {
        loading_refund.value[status] = false;
    }
}
onMounted(() => {
    const shop_id = localStorage.getItem("shop_id");
    if (shop_id == 0) {
        message.emit("show-message", { title: "Thông báo", text: 'Vui lòng đăng ký shop ký để sử dụng được chức năng này.', type: "error" });
        goTo("/kenh-nguoi-ban/dang-ky");
        return
    }
    get_orders();
});
</script>

<style scoped>
.__main>.__title {
    height: 50px;
}

.__main>.__content>.__order-management>.__type>.__item {
    cursor: pointer;
}

.__main>.__content>.__order-management>.__type>.__item.active {
    border-bottom: 2px solid var(--text-color);
    color: var(--text-color);
}

.__main>.__content>.__order-management>.__type>.__item.overtask {
    color: #4199E6;
}

table>thead {
    height: 50px;
}

table>tbody {
    height: 120px;
}
</style>