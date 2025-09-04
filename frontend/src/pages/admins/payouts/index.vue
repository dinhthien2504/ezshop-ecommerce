<template>
    <div class="admin__content">
        <div
            class="p-3 bg-white border-bottom text-grey fs-14 fw-medium d-flex align-items-center justify-content-between">
            <p>
                Các Lệnh Yêu Cầu Rút Tiền
            </p>
            <div class="d-flex align-items-center gap-2">
                <div class="dropdown">
                    <button type="button" class="btn btn-light border dropdown-toggle radius-2 fw-medium"
                        data-bs-toggle="dropdown">
                        Trạng Thái
                    </button>
                    <ul class="dropdown-menu radius-2">
                        <li @click="setStatus('pending')">
                            <a class="dropdown-item fw-medium cursor-pointer"
                                :class="{ active: selectedStatus === 'pending' }">
                                Chờ xử lý
                            </a>
                        </li>
                        <li @click="setStatus('completed')">
                            <a class="dropdown-item fw-medium cursor-pointer"
                                :class="{ active: selectedStatus === 'completed' }">
                                Hoàn tất
                            </a>
                        </li>
                        <li @click="setStatus('failed')">
                            <a class="dropdown-item fw-medium cursor-pointer"
                                :class="{ active: selectedStatus === 'failed' }">
                                Thất bại
                            </a>
                        </li>
                        <li @click="setStatus('rejected')">
                            <a class="dropdown-item fw-medium cursor-pointer"
                                :class="{ active: selectedStatus === 'rejected' }">
                                Từ chối
                            </a>
                        </li>
                    </ul>

                </div>

                <div class="dropdown">
                    <button type="button" class="btn btn-light border dropdown-toggle radius-2 fw-medium"
                        data-bs-toggle="dropdown">
                        Hiển Thị
                    </button>
                    <ul class="dropdown-menu radius-2">
                        <li v-for="option in [1, 10, 25, 50, 100, 500]" :key="option" @click="setPerPage(option)">
                            <a
                                :class="['dropdown-item fw-medium cursor-pointer', { active: filters.per_page === option }]">
                                {{ option }}
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="admin__search">
                    <button type="button" class="search__button">
                        <i class="ri-search-2-line"></i>
                    </button>
                    <form @submit.prevent="onSearch()" class="d-flex align-items-center gap-2">
                        <div class="d-flex align-items-center gap-1 position-relative">
                            <input type="text" v-model="filters.search" placeholder="Tìm kiếm lệnh theo mã"
                                class="form-control" />
                            <button v-if="filters.search" @click="clearSearch" type="button"
                                class="btn position-absolute top-50 end-0 translate-middle-y me-2 p-0 border-0 bg-transparent">
                                <i class="ri-close-line"></i>
                            </button>
                        </div>
                        <button type="submit" style="height: 38px" class="main-btn py-1 px-3 ">
                            <i class="ri-search-2-line"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div v-if="loading.fetch" style="height: 60vh;" class="d-flex align-items-center justify-content-center">
            <loading_dot size="40px" border="4px" />
        </div>
        <div v-else class="p-10 fade-in">
            <no_data v-if="payouts.length === 0" />
            <div v-else class="table-responsive">
                <table class="table border">
                    <thead class="table-active">
                        <tr class="lh-lg align-middle">
                            <th scope="col" class="fs-14 fw-semibold min-w-150">Mã Thanh Toán</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-150">Tên Cửa Hàng</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-100">Số Tiền</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-100">Trạng Thái</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-150 text-center">Ngày Yêu Cầu</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-150 text-center">Ngày Xử Lý</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-150 text-center">Người Duyệt</th>
                            <th scope="col" class="fs-14 fw-semibold text-center min-w-100">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="p in payouts">
                            <td class="align-middle">
                                <p class="fs-14 fw-medium">{{ p.payout_code }}</p>
                            </td>
                            <td class="align-middle fs-14">
                                <p>{{ p.shop.shop_name }}</p>
                            </td>
                            <td class="align-middle fs-14 fw-medium">
                                <p>{{ formatPrice(p.net_amount) }}</p>
                            </td>
                            <td class="align-middle">
                                <p class="fs-14 fw-medium" :class="statusColor(p.payout_status)">
                                    {{ statusLabel(p.payout_status) }}
                                </p>
                            </td>
                            <td class="align-middle text-center">
                                <p class="fs-14 fw-medium">
                                    {{ p.requested_at }}
                                </p>
                            </td>
                            <td class="align-middle text-center">
                                <p class="fs-14 fw-medium">
                                    {{ p.processed_at ?? '-' }}
                                </p>
                            </td>
                            <td class="align-middle text-center">
                                <p class="fs-14 fw-medium">
                                    {{ p.processed_by_user?.user_name ?? '-' }}
                                </p>
                            </td>
                            <td class="align-middle text-center">
                                <div class="more-wrapper">
                                    <button class="more-btn">
                                        <i class="ri-more-fill"></i>
                                    </button>
                                    <div class="more-popup">
                                        <button v-if="p.payout_status != 'completed' && p.approval_status != 'rejected'"
                                            @click="confirmHandlePayout(p)" class="text-white bg-success">
                                            Duyệt
                                            <i class="ri-checkbox-circle-line"></i>
                                        </button>
                                        <button v-if="p.payout_status != 'completed' && p.approval_status != 'rejected'"
                                            @click="confirmHandlePayout(p, true)" class="bg-warning">
                                            Không duyệt
                                            <i class="ri-close-circle-line"></i>
                                        </button>
                                        <button v-if="p.payout_status == 'completed' || p.approval_status == 'rejected'"
                                            @click="confirmHandlePayout(p, false, true)" class="bg-info">
                                            Kiểm tra
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <pagination :meta="meta" @changePage="onChangePage" />
            </div>
        </div>
    </div>
    <div v-if="payout" class="modal-background container-fluid p-0 d-flex justify-content-center align-items-center">
        <div class="modal-edit-address modal-ezeshop bg-white shadow-sm px-4 pt-4 pb-2">
            <div class="modal-ezeshop__top d-flex justify-content-between align-items-center">
                <div class="modal-ezeshop__name fs-18 fw-500">
                    {{ isPreviewing ? 'Xem Lại' : 'Kiểm Tra Thông Tin Chuyển Khoản' }}
                </div>
                <div @click="payout = null" class="modal-ezeshop__cancel cursor-pointer">
                    <i class="fa-solid fa-xmark fs-18"></i>
                </div>
            </div>
            <!-- Content -->
            <div class="modal-ezeshop__main w-100 px-1 py-2 border-top" style="max-height: 500px;">
                <form id="handle_form_payout"
                    @submit.prevent="isRejecting ? rejectedPayouts(payout.id) : processPayouts(payout.id)">
                    <div class="row g-0">
                        <!-- Mã yêu cầu -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Mã yêu cầu:</label>
                            <div>{{ payout.payout_code }}</div>
                        </div>

                        <!-- Ngày yêu cầu -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Ngày yêu cầu:</label>
                            <div>{{ payout.requested_at }}</div>
                        </div>

                        <!-- Ngày xử lý -->
                        <div v-if="payout.processed_at" class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Ngày xử lý:</label>
                            <div>{{ payout.processed_at }}</div>
                        </div>

                        <!-- Xử lý bởi -->
                        <div v-if="payout.processed_by_user" class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Xử lý bởi:</label>
                            <div> {{ payout.processed_by_user?.user_name ?? '-' }}</div>
                        </div>

                        <!-- Tên shop -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Tên shop:</label>
                            <div>{{ payout.shop.shop_name }}</div>
                        </div>

                        <!-- Chủ tài khoản -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Chủ tài khoản:</label>
                            <div>{{ payout.shop.bank_account_name }}</div>
                            <div>{{ payout.shop.bank_account_number }}</div>
                        </div>
                        <!-- Ngân hàng -->
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold me-2">Ngân hàng:</label>
                            <img :src="payout.shop.bank_logo" alt="logo bank" height="30" width="50" class="me-1" />
                            <div>{{ payout.shop.bank_name }}</div>
                            <div class="d-flex flex-column">

                            </div>
                        </div>
                        <!-- Số tiền thực nhận -->
                        <div class="col-md-12 py-3 border-top text-end">
                            <label class="form-label fw-bold text-success">Số tiền sẽ chuyển:</label>
                            <div class="fs-5 fw-bold text-success">
                                {{ Number(payout.net_amount).toLocaleString() }} đ
                            </div>
                        </div>
                    </div>
                    <div v-if="payout.reject_reason" class="mb-3 bg-danger p-2 text-white">
                        <label for="comment">Lý do từ chối:</label>
                        <p class="fs-16 fw-medium">{{ payout.reject_reason }}</p>
                    </div>
                    <div v-if="isRejecting" class="mb-3">
                        <label for="comment">Lý do từ chối:</label>
                        <textarea v-model="reason" class="form-control" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-ezeshop__bottom d-flex justify-content-end w-100 align-items-center border-top">
                <div class="modal-ezeshop__cancel me-4">
                    <button @click="payout = null" class="white-btn py-2 px-4 fs-14">
                        {{ !isPreviewing ? 'Hủy' : 'Đóng' }}
                    </button>
                </div>
                <div class="modal-ezeshop__save">
                    <button v-if="!isPreviewing" form="handle_form_payout" :disabled="loading.handle_form"
                        style="width: 200px; height: 37px;"
                        class="main-btn py-2 fs-14 d-flex align-items-center justify-content-center" type="submit">
                        <loading_loader_circle v-if="loading.handle_form" size="15px" color="#fff" border="2px" />
                        <span v-else-if="isRejecting">Xác Nhận Từ Chối Duyệt</span>
                        <span v-else>Xác Nhận Chuyển Tiền</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import { onMounted, ref } from 'vue'
import api from '@/configs/api'
import { formatPrice } from '@/utils/formatPrice'
import { payoutStatusMap } from '@/utils/payoutStatusMap'
import message from '@/utils/messageState'
import loading_loader_circle from '@/components/loading/loading__loader-circle.vue'
import loading_dot from '@/components/loading/loading__dot.vue'
import no_data from '@/components/no_data.vue'
import pagination from '@/components/pagination.vue'

const payouts = ref([])
const payout = ref(null)
const isRejecting = ref(false)
const isPreviewing = ref(false)
const reason = ref(null)
const loading = ref({
    fetch: false,
    handle_form: false
})

const filters = ref({
    search: null,
    payout_status: "pending",
    approval_status: "pending",
    per_page: 10,
    page: 1
})

const fetchPayouts = async () => {
    filters.value.page = currentPage.value;
    try {
        const res = await api.get('payouts', {
            params: filters.value
        })
        payouts.value = res.data.data
        meta.value = res.data.meta
    } catch (error) {
        console.log(error);
    }
}

const processPayouts = async (id) => {
    try {
        loading.value.handle_form = true
        const res = await api.post(`payouts/${id}/process`)
        if (res.status == 200) {
            const inx = payouts.value.findIndex(p => p.id === id)
            if (inx !== -1) {
                payouts.value[inx] = res.data.payout
                message.emit('show-message', { title: "Thông Báo", text: res.data.message || "Xử lý thành công", type: "success" })
            }
        }
    } catch (error) {
        console.log(error);
        message.emit('show-message', { title: "Thông Báo", text: error.response.data.message || error.message, type: "error" })
    } finally {
        loading.value.handle_form = false
        payout.value = null
    }
}

const rejectedPayouts = async (id) => {
    try {
        if (!reason.value) {
            message.emit('show-message', { title: "Thông Báo", text: "Vui lòng điền lý do không duyệt.", type: "warning" })
            return
        }
        loading.value.handle_form = true
        const res = await api.post(`payouts/${id}/reject`, {
            reason: reason.value
        })
        payouts.value = payouts.value.filter(p => p.id !== id)
        message.emit('show-message', { title: "Thông Báo", text: res.data.message || "Xử lý thành công", type: "success" })
    } catch (error) {
        console.log(error);
        message.emit('show-message', { title: "Thông Báo", text: error.response.data.message || error.message, type: "error" })
    } finally {
        loading.value.handle_form = false
        payout.value = null
    }
}

const confirmHandlePayout = (Payout, IsRejecting = false, Preview = false) => {
    payout.value = Payout
    isRejecting.value = IsRejecting
    isPreviewing.value = Preview
}

const statusLabel = (status) => {
    return payoutStatusMap[status]?.label || 'Không rõ';
};

const statusColor = (status) => {
    return `text-${payoutStatusMap[status]?.color || 'secondary'}`;
};

const selectedStatus = ref('pending')
const setStatus = (status) => {
    selectedStatus.value = status;

    // Reset bộ lọc
    filters.value.approval_status = null;
    filters.value.payout_status = null;
    switch (status) {
        case 'pending':
            filters.value.approval_status = 'pending';
            filters.value.payout_status = 'pending';
            break;
        case 'completed':
            filters.value.approval_status = 'approved';
            filters.value.payout_status = 'completed';
            break;
        case 'failed':
            filters.value.approval_status = 'approved';
            filters.value.payout_status = 'failed';
            break;
        case 'rejected':
            filters.value.approval_status = 'rejected';
            break;
    }

    fetchPayouts()
}

const setPerPage = (option) => {
    filters.value.per_page = option;
    fetchPayouts()
}
const meta = ref({})
const currentPage = ref(1)
const onChangePage = (page) => {
    currentPage.value = page
    fetchPayouts()
}

const onSearch = () => {
    currentPage.value = 1
    filters.value.payout_status = null
    filters.value.approval_status = null
    fetchPayouts()
}
const clearSearch = () => {
    filters.value.search = null
    fetchPayouts()
}

onMounted(async () => {
    loading.value.fetch = true
    await fetchPayouts()
    loading.value.fetch = false
})
</script>
<style scoped>
.dropdown .active {
    background-color: var(--text-color);
    color: var(--white-color) !important;
}
</style>