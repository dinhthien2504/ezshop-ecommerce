<template>
    <main class="admin__content">
        <div
            class="p-3 bg-white border-bottom text-grey fs-14 fw-medium d-flex align-items-center justify-content-between">
            <p class="m-0">Khách Hàng Tố Cáo (<span class="text-color">{{ totalViolations }}</span>)</p>
            <div class="d-flex align-items-center gap-2">
                <div class="admin__search">
                    <button type="button" @click="onSearch" class="search__button">
                        <i class="ri-search-2-line"></i>
                    </button>
                    <form @submit.prevent="onSearch">
                        <div class="d-flex align-items-center gap-1 position-relative">
                            <button type="submit" style="height: 38px" class="main-btn py-1 px-3">
                                <i class="ri-search-2-line"></i>
                            </button>
                            <input type="text" v-model="searchValue" placeholder="Tìm tố cáo..." class="form-control" />
                            <button v-if="searchValue" type="button" @click="searchValue = ''; onSearch()"
                                class="btn position-absolute top-50 end-0 translate-middle-y me-2 p-0 border-0 bg-transparent">
                                <i class="ri-close-line"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="p-10">
            <div class="nav-button gap-2 d-flex flex-wrap">
                <button class="white-btn fs-14 px-2 py-1" :class="{ 'btn-active': filterStatus === null }"
                    @click="filterStatus = null">Tất Cả ({{ totalViolations }})</button>
                <button class="white-btn fs-14 px-2 py-1" :class="{ 'btn-active': filterStatus === 'pending' }"
                    @click="filterStatus = 'pending'">Chờ Duyệt ({{ pendingViolations }})</button>
                <button class="white-btn fs-14 px-2 py-1" :class="{ 'btn-active': filterStatus === 'resolved' }"
                    @click="filterStatus = 'resolved'">Đã Xử Lý ({{ resolvedViolations }})</button>
                <button class="white-btn fs-14 px-2 py-1" :class="{ 'btn-active': filterStatus === 'ignored' }"
                    @click="filterStatus = 'ignored'">Bỏ Qua ({{ ignoredViolations }})</button>
            </div>
            <div v-if="loading.fetch" style="height: 60vh;" class="d-flex align-items-center justify-content-center">
                <loading__dot />
            </div>
            <div v-else class="table-responsive mt-2 fade-in">
                <div v-if="violations.length === 0">
                    <no_data />
                </div>
                <table v-else class="table border">
                    <thead class="table-active" style="height: 50px">
                        <tr class="lh-lg align-middle">
                            <th scope="col" class="min-w-50">
                                <div class="checkbox">
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="check-item" v-model="checkAll"
                                            @change="toggleAllSelection" />
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
                            </th>
                            <th scope="col" class="fs-14 fw-semibold min-w-150">Tên Cửa Hàng</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-150">Người Tố Cáo</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-100">Trạng Thái</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-200">Loại Vi Phạm</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-200">Mô Tả Chi Tiết</th>
                            <th scope="col" class="fs-14 fw-semibold text-center min-w-100">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="violation in violations">
                            <td scope="row" class="align-middle py-5">
                                <div class="checkbox me-2">
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="check-item" :value="violation.id"
                                            v-model="selectedViolations" />
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
                            </td>
                            <td class="align-middle">{{ violation.shop.shop_name }}</td>
                            <td class="align-middle">{{ violation.reporter.user_name }}</td>
                            <td class="align-middle">
                                <span v-if="violation.status === 'pending'" class="badge text-bg-info">Chờ duyệt</span>
                                <span v-else-if="violation.status === 'resolved'" class="badge text-bg-success">Đã xử lý</span>
                                <span v-else-if="violation.status === 'ignored'" class="badge text-bg-secondary">Đã bỏ qua</span>
                            </td>
                            <td class="align-middle">{{ violation.type.name }}</td>
                            <td class="align-middle">{{ violation.description }}</td>
                            <td class="align-middle text-center">
                                <div class="more-wrapper">
                                    <button class="more-btn">
                                        <i class="ri-more-fill"></i>
                                    </button>
                                    <div class="more-popup" style="top: -70px" v-if="violation.status == 'pending'">
                                        <button @click="openNewTab(violation.shop)" class="bg-primary text-white">Xem</button>
                                        <button @click="changeStatus(violation.id, 'ignored')" class="bg-secondary text-white">Bỏ</button>
                                        <button @click="changeStatus(violation.id, 'resolved')" class="bg-success text-white">Duyệt</button>
                                        <button @click="showConfirmModal(violation.id)"
                                            class="text-white bg-danger">Xóa</button>
                                    </div>
                                    <div class="more-popup two-row" style="top: -70px" v-else>
                                        <button @click="openNewTab(violation.shop)" class="bg-primary text-white">Xem</button>
                                        <button @click="showConfirmModal(violation.id)"
                                            class="text-white bg-danger">Xóa</button>
                                        <button @click="changeStatus(violation.id, 'pending')" class="bg-warning">Xử lý lại</button>

                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot class="table-active">
                        <tr class="lh-lg">
                            <th scope="col" class="align-middle">
                                <div class="checkbox align-items-center d-flex gap-1">
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="check-item" id="check_all" v-model="checkAll"
                                            @change="toggleAllSelection" />
                                        <div class="checkbox-wrapper">
                                            <div class="checkbox-bg"></div>
                                            <svg fill="none" viewBox="0 0 24 24" class="checkbox-icon">
                                                <path stroke-linejoin="round" stroke-linecap="round" stroke-width="3"
                                                    stroke="currentColor" d="M4 12L10 18L20 6" class="check-path">
                                                </path>
                                            </svg>
                                        </div>
                                    </label>
                                    <span class="fw-medium">All</span>
                                </div>
                            </th>
                            <th scope="col"></th>
                            <th scope="col"></th>
                            <th scope="col"></th>
                            <th scope="col"></th>
                            <th scope="col"></th>
                            <td class="align-middle text-center">
                                <div class="more-wrapper">
                                    <button class="more-btn p-0">
                                        <i class="ri-more-fill"></i>
                                    </button>
                                    <div class="more-popup">
                                        <button @click="showConfirmModal()" class="text-white bg-danger px-2 py-0">
                                            Xóa Tất Cả
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
                <!-- Pagination -->
                <div class="d-flex align-items-center justify-content-end me-3">
                    <pagination :meta="meta" @changePage="onChangePage" />
                </div>
            </div>
        </div>

        <!-- Confirm Delete Modal -->
        <div v-if="isDeleteModal"
            class="modal-background container-fluid p-0 d-flex justify-content-center align-items-center">
            <div class="modal-edit-address modal-ezeshop bg-white shadow-sm px-4 pt-4 pb-2" style="max-width: 500px;">
                <div class="modal-ezeshop__top d-flex justify-content-between align-items-center">
                    <div class="modal-ezeshop__name fs-18 fw-500">Xác Nhận Xóa</div>
                    <div @click="isDeleteModal = false" class="modal-ezeshop__cancel cursor-pointer"><i
                            class="fa-solid fa-xmark fs-18"></i></div>
                </div>
                <!-- Content -->
                <div class="modal-ezeshop__main w-100">
                    Bạn có chắc muốn xóa không?
                </div>
                <div class="mt-3 modal-ezeshop__bottom d-flex justify-content-end w-100 align-items-center border-top">
                    <div class="modal-ezeshop__cancel me-4"><button @click="isDeleteModal = false"
                            class="white-btn py-2 px-4 fs-14">Huỷ</button></div>
                    <div class="modal-ezeshop__save"><button
                            @click="isDeletingMultiple ? deleteSelectedViolations() : deleteViolation()"
                            class="main-btn py-2 px-4 fs-14" type="submit">
                            <loading__loader_circle size="15px" color="#fff" border="2px" v-if="loading.delete" />
                            <div v-else>Xác Nhận</div>
                        </button></div>
                </div>
            </div>
        </div>

    </main>
</template>
<script setup>
// =======================
// Import các thư viện và component cần thiết
// =======================
import { ref, onMounted, watch } from 'vue'
import api from '@/configs/api'
import pagination from '@/components/pagination.vue'
import message from '@/utils/messageState'
import loading__dot from '@/components/loading/loading__dot.vue'
import loading__loader_circle from '@/components/loading/loading__loader-circle.vue'
import no_data from '@/components/no_data.vue'
import { slugify } from '@/utils/slugHelpers'

// =======================
// Biến trạng thái (state)
// =======================

// Phân trang và tìm kiếm
const meta = ref({})
const currentPage = ref(1) // Trang hiện tại
const searchValue = ref('') // Giá trị tìm kiếm

// Danh sách vi phạm và tổng số
const violations = ref([]) // Danh sách vi phạm
const totalViolations = ref(0) // Tổng số vi phạm
const filterStatus = ref(null) // Trạng thái lọc (tất cả, chờ duyệt, đã xử lý, bỏ qua)
const pendingViolations = ref(0) // Số lượng vi phạm chờ duyệt
const resolvedViolations = ref(0) // Số lượng vi phạm đã xử lý
const ignoredViolations = ref(0) // Số lượng vi phạm đã bỏ qua

// Trạng thái loading cho các thao tác
const loading = ref({
    fetch: false,      // Đang tải dữ liệu
    save_add: false,   // Đang thêm type
    save_edit: false,  // Đang sửa type
    delete: false      // Đang xóa type
})



// =======================
// Modal Xác Nhận Xóa
// =======================
const isDeleteModal = ref(false) // Hiển thị modal xác nhận xóa
const idToDelete = ref(null) // Id loại vi phạm cần xóa
const isDeletingMultiple = ref(false) // Đang xóa nhiều loại vi phạm

// =======================
// Chọn/xóa nhiều loại vi phạm
// =======================
const selectedViolations = ref([]) // Danh sách id loại vi phạm được chọn
const checkAll = ref(false)   // Checkbox chọn tất cả

// =======================
// Các hàm xử lý chính
// =======================

// Lấy danh sách vi phạm từ API
const getViolations = async () => {
    try {
        loading.value.fetch = true
        const res = await api.get('/violations', {
            params: {
                page: currentPage.value,
                search: searchValue.value,
                status: filterStatus.value, // Thêm tham số status
            }
        })
        violations.value = res.data.violations
        
        // Sử dụng all_total cho nút "Tất cả" để giữ số ổn định
        totalViolations.value = res.data.all_total
        
        // Luôn cập nhật số đếm từng trạng thái
        pendingViolations.value = res.data.pending
        resolvedViolations.value = res.data.resolved
        ignoredViolations.value = res.data.ignored

        meta.value = {
            current_page: res.data.current_page,
            last_page: res.data.last_page,
            total: res.data.total, // Sử dụng total đã filter cho pagination
        }
        // console.log(violations.value)
    } catch (error) {
        console.error(error)
    } finally {
        loading.value.fetch = false
    }
}

// Đổi trang
const onChangePage = (page) => {
    currentPage.value = page
}

// Tìm kiếm
const onSearch = () => {
    currentPage.value = 1
    getViolations()
}


// Hiển thị modal xác nhận xóa (1 hoặc nhiều)
const showConfirmModal = (id = null) => {
    isDeleteModal.value = true
    idToDelete.value = id
    isDeletingMultiple.value = !id
}

// Xóa 1 vi phạm
const deleteViolation = async () => {
    try {
        loading.value.delete = true
        await api.delete(`/violations/${idToDelete.value}`)
        message.emit('show-message', {
            title: 'Thành công',
            text: 'Xóa thành công',
            type: 'success'
        })
        getViolations()
        selectedViolations.value = []
        checkAll.value = false
    } catch (error) {
        message.emit('show-message', {
            title: 'Thất bại',
            text: 'Xóa thất bại',
            type: 'error'
        })
    } finally {
        isDeleteModal.value = false
        isDeletingMultiple.value = false
        loading.value.delete = false
    }
}

// Xóa nhiều vi phạm đã chọn
const deleteSelectedViolations = async () => {
    if (selectedViolations.value.length === 0) {
        message.emit('show-message', {
            title: 'Chú ý',
            text: 'Chưa chọn vi phạm nào để xóa',
            type: 'warning'
        })
        isDeleteModal.value = false
        checkAll.value = false
        return
    }
    try {
        loading.value.delete = true
        isDeletingMultiple.value = true
        await api.delete('/violations/delete-multiple', {
            data: { ids: selectedViolations.value }
        })
        message.emit('show-message', {
            title: 'Thành công',
            text: 'Đã xóa các vi phạm đã chọn',
            type: 'success'
        })
        selectedViolations.value = []
        checkAll.value = false
        getViolations()
    } catch (error) {
        message.emit('show-message', {
            title: 'Thất bại',
            text: 'Xóa thất bại',
            type: 'error'
        })
    } finally {
        isDeleteModal.value = false
        idToDelete.value = null
        isDeletingMultiple.value = false
        loading.value.delete = false
    }
}

const changeStatus = async (id, status) => {
    try {
        const res = await api.post(`violations/change-status/${id}`, { status })
        // console.log(res.data)
        getViolations()
        message.emit('show-message', {
            title: 'Thành công',
            text: 'Đổi trạng thái thành công',
            type: 'success'
        })
    } catch (error) {
        console.log(error)
    }
}


// =======================
// Chọn tất cả / bỏ chọn tất cả
// =======================
const toggleAllSelection = () => {
    checkAll.value
        ? selectedViolations.value = violations.value.map(violation => violation.id)
        : selectedViolations.value = []
}

// Theo dõi selectedViolations để cập nhật checkAll
watch(selectedViolations, (newSelected) => {
    if (newSelected.length === violations.value.length && violations.value.length > 0) {
        checkAll.value = true
    } else {
        checkAll.value = false
    }
})

const openNewTab = (shop) => {
    const url = `/cua-hang-${slugify(shop.shop_name)}-${shop.id}`
    window.open(url, '_blank')
}


// Theo dõi thay đổi filterStatus để lấy lại dữ liệu
watch(filterStatus, () => {
    selectedViolations.value = []
    checkAll.value = false
    currentPage.value = 1 // Reset về trang đầu khi filter
    getViolations() // Gọi lại API
})

// Theo dõi thay đổi trang để lấy lại dữ liệu
watch(currentPage, () => {
    getViolations()
})

// Khi component mounted, lấy danh sách Vi Phạm
onMounted(async () => {
    await getViolations()
})
</script>
<style scoped>
.nav-button button {
    border-radius: 2px;
}

.btn-active {
    border: #dc3545 1px solid !important;
    background-color: var(--main-color) !important;
    color: #ffffff !important;
}
</style>