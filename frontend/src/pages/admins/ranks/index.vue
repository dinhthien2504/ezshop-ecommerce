<template>
    <main class="admin__content">
        <div
            class="p-3 bg-white border-bottom text-grey fs-14 fw-medium d-flex align-items-center justify-content-between">
            <p class="m-0">Danh Sách xếp hạng (<span class="text-color">{{ totalRank }}</span>)</p>
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
                            <input type="text" v-model="searchValue" placeholder="Tìm xếp hạng..."
                                class="form-control" />
                            <button v-if="searchValue" type="button" @click="searchValue = ''; onSearch()"
                                class="btn position-absolute top-50 end-0 translate-middle-y me-2 p-0 border-0 bg-transparent">
                                <i class="ri-close-line"></i>
                            </button>
                        </div>
                    </form>
                </div>
                <button @click="showAddModal" type="button" class="main-btn px-3" style="height: 38px">
                    <span class="d-none d-lg-block">Thêm Xếp Hạng</span>
                    <span class="d-block d-lg-none"><i class="ri-add-fill"></i></span>
                </button>
            </div>
        </div>
        <div class="p-10">
            <div v-if="loading.fetch" style="height: 60vh;"
                class="d-flex align-items-center justify-content-center">
                <loading__dot />
            </div>
            <div v-else class="table-responsive fade-in">
                <div v-if="ranks.length === 0">
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
                            <th scope="col" class="fs-14 fw-semibold min-w-150">Tên Xếp Hạng</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-200">Chi Tiêu Tối Thiểu</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-200">Chi Tiêu Tối Đa</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-250">Lợi Ích</th>
                            <th scope="col" class="fs-14 fw-semibold text-center min-w-100">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="rank in ranks">
                            <td scope="row" class="align-middle">
                                <div class="checkbox me-2">
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="check-item" :value="rank.id"
                                            v-model="selectedRanks" />
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
                            <td class="align-middle">{{ rank.name }}</td>
                            <td class="align-middle">{{ formatPrice(rank.min_spent) }}</td>
                            <td class="align-middle">{{ formatPrice(rank.max_spent) }}</td>
                            <td class="align-middle">{{ rank.benefits }}</td>
                            <td class="align-middle text-center">
                                <div class="more-wrapper">
                                    <button class="more-btn">
                                        <i class="ri-more-fill"></i>
                                    </button>
                                    <div class="more-popup">
                                        <button @click="showEditModal(rank.id)" class="bg-warning">Sửa</button>
                                        <button @click="showConfirmModal(rank.id)"
                                            class="text-white bg-danger">Xóa</button>
                                        <!-- <button class="text-white bg-success">Mở</button> -->
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


        <!-- Modal add -->
        <div v-if="isAddModal"
            class="modal-background container-fluid p-0 d-flex justify-content-center align-items-center">
            <div class="modal-edit-address modal-ezeshop bg-white shadow-sm px-4 pt-4 pb-2">
                <div class="modal-ezeshop__top d-flex justify-content-between align-items-center">
                    <div class="modal-ezeshop__name fs-18 fw-500">Thêm Xếp Hạng</div>
                    <div @click="isAddModal = false" class="modal-ezeshop__cancel cursor-pointer"><i
                            class="fa-solid fa-xmark fs-18"></i></div>
                </div>
                <!-- Content -->
                <div class="modal-ezeshop__main w-100 p-1">
                    <form @submit.prevent="submitAddRank" id="form-edit-cate" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="name" class="form-label fs-14">Tên Xếp Hạng<span
                                    class="text-color">*</span></label>
                            <input v-model="newRank.name" placeholder="Phàm nhân..." type="text"
                                class="form-control form-control__input">
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label fs-14">Chi Tiêu Tối Thiểu<span
                                    class="text-color">*</span></label>
                            <input v-model="newRank.min_spent" placeholder="" type="number" min="0" step="1000"
                                class="form-control form-control__input">
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label fs-14">Chi Tiêu Tối Đa<span
                                    class="text-color">*</span></label>
                            <input v-model="newRank.max_spent" placeholder="" type="number" min="0" step="1000"
                                class="form-control form-control__input">
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label fs-14">Lợi Ích</label>
                            <textarea v-model="newRank.benefits" type="text" placeholder="Người trên vạn người..."
                                class="form-control form-control__input"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-ezeshop__bottom d-flex justify-content-end w-100 align-items-center border-top">
                    <div class="modal-ezeshop__cancel me-4"><button @click="isAddModal = false"
                            class="white-btn py-2 px-4 fs-14">Huỷ</button></div>
                    <div class="modal-ezeshop__save"><button form="form-edit-cate" class="main-btn py-2 px-4 fs-14"
                            type="submit">
                            <loading__loader_circle size="15px" color="#fff" border="2px" v-if="loading.save_add" />
                            <div v-else>Thêm</div>
                        </button>
                    </div>
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
                            @click="isDeletingMultiple ? deleteSelectedRanks() : deleteRank()"
                            class="main-btn py-2 px-4 fs-14" type="submit">
                            <loading__loader_circle size="15px" color="#fff" border="2px" v-if="loading.delete" />
                            <div v-else>Xác Nhận</div>
                        </button></div>
                </div>
            </div>
        </div>

        <!-- Modal edit -->
        <div v-if="isEditModal"
            class="modal-background container-fluid p-0 d-flex justify-content-center align-items-center">
            <div class="modal-edit-address modal-ezeshop bg-white shadow-sm px-4 pt-4 pb-2">
                <div class="modal-ezeshop__top d-flex justify-content-between align-items-center">
                    <div class="modal-ezeshop__name fs-18 fw-500">Sửa Xếp Hạng</div>
                    <div @click="isEditModal = false" class="modal-ezeshop__cancel cursor-pointer"><i
                            class="fa-solid fa-xmark fs-18"></i></div>
                </div>
                <!-- Content -->
                <div class="modal-ezeshop__main w-100 p-1">
                    <form @submit.prevent="submitEditRank(editRank.id)" id="form-edit-cate"
                        enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="name" class="form-label fs-14">Tên Xếp Hạng<span
                                    class="text-color">*</span></label>
                            <input v-model="editRank.name" placeholder="Phàm nhân..." type="text"
                                class="form-control form-control__input">
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label fs-14">Chi Tiêu Tối Thiểu<span
                                    class="text-color">*</span></label>
                            <input v-model="editRank.min_spent" placeholder="" type="number" min="0" step="1000"
                                class="form-control form-control__input">
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label fs-14">Chi Tiêu Tối Đa<span
                                    class="text-color">*</span></label>
                            <input v-model="editRank.max_spent" placeholder="" type="number" min="0" step="1000"
                                class="form-control form-control__input">
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label fs-14">Lợi Ích</label>
                            <textarea v-model="editRank.benefits" type="text" placeholder="Người trên vạn người..."
                                class="form-control form-control__input"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-ezeshop__bottom d-flex justify-content-end w-100 align-items-center border-top">
                    <div class="modal-ezeshop__cancel me-4"><button @click="isEditModal = false"
                            class="white-btn py-2 px-4 fs-14">Huỷ</button></div>
                    <div class="modal-ezeshop__save"><button form="form-edit-cate" class="main-btn py-2 px-4 fs-14"
                            type="submit">
                            <loading__loader_circle size="15px" color="#fff" border="2px" v-if="loading.save_edit" />
                            <div v-else>Lưu</div>
                        </button>
                    </div>
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
import { formatDate } from '@/utils/formatDate'
import { formatPrice } from '@/utils/formatPrice'
import pagination from '@/components/pagination.vue'
import message from '@/utils/messageState'
import loading__dot from '@/components/loading/loading__dot.vue'
import loading__loader_circle from '@/components/loading/loading__loader-circle.vue'
import no_data from '@/components/no_data.vue'

// =======================
// Biến trạng thái (state)
// =======================

// Phân trang và tìm kiếm
const meta = ref({})
const currentPage = ref(1) // Trang hiện tại
const searchValue = ref('') // Giá trị tìm kiếm

// Danh sách rank và tổng số
const ranks = ref([]) // Danh sách rank
const totalRank = ref(0) // Tổng số rank

// Trạng thái loading cho các thao tác
const loading = ref({
    fetch: false,      // Đang tải dữ liệu
    save_add: false,   // Đang thêm rank
    save_edit: false,  // Đang sửa rank
    delete: false      // Đang xóa rank
})

// =======================
// Modal Thêm Xếp Hạng
// =======================
const isAddModal = ref(false)
const newRank = ref({
    name: '',
    min_spent: 0,
    max_spent: 0,
    benefits: ''
})

// =======================
// Modal Sửa Xếp Hạng
// =======================
const isEditModal = ref(false)
const editRank = ref({
    id: 0,
    name: '',
    min_spent: 0,
    max_spent: 0,
    benefits: ''
})

// =======================
// Modal Xác Nhận Xóa
// =======================
const isDeleteModal = ref(false) // Hiển thị modal xác nhận xóa
const rankIdToDelete = ref(null) // Id vai trò cần xóa
const isDeletingMultiple = ref(false) // Đang xóa nhiều vai trò

// =======================
// Chọn/xóa nhiều rank
// =======================
const selectedRanks = ref([]) // Danh sách id rank được chọn
const checkAll = ref(false)   // Checkbox chọn tất cả

// =======================
// Các hàm xử lý chính
// =======================

// Lấy danh sách rank từ API
const getRanks = async () => {
    try {
        loading.value.fetch = true
        const res = await api.get('/rank', {
            params: {
                page: currentPage.value,
                search: searchValue.value,
            }
        })
        ranks.value = res.data.ranks
        totalRank.value = res.data.total
        meta.value = {
            current_page: res.data.current_page,
            last_page: res.data.last_page,
            total: res.data.total,
        }
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
    getRanks()
}

// =======================
// Thêm xếp hạng
// =======================

// Hiển thị modal thêm
const showAddModal = () => {
    newRank.value = {}
    isAddModal.value = true
}

// Gửi form thêm xếp hạng
const submitAddRank = async () => {
    if (!validateAdd(newRank.value)) return
    try {
        loading.value.save_add = true
        await api.post('/rank', newRank.value)
        message.emit('show-message', {
            title: 'Thành công',
            text: 'Thêm xếp hạng thành công',
            type: 'success'
        })
        isAddModal.value = false
        newRank.value = {
            name: '',
            min_spent: 0,
            max_spent: 0,
            benefits: ''
        }
        getRanks()
    } catch (err) {
        console.error(err)
        message.emit('show-error', {
            title: 'Thất bại',
            text: 'Không thể thêm xếp hạng',
            type: 'error'
        })
    } finally {
        loading.value.save_add = false
    }
}

// =======================
// Sửa xếp hạng
// =======================

// Hiển thị modal sửa
const showEditModal = async (id) => {
    isEditModal.value = true
    const rankToEdit = ranks.value.find(rank => rank.id === id)
    if (!rankToEdit) return
    editRank.value.id = rankToEdit.id
    editRank.value.name = rankToEdit.name
    editRank.value.min_spent = rankToEdit.min_spent
    editRank.value.max_spent = rankToEdit.max_spent
    editRank.value.benefits = rankToEdit.benefits
}

// Gửi form sửa xếp hạng
const submitEditRank = async (id) => {
    if (!validateEdit(editRank.value)) return
    try {
        loading.value.save_edit = true
        const payload = {
            name: editRank.value.name,
            min_spent: editRank.value.min_spent,
            max_spent: editRank.value.max_spent,
            benefits: editRank.value.benefits
        }
        await api.put(`/rank/${id}`, payload)
        message.emit('show-message', {
            title: 'Thành công',
            text: 'Cập nhật thành công',
            type: 'success'
        })
        isEditModal.value = false
        await getRanks()
    } catch (error) {
        const msg = error.response?.data?.message || 'Có lỗi xảy ra khi cập nhật'
        message.emit('show-message', {
            title: 'Lỗi',
            text: msg,
            type: 'error'
        })
        console.error('Sửa thất bại:', error.response?.data || error)
    } finally {
        loading.value.save_edit = false
    }
}

// =======================
// Xóa xếp hạng
// =======================

// Hiển thị modal xác nhận xóa (1 hoặc nhiều)
const showConfirmModal = (id = null) => {
    isDeleteModal.value = true
    rankIdToDelete.value = id
    isDeletingMultiple.value = !id
}

// Xóa 1 xếp hạng
const deleteRank = async () => {
    try {
        loading.value.delete = true
        await api.delete(`/rank/${rankIdToDelete.value}`)
        message.emit('show-message', {
            title: 'Thành công',
            text: 'Xóa thành công',
            type: 'success'
        })
        getRanks()
        selectedRanks.value = []
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

// Xóa nhiều xếp hạng đã chọn
const deleteSelectedRanks = async () => {
    if (selectedRanks.value.length === 0) {
        message.emit('show-message', {
            title: 'Chú ý',
            text: 'Chưa chọn vai trò nào để xóa',
            type: 'warning'
        })
        isDeleteModal.value = false
        checkAll.value = false
        return
    }
    try {
        loading.value.delete = true
        isDeletingMultiple.value = true
        await api.delete('/rank/delete-multiple', {
            data: { ids: selectedRanks.value }
        })
        message.emit('show-message', {
            title: 'Thành công',
            text: 'Đã xóa các vai trò đã chọn',
            type: 'success'
        })
        selectedRanks.value = []
        checkAll.value = false
        getRanks()
    } catch (error) {
        message.emit('show-message', {
            title: 'Thất bại',
            text: 'Xóa thất bại',
            type: 'error'
        })
    } finally {
        isDeleteModal.value = false
        rankIdToDelete.value = null
        isDeletingMultiple.value = false
        loading.value.delete = false
    }
}

// =======================
// Chọn tất cả / bỏ chọn tất cả
// =======================
const toggleAllSelection = () => {
    checkAll.value
        ? selectedRanks.value = ranks.value.map(Rank => Rank.id)
        : selectedRanks.value = []
}

// Theo dõi selectedRanks để cập nhật checkAll
watch(selectedRanks, (newSelected) => {
    if (newSelected.length === ranks.value.length && ranks.value.length > 0) {
        checkAll.value = true
    } else {
        checkAll.value = false
    }
})

// =======================
// Validate form
// =======================

// Kiểm tra hợp lệ khi thêm
function validateAdd(form) {
    const errors = []

    // 1. Tên xếp hạng
    if (!form.name || !form.name.trim()) {
        errors.push('Tên xếp hạng là bắt buộc.')
    } else if (form.name.length > 50) {
        errors.push('Tên xếp hạng không được vượt quá 50 ký tự.')
    } else {
        const nameExists = ranks.value.some(rank => rank.name.toLowerCase().trim() === form.name.toLowerCase().trim())
        if (nameExists) {
            errors.push("Tên xếp hạng đã tồn tại.")
        }
    }

    // 2. Chi tiêu tối thiểu
    if (form.min_spent === undefined || isNaN(form.min_spent)) {
        errors.push('Chi tiêu tối thiểu là bắt buộc và phải là số.')
    } else if (form.min_spent < 0) {
        errors.push('Chi tiêu tối thiểu không được âm.')
    }

    // 3. Chi tiêu tối đa
    if (form.max_spent === undefined || isNaN(form.max_spent)) {
        errors.push('Chi tiêu tối đa là bắt buộc và phải là số.')
    } else if (form.max_spent < 0) {
        errors.push('Chi tiêu tối đa không được âm.')
    } else if (form.max_spent < form.min_spent) {
        errors.push('Chi tiêu tối đa phải lớn hơn hoặc bằng chi tiêu tối thiểu.')
    }

    // 4. Lợi ích (nếu có)
    if (form.benefits && typeof form.benefits !== 'string') {
        errors.push('Lợi ích phải là chuỗi.')
    }

    // 5. Hiển thị lỗi nếu có
    if (errors.length > 0) {
        message.emit('show-message', {
            title: 'Lỗi xác thực',
            text: errors.join(' || '),
            type: 'error'
        })
        return false
    }

    return true
}

// Kiểm tra hợp lệ khi sửa
function validateEdit(form) {
    const errors = []

    // Tên xếp hạng
    if (!form.name || !form.name.trim()) {
        errors.push('Tên xếp hạng là bắt buộc.')
    } else if (form.name.length > 50) {
        errors.push('Tên xếp hạng không được vượt quá 50 ký tự.')
    }

    // Chi tiêu tối thiểu
    if (form.min_spent === undefined || isNaN(form.min_spent)) {
        errors.push('Chi tiêu tối thiểu là bắt buộc và phải là số.')
    } else if (form.min_spent < 0) {
        errors.push('Chi tiêu tối thiểu không được âm.')
    }

    // Chi tiêu tối đa
    if (form.max_spent === undefined || isNaN(form.max_spent)) {
        errors.push('Chi tiêu tối đa là bắt buộc và phải là số.')
    } else if (form.max_spent < 0) {
        errors.push('Chi tiêu tối đa không được âm.')
    } else if (form.max_spent < form.min_spent) {
        errors.push('Chi tiêu tối đa phải lớn hơn hoặc bằng chi tiêu tối thiểu.')
    }

    // Lợi ích
    if (form.benefits && typeof form.benefits !== 'string') {
        errors.push('Lợi ích phải là chuỗi.')
    }

    if (errors.length > 0) {
        message.emit('show-message', {
            title: 'Lỗi xác thực',
            text: errors.join(' || '),
            type: 'error'
        })
        return false
    }

    return true
}

// =======================
// Watchers và lifecycle
// =======================

// Theo dõi thay đổi trang để lấy lại dữ liệu
watch(currentPage, () => {
    getRanks()
})

// Khi component mounted, lấy danh sách Rank
onMounted(async () => {
    await getRanks()
})
</script>