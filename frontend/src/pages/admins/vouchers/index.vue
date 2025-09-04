<template>
    <div class="admin__content">
        <div
            class="p-3 bg-white border-bottom text-grey fs-14 fw-medium d-flex align-items-center justify-content-between">
            <p class="m-0">
                Quản Lý Mã Giảm Giá
            </p>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <div class="dropdown">
                    <button type="button" class="white-btn px-2 py-1 fs-14 dropdown-toggle-mobile dropdown-toggle radius-2 fw-medium"
                        data-bs-toggle="dropdown">
                        Trạng Thái
                    </button>
                    <ul class="dropdown-menu shadow-sm radius-2">
                        <li @click="setStatus('active')">
                            <a class="dropdown-item fw-medium cursor-pointer"
                                :class="{ active: selectedStatus === 'active' }">
                                Đang hoạt động
                            </a>
                        </li>
                        <li @click="setStatus('upcoming')">
                            <a class="dropdown-item fw-medium cursor-pointer"
                                :class="{ active: selectedStatus === 'upcoming' }">
                                Sắp diễn ra
                            </a>
                        </li>
                        <li @click="setStatus('out_of_stock')">
                            <a class="dropdown-item fw-medium cursor-pointer"
                                :class="{ active: selectedStatus === 'out_of_stock' }">
                                Đã hết
                            </a>
                        </li>
                        <li @click="setStatus('expired')">
                            <a class="dropdown-item fw-medium cursor-pointer"
                                :class="{ active: selectedStatus === 'expired' }">
                                Đã hết hạn
                            </a>
                        </li>
                        <li @click="setStatus('inactive')">
                            <a class="dropdown-item fw-medium cursor-pointer"
                                :class="{ active: selectedStatus === 'inactive' }">
                                Đã tạm ngừng
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="dropdown">
                    <button type="button" class="white-btn px-2 py-1 fs-14 dropdown-toggle-mobile dropdown-toggle radius-2 fw-medium"
                        data-bs-toggle="dropdown">
                        Tháng: {{ selectedMonth || 'Tất cả' }}
                    </button>
                    <ul class="dropdown-menu shadow-sm radius-2">
                        <li v-for="month in 12" :key="month" @click="setMonth(month)">
                            <a class="dropdown-item fw-medium cursor-pointer"
                                :class="{ active: selectedMonth === month }">
                                Tháng {{ month }}
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="dropdown">
                    <button type="button" class="white-btn px-2 py-1 fs-14 dropdown-toggle-mobile dropdown-toggle radius-2 fw-medium"
                        data-bs-toggle="dropdown">
                        Năm: {{ selectedYear || 'Tất cả' }}
                    </button>
                    <ul class="dropdown-menu shadow-sm radius-2">
                        <li v-for="year in years" :key="year" @click="setYear(year)">
                            <a class="dropdown-item fw-medium cursor-pointer"
                                :class="{ active: selectedYear === year }">
                                {{ year }}
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="dropdown">
                    <button type="button" class="white-btn px-2 py-1 fs-14 dropdown-toggle-mobile dropdown-toggle radius-2 fw-medium"
                        data-bs-toggle="dropdown">
                        Hiển Thị
                    </button>
                    <ul class="dropdown-menu shadow-sm radius-2">
                        <li v-for="option in [10, 25, 50, 100, 500]" :key="option" @click="setPerPage(option)">
                            <a :class="['dropdown-item fw-medium cursor-pointer', { active: per_page === option }]">
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
                            <input type="text" v-model="searchValue" placeholder="Tìm kiếm mã giảm giá"
                                class="form-control radius-2" />
                            <button v-if="searchValue" type="button" @click="clearSearch"
                                class="btn position-absolute top-50 end-0 translate-middle-y me-2 p-0 border-0 bg-transparent">
                                <i class="ri-close-line"></i>
                            </button>
                        </div>
                    </form>
                </div>
                <button type="button" @click="openAddModal()" class="main-btn px-3" style="height: 38px;">
                    <span class="d-none d-xxl-block">Thêm Mã Giảm Giá</span>
                    <span class="d-block d-xxl-none"><i class="ri-add-fill"></i></span>
                </button>
            </div>
        </div>
        <div v-if="loading.fetch" style="height: 60vh;" class="d-flex align-items-center justify-content-center">
            <loading_dot size="30px" border="4px" />
        </div>
        <div v-else class="p-10">
            <no_data v-if="vouchers.length == 0" />
            <div v-else class="table-responsive fade-in">
                <table class="table border">
                    <thead class="table-active">
                        <tr class="lh-lg align-middle">
                            <th scope="col" class="fs-14 fw-semibold min-w-100">Mã Giảm Giá</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-150">Loại Giảm Giá</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-100">Giá Trị</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-200">Điều Kiện Áp Dụng</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-100">Còn Lại</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-100">Đã Dùng</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-200 text-center">Thời Gian Hiệu Lực</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-100">Trạng Thái</th>
                            <th scope="col" class="fs-14 fw-semibold text-center min-w-100">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="voucher in vouchers" class="align-middle fw-medium">
                            <td>{{ voucher.code }}</td>
                            <td>{{ type[voucher.type] }}</td>
                            <td>
                                <span v-if="voucher.percent">
                                    {{ voucher.percent }} %
                                </span>
                                <span v-else>
                                    {{ Number(voucher.max).toLocaleString('vi-VN') + ' đ' }}
                                </span>
                            </td>
                            <td>
                                Đơn từ
                                <span>{{ Number(voucher.min).toLocaleString('vi-VN') + ' đ' }}</span>,
                                giới hạn {{ voucher.limit }} lần/người
                            </td>
                            <td>{{ voucher.quantity }}</td>
                            <td>{{ voucher.used_count }}</td>
                            <td class="text-center">
                                <p>{{ formatDateTime(voucher.start_date) }}</p>
                                <i class="ri-arrow-down-long-line"></i>
                                <p>{{ formatDateTime(voucher.end_date) }}</p>
                            </td>
                            <td>
                                <span v-if="selectedStatus === 'active'" class="badge bg-success radius-2">
                                    Đang hoạt động
                                </span>
                                <span v-else-if="selectedStatus === 'upcoming'" class="badge bg-primary radius-2">
                                    Sắp diễn ra
                                </span>
                                <span v-else-if="selectedStatus === 'expired'" class="badge bg-danger radius-2">
                                    Đã hết hạn
                                </span>
                                <span v-else-if="selectedStatus === 'out_of_stock'" class="badge bg-warning radius-2">
                                    Đã hết
                                </span>
                                <span v-else-if="selectedStatus === 'inactive'" class="badge bg-secondary radius-2">
                                    Đã tạm ngừng
                                </span>
                                <span v-else class="badge bg-light text-dark radius-2">Không xác định</span>
                            </td>

                            <td class="align-middle text-center py-3">
                                <div class="more-wrapper">
                                    <button class="more-btn one-button">
                                        <i class="ri-more-fill"></i>
                                    </button>
                                    <div class="more-popup">
                                        <button @click="openEditModal(voucher)" class="bg-warning">
                                            Cập Nhật
                                            <i class="ri-edit-line"></i>
                                        </button>
                                        <button class="text-white bg-danger">
                                            Xóa
                                            <i class="ri-delete-bin-line"></i>
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
    <div v-if="isModalVisible"
        class="modal-background container-fluid p-0 d-flex justify-content-center align-items-center">
        <div class="modal-edit-address modal-ezeshop bg-white shadow-sm px-4 pt-4 pb-2">
            <div class="modal-ezeshop__top d-flex justify-content-between align-items-center">
                <div class="modal-ezeshop__name fs-18 fw-500">
                    {{ isEdit ? 'Chỉnh sửa Mã Giảm Giá' : 'Thêm Mã Giảm Giá' }}
                </div>
                <div @click="isModalVisible = false" class="modal-ezeshop__cancel cursor-pointer">
                    <i class="fa-solid fa-xmark fs-18"></i>
                </div>
            </div>
            <!-- Content -->
            <div class="modal-ezeshop__main w-100 p-1" style="max-height: 500px;">
                <form id="handle_form_voucher" @submit.prevent="addOrEditVoucher()">
                    <div class="mb-3">
                        <label for="title" class="form-label fs-14">
                            Tiêu Đề
                            <span class="text-color">*</span>
                        </label>
                        <input type="text" v-model="voucher.title" id="title" class="form-control">
                        <p class="text-danger" v-if="errors.title">
                            {{ Array.isArray(errors.title) ? errors.title[0] : errors.title }}
                        </p>
                    </div>
                    <div class="mb-3">
                        <label for="code" class="form-label fs-14">
                            Mã Giảm Giá
                            <span class="text-color">*</span>
                        </label>
                        <input type="text" v-model="voucher.code" id="code" class="form-control">
                        <p class="text-danger" v-if="errors.code">
                            {{ Array.isArray(errors.code) ? errors.code[0] : errors.code }}
                        </p>
                    </div>
                    <div class="row g-0">
                        <div class="mb-3 col-6 pe-2">
                            <label for="type" class="form-label fs-14">Loại Ưu Đãi</label>
                            <select id="type" v-model="voucher.type"
                                class="form-control form-select cursor-pointer radius-2">
                                <option value="">Chọn loại ưu đãi</option>
                                <option value="percentage_discount">Giảm theo(%)</option>
                                <option value="fixed_amount">Giảm cố định</option>
                                <option value="free_shipping">Miễn phí vận chuyển</option>
                            </select>
                            <p class="text-danger" v-if="errors.type">
                                {{ Array.isArray(errors.type) ? errors.type[0] : errors.type }}
                            </p>
                        </div>
                        <div class="mb-3 col-6 ps-2">
                            <label for="limit" class="form-label fs-14">
                                Giới Hạn Dùng
                                <span class="text-color">*</span>
                            </label>
                            <input type="number" v-model="voucher.limit" id="limit" class="form-control">
                            <p class="text-danger" v-if="errors.limit">
                                {{ Array.isArray(errors.limit) ? errors.limit[0] : errors.limit }}
                            </p>
                        </div>
                    </div>
                    <div class="row g-0">
                        <div class="mb-3 col-6 pe-2">
                            <label for="min" class="form-label fs-14">
                                Đơn Tối Thiểu
                                <span class="text-color">*</span>
                            </label>
                            <input type="number" v-model="voucher.min" id="min" class="form-control">
                            <p class="text-danger" v-if="errors.min">
                                {{ Array.isArray(errors.min) ? errors.min[0] : errors.min }}
                            </p>
                        </div>
                        <div class="mb-3 col-6 ps-2">
                            <label for="max" class="form-label fs-14">
                                Giảm Tối Đa
                                <span class="text-color">*</span>
                            </label>
                            <input type="number" v-model="voucher.max" id="max" class="form-control">
                            <p class="text-danger" v-if="errors.max">
                                {{ Array.isArray(errors.max) ? errors.max[0] : errors.max }}
                            </p>
                        </div>
                    </div>
                    <div class="row g-0">
                        <div class="mb-3 col-6 pe-2">
                            <label for="quantity" class="form-label fs-14">
                                Số Lượng
                                <span class="text-color">*</span>
                            </label>
                            <input type="number" v-model="voucher.quantity" id="quanitty" class="form-control">
                            <p class="text-danger" v-if="errors.quantity">
                                {{ Array.isArray(errors.quantity) ? errors.quantity[0] : errors.quantity }}
                            </p>
                        </div>
                        <div v-if="voucher.type !== 'fixed_amount'" class="mb-3 col-6 ps-2">
                            <label for="percent" class="form-label fs-14">
                                Phần Trăm(%)
                            </label>
                            <input type="number" v-model="voucher.percent" id="percent" class="form-control">
                            <p class="text-danger" v-if="errors.percent">
                                {{ Array.isArray(errors.percent) ? errors.percent[0] : errors.percent }}
                            </p>
                        </div>
                    </div>
                    <div class="row g-0">
                        <div class="mb-3 col-6 pe-2">
                            <label for="start_date" class="form-label">Ngày Bắt Đầu</label>
                            <input type="datetime-local" v-model="voucher.start_date" class="form-control"
                                id="start_date">
                            <p class="text-danger" v-if="errors.start_date">
                                {{ Array.isArray(errors.start_date) ? errors.start_date[0] : errors.start_date }}
                            </p>
                        </div>
                        <div class="mb-3 col-6 ps-2">
                            <label for="end_date" class="form-label">Ngày Kết Thúc</label>
                            <input type="datetime-local" v-model="voucher.end_date" class="form-control" id="end_date">
                            <p class="text-danger" v-if="errors.end_date">
                                {{ Array.isArray(errors.end_date) ? errors.end_date[0] : errors.end_date }}
                            </p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="comment">Mô Tả</label>
                        <textarea class="form-control" v-model="voucher.description" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-ezeshop__bottom d-flex justify-content-end w-100 align-items-center border-top">
                <div class="modal-ezeshop__cancel me-4">
                    <button @click="isModalVisible = false" class="white-btn py-2 px-4 fs-14">Huỷ</button>
                </div>
                <div class="modal-ezeshop__save">
                    <button form="handle_form_voucher" :disabled="loading.handlForm" style="width: 150px; height: 37px;"
                        class="main-btn py-2 fs-14 d-flex align-items-center justify-content-center" type="submit">
                        <loading_loader_circle v-if="loading.handlForm" size="15px" color="#fff" border="2px" />
                        <span v-else>{{ isEdit ? 'Cập Nhật' : 'Thêm Mã Giảm Giá' }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import { onMounted, ref } from 'vue'
import api from '@/configs/api'
import message from '@/utils/messageState'
import loading_dot from '@/components/loading/loading__dot.vue'
import loading_loader_circle from '@/components/loading/loading__loader-circle.vue'
import pagination from '@/components/pagination.vue'
import no_data from '@/components/no_data.vue'
import { formatDateTime } from '@/utils/formatDate'

onMounted(() => {
    const now = new Date()
    const month = now.getMonth() + 1
    const year = now.getFullYear()

    const startYear = year - 5
    const endYear = year + 5

    years.value = Array.from({ length: endYear - startYear + 1 }, (_, i) => startYear + i)
    selectedMonth.value = month
    selectedYear.value = year
    fetchVouchersPlatform()
})
/*
    Search & Pagination & Filter
*/
const meta = ref({})
const currentPage = ref(1)
const searchValue = ref('')
const per_page = ref(10)
const selectedMonth = ref(null)
const selectedYear = ref(null)
const selectedStatus = ref('active')
const years = ref([])
const onSearch = () => {
    currentPage.value = 1
    fetchVouchersPlatform()
}
const clearSearch = () => {
    searchValue.value = ''
    fetchVouchersPlatform()
}
const setPerPage = (perPage) => {
    per_page.value = perPage
    fetchVouchersPlatform()
}
const setMonth = (month) => {
    selectedMonth.value = month
    fetchVouchersPlatform()
}
const setYear = (year) => {
    selectedYear.value = year
    fetchVouchersPlatform()
}
const setStatus = (status) => {
    selectedStatus.value = status
    fetchVouchersPlatform()
}
const onChangePage = (page) => {
    currentPage.value = page
    fetchVouchersPlatform()
}
/*
    LOADING
*/
const loading = ref({
    fetch: false,
    handlForm: false
})

/*
    GET VOUCHERS
*/
const type = ref({
    fixed_amount: 'Giảm cố định',
    percentage_discount: 'Giảm theo phần trăm',
    free_shipping: 'Miễn phí vận chuyển'
})
const vouchers = ref([])
const fetchVouchersPlatform = async () => {
    try {
        loading.value.fetch = true
        const res = await api.get('/vouchers/platform', {
            params: {
                page: currentPage.value,
                search: searchValue.value,
                per_page: per_page.value,
                month: selectedMonth.value,
                year: selectedYear.value,
                status: selectedStatus.value

            }
        })
        if (res.status == 200) {
            vouchers.value = res.data.data
            meta.value = res.data.meta
        }
    } catch {
        message.emit('show-message', { title: "Error", text: "Failed to fetch vouchers.", type: "error" })
    } finally {
        loading.value.fetch = false
    }
}

/*
    ADD & UPDATE VOUCHER
*/
const openAddModal = () => {
    errors.value = {}
    isEdit.value = false
    voucher.value = { ...voucher_default }
    isModalVisible.value = true
}
const openEditModal = (item) => {
    errors.value = {}
    isEdit.value = true
    voucher.value = { ...item }
    isModalVisible.value = true
}
const isEdit = ref(false)
const isModalVisible = ref(false)

const voucher_default = {
    title: '',
    code: '',
    type: '',
    source: 'platform',
    shop_id: null,
    quantity: 1,
    limit: 1,
    min: 0,
    max: 0,
    percent: null,
    start_date: null,
    end_date: null,
    description: '',
    is_active: true,
}
const voucher = ref({ ...voucher_default })
const addOrEditVoucher = async () => {
    if (!validateVoucherForm(voucher.value)) return
    const payload = voucher.value
    try {
        loading.value.handlForm = true
        if (isEdit.value && voucher.value.id) {
            const response = await api.put(`/vouchers/${payload.id}`, payload)
            if (response.status == 200) {
                const inx = vouchers.value.findIndex((item) => item.id == payload.id)
                vouchers.value[inx] = { ...vouchers.value[inx], ...payload }
                message.emit('show-message', { title: "Thông báo", text: "Cập nhật mã giảm giá thành công.", type: "success" })
            }
        } else {
            const response = await api.post('/vouchers', payload)
            if (response.status == 201) {
                setStatus('active')
                message.emit('show-message', { title: "Thông báo", text: "Thêm mã giảm giá thành công.", type: "success" })
                voucher.value = { ...voucher_default }
            }
        }
    } catch (error) {
        message.emit('show-message', { title: "Thông báo", text: error.response.data.message, type: "error" })
    } finally {
        loading.value.handlForm = false
    }
}
/*
    VALIDATE
*/
const errors = ref({})
const validateVoucherForm = (voucher) => {
    errors.value = {};

    if (!voucher.title) {
        errors.value.title = 'Tiêu đề không được để trống.';
    }

    if (!voucher.code) {
        errors.value.code = 'Mã giảm giá không được để trống.';
    }

    if (!voucher.type) {
        errors.value.type = 'Vui lòng chọn loại ưu đãi.';
    }

    if (voucher.limit === null || voucher.limit < 1) {
        errors.value.limit = 'Giới hạn dùng phải lớn hơn 0.';
    }

    if (voucher.min === null || voucher.min < 0) {
        errors.value.min = 'Đơn tối thiểu không hợp lệ.';
    }

    if (voucher.max === null || voucher.max <= 0) {
        errors.value.max = 'Giảm tối đa phải lớn hơn 0.';
    }

    if (voucher.quantity === null || voucher.quantity < 1) {
        errors.value.quantity = 'Số lượng phải lớn hơn 0.';
    }

    if (voucher.type === 'fixed_amount') {
        voucher.percent = null;
    } else if (voucher.type === 'free_shipping') {
        if (voucher.percent) {
            if (voucher.percent < 1 || voucher.percent > 100) {
                errors.value.percent = 'Phần trăm phải từ 1 đến 100.';
            }
        }
    } else {
        if (voucher.percent === null || voucher.percent < 1 || voucher.percent > 100) {
            errors.value.percent = 'Phần trăm phải từ 1 đến 100.';
        }
    }


    if (!voucher.start_date) {
        errors.value.start_date = 'Vui lòng chọn ngày bắt đầu.';
    }

    if (!voucher.end_date) {
        errors.value.end_date = 'Vui lòng chọn ngày kết thúc.';
    }

    if (voucher.start_date && voucher.end_date && voucher.end_date < voucher.start_date && voucher.start_date == voucher.end_date) {
        errors.value.end_date = 'Ngày kết thúc phải sau ngày bắt đầu.';
    }
    return Object.keys(errors.value).length === 0;
}
</script>
<style scoped>
.dropdown .active {
    background-color: var(--text-color);
    color: var(--white-color) !important;
}
</style>