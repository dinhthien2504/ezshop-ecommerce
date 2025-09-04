<script setup>
import { computed, onMounted, ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/configs/api'
import orderStatus from '@/components/orderStatus.vue'
import loading__dot from '@/components/loading/loading__dot.vue'
import loading__loader_circle from '@/components/loading/loading__loader-circle.vue'
import message from '@/utils/messageState'
import useGoTo from '@/utils/useGoto'
import { formatPrice } from '@/utils/formatPrice'
const { goTo } = useGoTo()
const loading = ref({
    fetch: false,
    cancel: false,
    feedback: false,
    comfirm_order: false,
    confirm_refund: false,
    calculate_refund: false
})
const route = useRoute()
const statusOrder = [
    { value: 0, label: 'Tất Cả' },
    { value: 1, label: 'Chờ Xác Nhận' },
    { value: 2, label: 'Đã Xác Nhận' },
    { value: 3, label: 'Chờ Giao Hàng' },
    { value: 4, label: 'Đã Giao Hàng' },
    { value: 5, label: 'Hoàn Thành' },
    { value: 6, label: 'Đã Hủy' },
    { value: 7, label: 'Trả Hàng/Hoàn Tiền' }
]

const isModalCancel = ref(false)
const order_id = ref(null)
const openModalCancel = (orderId) => {
    order_id.value = orderId
    isModalCancel.value = true
}
const cancelOrderStatus = async () => {
    const orderId = order_id.value
    try {
        loading.value.cancel = true
        const res = await api.post(`/orders/${orderId}/update-status`, {
            status: 6
        })
        if (res.status == 200) {
            const inx = filteredOrders.value.findIndex((item) => item.order_id == orderId)
            if (inx != -1) {
                filteredOrders.value[inx].order_status = res.data.order_status
                message.emit("show-message", {
                    title: "Thông báo",
                    text: 'Đã hủy đơn hàng.',
                    type: "success"
                })
            }
            if (route.query.type !== 0) {
                filteredOrders.value = filteredOrders.value.filter((item) => item.order_id != orderId)
            }
        }
    } catch (error) {
        console.log(error);
    } finally {
        isModalCancel.value = false
        loading.value.cancel = false
    }
}

const confirmOrderReceived = async (orderId) => {
    try {
        loading.value.comfirm_order = true
        const res = await api.post(`/orders/${orderId}/update-status`, {
            status: 5
        })
        if (res.status == 200) {
            const inx = filteredOrders.value.findIndex((item) => item.order_id == orderId)
            if (inx !== -1) {
                filteredOrders.value[inx].order_status = res.data.order_status
            }
            if (route.query.type !== 0 || !route.query.type) {
                filteredOrders.value = filteredOrders.value.filter((item) => item.order_id != orderId)
            }
            await api.post("/rank/update-user-rank");
            message.emit("show-message", {
                title: "Thông báo",
                text: 'Bạn đã xác nhận đã nhận được hàng.',
                type: "success"
            })
        }

    } catch (error) {
        console.log(error);
    } finally {
        loading.value.comfirm_order = false
    }
}

const orders = ref([])
const filteredOrders = ref([])
const search = ref('');
const getOrders = async () => {
    try {
        loading.value.fetch = true
        const res = await api.get('/orders/user')
        if (res.status === 200) {
            orders.value = res.data.orders
            await filterOrdersByQuery()
        }
    } catch (error) {
        console.log(error)
    } finally {
        loading.value.fetch = false
    }
}

const filterOrdersByQuery = () => {
    const type = Number(route.query.type || 0)
    const keyword = (search.value || '').toLowerCase()
    let results = orders.value
    if (type !== 0) {
        results = results.filter(order => order.order_status === type)
    }

    if (keyword) {
        results = results.filter(order => {
            const shopName = order.shop_name?.toLowerCase() || ''
            const orderId = String(order.order_id || '')
            const productNames = order.order_details?.map(p => p.product_name.toLowerCase()).join(' ') || ''

            return (
                shopName.includes(keyword) ||
                orderId.includes(keyword) ||
                productNames.includes(keyword)
            )
        })
    }

    filteredOrders.value = results
}

const showModalRating = ref(false)
const order = ref({})
const feedbacks = ref({})

const ratingLabel = (order_detail_id) => {
    const rating = feedbacks.value[order_detail_id]?.rating || 0;
    return ['Tệ', 'Không hài lòng', 'Bình thường', 'Hài lòng', 'Tuyệt vời'][rating - 1] || '';
}

const submitReview = async () => {
    try {
        loading.value.feedback = true
        const res = await api.post('/rates', {
            feedbacks: feedbacks.value
        })
        if (res.status == 200) {
            const order_detail_ids = Object.keys(feedbacks.value).map(id => Number(id));
            order.value.order_details.forEach(detail => {
                if (order_detail_ids.includes(detail.order_detail_id)) {
                    detail.is_reviewed = 1;
                }
            })
            message.emit("show-message", {
                title: "Thông báo",
                text: res.data.message,
                type: "success"
            })
        }
    } catch (error) {
        console.log(error);
    } finally {
        showModalRating.value = false
        loading.value.feedback = false
    }
}

const openModalRating = (data_order) => {
    order.value = data_order
    data_order.order_details.forEach(pro => {
        feedbacks.value[pro.order_detail_id] = {
            order_detail_id: pro.order_detail_id,
            rating: 5,
            review: '',
            images: []
        }
    })
    showModalRating.value = true
}

const handleFileUpload = (event, order_detail_id, target = 'feedbacks', field = 'images') => {
    const files = event.target.files;
    if (!files.length) return;

    const storage = target === 'feedbacks' ? feedbacks : refunds;
    const remainingSlots = 5 - storage.value[order_detail_id][field].length;
    if (remainingSlots <= 0) return;

    const limitedFiles = Array.from(files).slice(0, remainingSlots);

    for (const file of limitedFiles) {
        if (!file.type.startsWith('image/')) continue;

        const reader = new FileReader();
        reader.onload = (e) => {
            storage.value[order_detail_id][field].push(e.target.result);
        };
        reader.readAsDataURL(file);
    }

    event.target.value = '';
}

const removeImage = (index, order_detail_id) => {
    feedbacks.value[order_detail_id].images.splice(index, 1)
}

const hasSelected = computed(() => {
    return Object.values(refunds.value).some(r => r.selected)
})
const goToShop = (shop_id, shop_name) => {
    if (shop_id && shop_name) {
        const shopSlug = shop_name
            .toLowerCase()
            .replace(/\s+/g, '-')
            .replace(/[^a-z0-9-]/g, '')

        goTo({
            name: 'shop-profile',
            params: {
                slug: shopSlug,
                id: shop_id
            }
        })
    }
}

// ORDER REFUNDS
const showModalRefund = ref(false)
const refunds = ref({}) // giống feedbacks

const openModalRefund = (data_order) => {
    order.value = data_order

    order.value.order_details.forEach((pro) => {
        refunds.value[pro.order_detail_id] = {
            selected: false,
            qty: pro.quantity,
            variant_id: pro.variant_id,
            reason_type: "seller_fault",
            reason_detail: "",
            evidences: [],
        };
    });
    showModalRefund.value = true
}

// Xóa ảnh minh chứng
const removeEvidence = (index, orderDetailId) => {
    refunds.value[orderDetailId].evidences.splice(index, 1);
}
const submitRefund = async () => {
    // Validate trước
    for (const [orderDetailId, refund] of Object.entries(refunds.value)) {
        console.log(refund.selected);

        if (refund.selected) {
            if (!refund.reason_detail || refund.reason_detail.trim() === "") {
                message.emit("show-message", {
                    title: "Lỗi",
                    text: "Vui lòng nhập lý do chi tiết cho sản phẩm cần hoàn trả.",
                    type: "error"
                })
                return
            }
            if (!refund.evidences || refund.evidences.length === 0) {
                message.emit("show-message", {
                    title: "Lỗi",
                    text: "Vui lòng thêm ít nhất 1 ảnh minh chứng cho sản phẩm cần hoàn trả.",
                    type: "error"
                })
                return
            }
        }
    }

    const payload = {
        order_id: order.value.order_id,
        address_id: order.value.address_id,
        total_refund: refundAmount.value.total_refund,
        items: []
    }

    refundAmount.value.refund_items.forEach(item => {
        // Lấy thông tin chi tiết từ kết quả calculate
        payload.items.push({
            order_detail_id: item.order_detail_id,
            variant_id: item.variant_id,
            qty: item.qty,
            price_original: item.price_original,
            subtotal: item.subtotal,
            discount_allocated: item.discount_allocated,
            refund_amount: item.refund_amount,
            shipping_fee: item.shipping_fee,
            shipping_payer: item.shipping_payer,

            // Gắn thêm lý do và chứng cứ từ form người dùng
            reason_type: refunds.value[item.order_detail_id]?.reason_type,
            reason_detail: refunds.value[item.order_detail_id]?.reason_detail,
            evidences: refunds.value[item.order_detail_id]?.evidences || []
        })
    })
    try {
        loading.value.confirm_refund = true
        const res = await api.post('refunds', payload);
        if (res.status == 201) {
            //Tìm ra đơn hàng muốn trả
            const inx = filteredOrders.value.findIndex((item) => item.order_id == order.value.order_id)
            filteredOrders.value[inx].order_status = 7;
            filteredOrders.value[inx].refund = res.data
            console.log(filteredOrders.value[inx]);
            if (route.query.type != 0) {
                filterOrdersByQuery()
            }


            message.emit("show-message", {
                title: "Thông báo",
                text: "Đã gửi yêu cầu trả hàng hoàn tiền.",
                type: "success"
            })
        }
    } catch (e) {
        console.error("Submit refund error:", e);
    } finally {
        loading.value.confirm_refund = false
        showModalRefund.value = false
    }
};

const refundAmount = ref({}) // Lưu kết quả tính toán từng món

const calculateRefundAmount = async () => {
    const payload = {
        order_id: order.value.order_id,
        items: [],
    };

    Object.keys(refunds.value).forEach((orderDetailId) => {
        const r = refunds.value[orderDetailId];
        if (r.selected && r.qty > 0) {
            payload.items.push({
                order_detail_id: Number(orderDetailId),
                variant_id: r.variant_id,
                qty: r.qty,
                reason_type: r.reason_type
            })
        }
    })
    try {
        loading.value.calculate_refund = true
        const res = await api.post('refund_items/calculate', payload)
        if (res.status == 200) {
            refundAmount.value = res.data
        }
    } catch (error) {
        console.error(error);
    } finally {
        loading.value.calculate_refund = false
    }
}

onMounted(async () => {
    await getOrders()
})

watch(() => route.query.type, () => {
    filterOrdersByQuery()
})
</script>
<template>
    <div class="profile__right overflow-hidden">
        <div>
            <div class="d-flex flex-row text-center mx-1 mt-lg-3 scroll-hidden shadow-sm">
                <router-link :to="{
                    name: 'order-history',
                    query: { type: s.value }
                }" v-for="s in statusOrder"
                    :class="{ 'menu--order': true, 'active': !route.query.type && s.value == 0 || route.query.type == s.value }">
                    {{ s.label }}
                </router-link>
            </div>
            <!-- Search Order -->
            <form v-if="!route.query.type || route.query.type == 0" @submit.prevent="filterOrdersByQuery"
                class="search--order m-1 my-2">
                <button type="submit">
                    <i class="ri-search-2-line"></i>
                </button>
                <input v-model="search" class="w-100 fs-14" autocomplete="off"
                    placeholder="Bạn có thể tìm kiếm theo tên Shop, ID đơn hàng hoặc Tên Sản phẩm" value="">
            </form>
            <div v-if="loading.fetch"
                class="d-flex align-items-center justify-content-center p-4 text-center flex-column"
                style="min-height: 500px;">
                <loading__dot />
                <p class="mt-2">Đang tải dữ liệu đơn hàng...</p>
            </div>
            <div v-else>
                <!-- Order -->
                <div v-if="filteredOrders.length > 0" v-for="order in filteredOrders"
                    class="border p-3 bg-white shadow-sm m-1 mb-2">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <i class="ri-home-8-line"></i>
                            <!-- <span class="badge bg-main radius-2">Yêu thích</span> -->
                            <strong @click="goToShop(order.shop_id, order.shop_name)"
                                class="shop-name cursor-pointer">{{ order.shop_name }}</strong>
                            <button @click="goToShop(order.shop_id, order.shop_name)"
                                class="fs-10 fw-medium white-btn py-1 px-2 radius-2 d-none d-lg-block">
                                <i class="ri-home-9-line"></i>
                                Xem Shop
                            </button>
                        </div>
                        <orderStatus :status="order.order_status" />
                    </div>
                    <router-link :to="`/nguoi-dung/don-hang-${order.order_id}`">
                        <div v-if="order.order_details.length > 0" v-for="Dorder in order.order_details"
                            class="d-flex border-top py-3">
                            <img :src="`/imgs/products/${Dorder.image}`" :alt="Dorder.product_name"
                                class="rounded me-3 border radius-2"
                                style="width: 80px; height: 80px; object-fit: contain;">
                            <div class="flex-grow-1">
                                <div class="fw-medium text-truncate-1-line">{{ Dorder.product_name }}</div>
                                <div class="text-muted small mb-1">{{ Dorder.attributes }}</div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="text-muted small">x{{ Dorder.quantity }}</div>
                                    <div class="text-end w-100 text-danger fw-medium">
                                        {{ Number(Dorder.price).toLocaleString('vi-VN') + ' ₫' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </router-link>
                    <div class="border-top pt-3 mt-3">
                        <div class="d-flex justify-content-end align-items-center mb-2">
                            <div class="text-end">
                                <div class="mb-2">
                                    <span class="me-2 fw-medium fs-14">Thành tiền:</span>
                                    <span class="text-danger fw-medium fs-18">
                                        {{ Number(order.total_amount).toLocaleString('vi-VN') + ' ₫' }}</span>
                                </div>
                                <div class="d-flex gap-2 justify-content-end">
                                    <button @click="openModalRating(order)"
                                        v-if="order.order_status == 5 && order.order_details.some(d => d.is_reviewed == 0)"
                                        class="main-btn px-2 py-1 fs-14">
                                        Đánh Giá
                                    </button>
                                    <button v-if="order.order_status == 4" :disabled="loading.confirm_refund"
                                        @click="openModalRefund(order)"
                                        class="white-btn px-2 py-1 fs-14 text-center d-flex justify-content-center">
                                        <loading__loader_circle v-if="loading.confirm_refund" color="#fff" size="20px"
                                            border="3px" />
                                        <span v-else>Y/C Trả Hàng/Hoàn Tiền</span>
                                    </button>
                                    <button @click="confirmOrderReceived(order.order_id)" v-if="order.order_status == 4"
                                        class="main-btn px-2 py-1 fs-14 text-center d-flex justify-content-center"
                                        style="width: 200px;">
                                        <loading__loader_circle v-if="loading.comfirm_order" color="#fff" size="20px"
                                            border="3px" />
                                        <span v-else>Đã Nhận Được Hàng</span>
                                    </button>
                                    <button @click="openModalCancel(order.order_id)" v-if="order.order_status == 1"
                                        class="white-btn px-2 py-1 fs-14">
                                        Hủy Đơn
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-else class="d-flex align-items-center justify-content-center bg-white mt-2"
                    style="min-height: 500px;">
                    <div class="text-center">
                        <img style="width: 100px;" src="/imgs/empty-order.png" alt="Chưa Có Đơn Hàng">
                        <h2 class="fs-18 mt-2">Chưa có đơn hàng</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div v-if="isModalCancel"
        class="modal-background container-fluid p-0 d-flex justify-content-center align-items-center">
        <div class=" modal-ezeshop bg-white shadow-sm px-4 pt-4 pb-2" style="max-width: 500px">
            <div class="modal-ezeshop__top d-flex justify-content-between align-items-center">
                <div class="modal-ezeshop__name fs-18 fw-500">Xác Nhận Hủy Đơn Hàng</div>
                <div @click="isModalCancel = false" class="modal-ezeshop__cancel cursor-pointer">
                    <i class="fa-solid fa-xmark fs-18"></i>
                </div>
            </div>
            <!-- Content -->
            <div class="modal-ezeshop__main w-100">
                Bạn có chắc chắn hủy đơn hàng này?
            </div>
            <div class="modal-ezeshop__bottom d-flex justify-content-end w-100 align-items-center border-top">
                <div class="modal-ezeshop__cancel me-4">
                    <button @click="isModalCancel = false" class="white-btn py-2 px-4 fs-14">Huỷ</button>
                </div>
                <div class="modal-ezeshop__save">
                    <button type="button" class="main-btn fs-14 d-flex align-items-center justify-content-center"
                        @click="cancelOrderStatus()" style="width: 100px; height: 38.6px;">
                        <loading__loader_circle v-if="loading.cancel" border="3px" size="20px" color="#fff" />
                        <span v-else>Xác Nhận</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Model Confirm Cancel-->
    <!-- Modal Show Rating-->
    <div class="modal fade" :class="{ show: showModalRating }" style="display: block;" v-if="showModalRating"
        tabindex="-1">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content radius-2 border-0 shadow-sm">
                <div class="modal-header">
                    <h5 class="modal-title fs-20">Đánh Giá Sản Phẩm</h5>
                    <button type="button" class="btn-close" @click="showModalRating = false"></button>
                </div>
                <div v-for="pro in order.order_details" class="modal-body">
                    <div class="d-flex mb-3">
                        <img :src="`/imgs/products/${pro.image}`" class="me-3" width="50" height="50" alt="Sản phẩm">
                        <div>
                            <p class="fw-medium fs-14">{{ pro.product_name }}</p>
                            <span class="fs-12">{{ pro.attributes }}</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="fs-14 fw-medium">Chất lượng sản phẩm</label>
                        <div class="rating__stars">
                            <i class="bi"
                                :class="feedbacks[pro.order_detail_id].rating >= 1 ? 'bi-star-fill' : 'bi-star'"
                                @click="feedbacks[pro.order_detail_id].rating = 1"></i>
                            <i class="bi"
                                :class="feedbacks[pro.order_detail_id].rating >= 2 ? 'bi-star-fill' : 'bi-star'"
                                @click="feedbacks[pro.order_detail_id].rating = 2"></i>
                            <i class="bi"
                                :class="feedbacks[pro.order_detail_id].rating >= 3 ? 'bi-star-fill' : 'bi-star'"
                                @click="feedbacks[pro.order_detail_id].rating = 3"></i>
                            <i class="bi"
                                :class="feedbacks[pro.order_detail_id].rating >= 4 ? 'bi-star-fill' : 'bi-star'"
                                @click="feedbacks[pro.order_detail_id].rating = 4"></i>
                            <i class="bi"
                                :class="feedbacks[pro.order_detail_id].rating >= 5 ? 'bi-star-fill' : 'bi-star'"
                                @click="feedbacks[pro.order_detail_id].rating = 5"></i>
                            <span>{{ ratingLabel(pro.order_detail_id) }}</span>
                        </div>
                    </div>

                    <div class="form-box mb-3">
                        <label for="review" class="fs-14">Kinh nghiệm sử dụng</label>
                        <textarea id="review" class="form-control mb-3 redius-2" rows="4"
                            v-model="feedbacks[pro.order_detail_id].review"
                            placeholder="Nhận xét của bạn về sản phẩm"></textarea>

                        <label class="fs-14">Hình ảnh/Video</label>
                        <div class="d-flex flex-wrap gap-2 my-2">
                            <div v-for="(img, index) in feedbacks[pro.order_detail_id].images" :key="index"
                                class="rating__image-preview">
                                <img :src="img" class="image-preview" width="55" height="55" style="object-fit: cover;"
                                    alt="Preview">
                                <button class="rating__remove-btn"
                                    @click="removeImage(index, pro.order_detail_id)">&times;</button>
                            </div>
                            <label v-if="feedbacks[pro.order_detail_id].images.length < 5" class="rating__add-preview">
                                <i class="bi bi-camera"></i>
                                <span>{{ feedbacks[pro.order_detail_id].images.length }}/5</span>
                                <input type="file" class="d-none" multiple
                                    @change="(e) => handleFileUpload(e, pro.order_detail_id, 'feedbacks', 'images')" />
                            </label>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end gap-3 pb-3 px-3">
                    <button class="white-btn py-1 px-2" @click="showModalRating = false">TRỞ LẠI</button>
                    <button class="main-btn d-flex align-items-center justify-content-center"
                        style="width: 130px; height: 35px;" @click="submitReview">
                        <loading__loader_circle v-if="loading.feedback" border="3px" size="20px" color="#fff" />
                        <span v-else>ĐÁNH GIÁ</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Yêu cầu trả hàng/hoàn tiền -->
    <div class="modal fade" :class="{ show: showModalRefund }" style="display: block;" v-if="showModalRefund"
        tabindex="-1">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content radius-2 border-0 shadow-sm">
                <div class="modal-header">
                    <h5 class="modal-title fs-20">Yêu cầu trả hàng / hoàn tiền</h5>
                    <button type="button" class="btn-close white-btn" @click="showModalRefund = false"></button>
                </div>
                <div class="p-3 bg-light">
                    <p class="fs-14 fw-medium">Địa chỉ lấy hàng</p>
                    <div class="d-flex align-items-center gap-1">
                        <i class="fs-14 text-color ri-map-pin-2-fill"></i>
                        <span class="fs-12">{{ order.pickup_address }}</span>
                    </div>
                </div>
                <h3 class="px-3 py-2 fs-20 text-center text-color border-bottom">Tổng tiền hàng: {{
                    formatPrice(order.total_amount) }}</h3>
                <div v-for="pro in order.order_details" :key="pro.order_detail_id" class="modal-body border-bottom">
                    <!-- Thông tin sản phẩm -->
                    <div class="d-flex mb-3 align-items-center">
                        <!-- <input type="checkbox" class="form-check-input me-2"/> -->
                        <div class="checkbox">
                            <label class="checkbox-label">
                                <input type="checkbox" v-model="refunds[pro.order_detail_id].selected" />
                                <div class="checkbox-wrapper">
                                    <div class="checkbox-bg"></div>
                                    <svg fill="none" viewBox="0 0 24 24" class="checkbox-icon">
                                        <path stroke-linejoin="round" stroke-linecap="round" stroke-width="3"
                                            stroke="currentColor" d="M4 12L10 18L20 6" class="check-path">
                                        </path>
                                    </svg>
                                </div>
                            </label>
                        </div>
                        <!--   -->
                        <img :src="`/imgs/products/${pro.image}`" class="me-3" width="50" height="50" alt="Sản phẩm">
                        <div>
                            <p class="fw-medium fs-14 mb-0">{{ pro.product_name }}</p>
                            <span class="fs-12">{{ pro.attributes }}</span>
                            <div class="mt-1">
                                <small>Số lượng: {{ pro.quantity }}</small>
                            </div>
                            <div class="fs-14">Thành tiền:
                                <span class="text-color fs-16">{{ formatPrice(pro.price * pro.quantity) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Chọn lý do trả -->
                    <div class="mb-3">
                        <!-- v-if="refunds[pro.order_detail_id].selected"  -->
                        <label class="fs-14 fw-medium">Lý do</label>
                        <select class="form-select radius-2 form-control"
                            v-model="refunds[pro.order_detail_id].reason_type">
                            <!--  -->
                            <option value="seller_fault">Shop gửi sai hàng / thiếu hàng</option>
                            <option value="shipping_fault">Hàng bị hư hỏng khi vận chuyển</option>
                            <option value="buyer_fault">Tôi đặt nhầm, muốn đổi trả</option>
                            <option value="other">Khác</option>
                        </select>
                    </div>
                    <div class="mb-2" v-if="refunds[pro.order_detail_id].selected">
                        <p v-if="refunds[pro.order_detail_id].reason_type == 'buyer_fault'"
                            class="fs-14 fw-medium text-color">
                            Bạn chịu tiền ship hoàn về
                        </p>
                        <p v-if="refunds[pro.order_detail_id].reason_type == 'seller_fault'"
                            class="fs-14 fw-medium text-color">
                            Shop chịu tiền ship hoàn về
                        </p>
                        <p v-if="refunds[pro.order_detail_id].reason_type == 'shipping_fault'"
                            class="fs-14 fw-medium text-color">
                            Sàn hỗ trợ tiền ship hoàn về
                        </p>
                        <p v-if="refunds[pro.order_detail_id].reason_type === 'other'"
                            class="fs-14 fw-medium text-color">
                            Vui lòng chọn bên chịu phí ship hoàn về:
                        </p>
                        <select v-if="refunds[pro.order_detail_id].reason_type === 'other'"
                            v-model="refunds[pro.order_detail_id].shipping_responsible"
                            class="form-select radius-2 form-control">
                            <option value="buyer">Bạn</option>
                            <option value="seller">Shop</option>
                            <option value="platform">Sàn</option>
                        </select>

                    </div>
                    <!-- Mô tả chi tiết -->
                    <div class="mb-3">
                        <!-- v-if="refunds[pro.order_detail_id].selected"  -->
                        <label class="fs-14">Mô tả chi tiết</label>
                        <textarea class="form-control" rows="3" v-model="refunds[pro.order_detail_id].reason_detail"
                            placeholder="Mô tả chi tiết vấn đề sản phẩm..."></textarea>
                        <!-- -->
                    </div>

                    <!-- Ảnh minh chứng -->
                    <div class="mb-3" v-if="refunds[pro.order_detail_id].selected">
                        <label class="fs-14">Ảnh minh chứng</label>
                        <div class="d-flex flex-wrap gap-2 my-2">
                            <div v-for="(img, index) in refunds[pro.order_detail_id].evidences" :key="index"
                                class="rating__image-preview">
                                <img :src="img" class="image-preview" width="55" height="55" style="object-fit: cover;"
                                    alt="Preview">
                                <button class="rating__remove-btn"
                                    @click="removeEvidence(index, pro.order_detail_id)">&times;</button>
                            </div>
                            <label v-if="refunds[pro.order_detail_id].evidences.length < 5" class="rating__add-preview">
                                <i class="bi bi-camera"></i>
                                <span>{{ refunds[pro.order_detail_id].evidences.length }}/5</span>
                                <input type="file" class="d-none" multiple
                                    @change="(e) => handleFileUpload(e, pro.order_detail_id, 'refunds', 'evidences')" />
                            </label>
                        </div>
                    </div>
                </div>
                <div v-if="hasSelected && refundAmount.total_refund" class="p-3 text-center bg-light">
                    <p class="fs-16 fw-medium">Tiền bạn nhận được là:
                        <span class="fs-20 text-color">
                            {{ formatPrice(refundAmount.total_refund) }}
                        </span>
                    </p>
                </div>
                <!-- Footer -->
                <div class="d-flex justify-content-end gap-3 pb-3 px-3 mt-3">
                    <button class="main-btn py-1 px-2 fs-14" @click="showModalRefund = false">TRỞ LẠI</button>
                    <button class="white-btn d-flex align-items-center justify-content-center fs-14"
                        :disabled="loading.calculate_refund || !hasSelected" @click="calculateRefundAmount()"
                        style="width: 140px; height: 35px;">
                        <loading__loader_circle v-if="loading.calculate_refund" border="3px" size="20px" />
                        <span v-else>TÍNH TIỀN NHẬN</span>
                    </button>
                    <button class="white-btn d-flex align-items-center justify-content-center fs-14"
                        style="width: 150px; height: 35px;" @click="submitRefund"
                        :disabled="!hasSelected || !refundAmount.total_refund">
                        <loading__loader_circle v-if="loading.confirm_refund" border="3px" size="20px" />
                        <span v-else>GỬI YÊU CẦU</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

</template>
<style>
.rating__stars i {
    color: #faca51;
    font-size: 20px;
    cursor: pointer;
    margin: 0 4px;
}

.rating__stars span {
    font-size: 16px;
    margin-left: 10px;
    color: #0000008A;
    font-weight: 500;
}

.form-box {
    background: #f9f9f9;
    padding: 1rem;
    border-radius: 8px;
}

.rating__image-preview {
    object-fit: cover;
    position: relative;
    background-color: white;
}

.rating__add-preview {
    display: flex;
    align-items: center;
    flex-direction: column;
    justify-content: center;
    cursor: pointer;
    color: #00000042;
    width: 55px;
    height: 55px;
    border: 1px dotted #00000042;
}

.rating__add-preview span {
    font-size: 12px;
}

.rating__remove-btn {
    position: absolute;
    top: 0px;
    right: 0px;
    background: var(--text-color);
    color: white;
    border: none;
    width: 15px;
    height: 15px;
    font-size: 14px;
    display: flex;
    justify-content: center;
    align-items: center;
    cursor: pointer;
}
</style>