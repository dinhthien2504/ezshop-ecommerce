<template>
    <main class="admin__content">
        <div
            class="p-3 bg-white border-bottom text-grey fs-14 fw-medium d-flex align-items-center justify-content-between">
            <p class="m-0">Danh Sách Loại Vi Phạm (<span class="text-color">{{ totalTypes }}</span>)</p>
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
                            <input type="text" v-model="searchValue" placeholder="Tìm loại vi phạm..."
                                class="form-control" />
                            <button v-if="searchValue" type="button" @click="searchValue = ''; onSearch()"
                                class="btn position-absolute top-50 end-0 translate-middle-y me-2 p-0 border-0 bg-transparent">
                                <i class="ri-close-line"></i>
                            </button>
                        </div>
                    </form>
                </div>
                <button @click="showAddModal" type="button" class="main-btn px-3" style="height: 38px">
                    <span class="d-none d-lg-block">Thêm Loại Vi Phạm</span>
                    <span class="d-block d-lg-none"><i class="ri-add-fill"></i></span>
                </button>
            </div>
        </div>
        <div class="p-10">
            <div v-if="loading.fetch" style="height: 60vh;" class="d-flex align-items-center justify-content-center">
                <loading__dot />
            </div>
            <div v-else class="table-responsive fade-in">
                <div v-if="types.length === 0">
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
                            <th scope="col" class="fs-14 fw-semibold min-w-300">Loại Vi Phạm</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-100">Mã Loại Vi Phạm</th>
                            <th scope="col" class="fs-14 fw-semibold text-center min-w-100">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="type in types">
                            <td scope="row" class="align-middle">
                                <div class="checkbox me-2">
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="check-item" :value="type.id"
                                            v-model="selectedTypes" />
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
                            <td class="align-middle">{{ type.name }}</td>
                            <td class="align-middle">{{ type.code }}</td>
                            <td class="align-middle text-center">
                                <div class="more-wrapper">
                                    <button class="more-btn">
                                        <i class="ri-more-fill"></i>
                                    </button>
                                    <div class="more-popup">
                                        <button @click="showEditModal(type.id)" class="bg-warning">Sửa</button>
                                        <button @click="showConfirmModal(type.id)"
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
                    <div class="modal-ezeshop__name fs-18 fw-500">Thêm Loại Vi Phạm</div>
                    <div @click="isAddModal = false" class="modal-ezeshop__cancel cursor-pointer"><i
                            class="fa-solid fa-xmark fs-18"></i></div>
                </div>
                <!-- Content -->
                <div class="modal-ezeshop__main w-100 p-1">
                    <form @submit.prevent="submitAddType" id="form-edit-cate" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="name" class="form-label fs-14">Tên Loại Vi Phạm<span
                                    class="text-color">*</span></label>
                            <input v-model="newType.name" placeholder="Lừa đảo..." type="text"
                                class="form-control form-control__input">
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label fs-14">Mã Loại Vi Phạm<span
                                    class="text-color">*</span></label>
                            <input v-model="newType.code" placeholder="code..." type="text"
                                class="form-control form-control__input">
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
                            @click="isDeletingMultiple ? deleteSelectedTypes() : deleteType()"
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
                    <div class="modal-ezeshop__name fs-18 fw-500">Sửa Cấp Bậc</div>
                    <div @click="isEditModal = false" class="modal-ezeshop__cancel cursor-pointer"><i
                            class="fa-solid fa-xmark fs-18"></i></div>
                </div>
                <!-- Content -->
                <div class="modal-ezeshop__main w-100 p-1">
                    <form @submit.prevent="submitEditType(editType.id)" id="form-edit-cate"
                        enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="name" class="form-label fs-14">Tên Loại Vi Phạm<span
                                    class="text-color">*</span></label>
                            <input v-model="editType.name" placeholder="Lừa đảo..." type="text"
                                class="form-control form-control__input">
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label fs-14">Mã Loại Vi Phạm<span
                                    class="text-color">*</span></label>
                            <input v-model="editType.code" placeholder="code..." type="text"
                                class="form-control form-control__input">
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

// Danh sách types và tổng số
const types = ref([]) // Danh sách types
const totalTypes = ref(0) // Tổng số types

// Trạng thái loading cho các thao tác
const loading = ref({
    fetch: false,      // Đang tải dữ liệu
    save_add: false,   // Đang thêm type
    save_edit: false,  // Đang sửa type
    delete: false      // Đang xóa type
})

// =======================
// Modal Thêm Cấp Bậc
// =======================
const isAddModal = ref(false)
const newType = ref({
    name: '',
    code: '',
})

// =======================
// Modal Sửa Cấp Bậc
// =======================
const isEditModal = ref(false)
const editType = ref({
    id: 0,
    name: '',
    code: ''
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
const selectedTypes = ref([]) // Danh sách id loại vi phạm được chọn
const checkAll = ref(false)   // Checkbox chọn tất cả

// =======================
// Các hàm xử lý chính
// =======================

// Lấy danh sách types từ API
const getTypes = async () => {
    try {
        loading.value.fetch = true
        const res = await api.get('/violation-types/admin', {
            params: {
                page: currentPage.value,
                search: searchValue.value,
            }
        })
        types.value = res.data.types
        totalTypes.value = res.data.total
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
    getTypes()
}

// =======================
// Thêm cấp bậc
// =======================

// Hiển thị modal thêm
const showAddModal = () => {
    newType.value = {}
    isAddModal.value = true
}

// Gửi form thêm loại vi phạm
const submitAddType = async () => {
    if (!validateAdd(newType.value)) return
    try {
        loading.value.save_add = true
        await api.post('/violation-types', newType.value)
        message.emit('show-message', {
            title: 'Thành công',
            text: 'Thêm loại vi phạm thành công',
            type: 'success'
        })
        isAddModal.value = false
        newType.value = {
            name: '',
            code: '',
        }
        getTypes()
    } catch (err) {
        console.error(err)
        message.emit('show-error', {
            title: 'Thất bại',
            text: 'Không thể thêm loại vi phạm',
            type: 'error'
        })
    } finally {
        loading.value.save_add = false
    }
}

// =======================
// Sửa cấp bậc
// =======================

// Hiển thị modal sửa
const showEditModal = async (id) => {
    isEditModal.value = true
    const typeToEdit = types.value.find(type => type.id === id)
    if (!typeToEdit) return
    editType.value.id = typeToEdit.id
    editType.value.name = typeToEdit.name
    editType.value.code = typeToEdit.code
}

// Gửi form sửa loại vi phạm
const submitEditType = async (id) => {
    if (!validateEdit(editType.value)) return
    try {
        loading.value.save_edit = true
        const payload = {
            name: editType.value.name,
            code: editType.value.code,
        }
        await api.put(`/violation-types/${id}`, payload)
        message.emit('show-message', {
            title: 'Thành công',
            text: 'Cập nhật thành công',
            type: 'success'
        })
        isEditModal.value = false
        await getTypes()
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
// Xóa cấp bậc
// =======================

// Hiển thị modal xác nhận xóa (1 hoặc nhiều)
const showConfirmModal = (id = null) => {
    isDeleteModal.value = true
    idToDelete.value = id
    isDeletingMultiple.value = !id
}

// Xóa 1 loại vi phạm
const deleteType = async () => {
    try {
        loading.value.delete = true
        await api.delete(`/violation-types/${idToDelete.value}`)
        message.emit('show-message', {
            title: 'Thành công',
            text: 'Xóa thành công',
            type: 'success'
        })
        getTypes()
        selectedTypes.value = []
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

// Xóa nhiều loại vi phạm đã chọn
const deleteSelectedTypes = async () => {
    if (selectedTypes.value.length === 0) {
        message.emit('show-message', {
            title: 'Chú ý',
            text: 'Chưa chọn loại vi phạm nào để xóa',
            type: 'warning'
        })
        isDeleteModal.value = false
        checkAll.value = false
        return
    }
    try {
        loading.value.delete = true
        isDeletingMultiple.value = true
        await api.delete('/violation-types/delete-multiple', {
            data: { ids: selectedTypes.value }
        })
        message.emit('show-message', {
            title: 'Thành công',
            text: 'Đã xóa các loại vi phạm đã chọn',
            type: 'success'
        })
        selectedTypes.value = []
        checkAll.value = false
        getTypes()
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

// =======================
// Chọn tất cả / bỏ chọn tất cả
// =======================
const toggleAllSelection = () => {
    checkAll.value
        ? selectedTypes.value = types.value.map(type => type.id)
        : selectedTypes.value = []
}

// Theo dõi selectedTypes để cập nhật checkAll
watch(selectedTypes, (newSelected) => {
    if (newSelected.length === types.value.length && types.value.length > 0) {
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

    // 1. Tên loại vi phạm
    if (!form.name || !form.name.trim()) {
        errors.push('Tên loại vi phạm là bắt buộc.')
    } else if (form.name.length > 100) {
        errors.push('Tên loại vi phạm không được vượt quá 100 ký tự.')
    } else {
        const nameExists = types.value.some(type => type.name.toLowerCase().trim() === form.name.toLowerCase().trim())
        if (nameExists) {
            errors.push("Tên loại vi phạm đã tồn tại.")
        }
    }

    // 2. Mã loại vi phạm
    if (!form.code || !form.code.trim()) {
        errors.push('Mã loại vi phạm là bắt buộc.')
    } else if (form.code.length > 20) {
        errors.push('Mã loại vi phạm không được vượt quá 20 ký tự.')
    } else {
        const codeExists = types.value.some(type => type.code.toLowerCase().trim() === form.code.toLowerCase().trim())
        if (codeExists) {
            errors.push("Mã loại vi phạm đã tồn tại.")
        }
    }

    // 3. Hiển thị lỗi nếu có
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

    // Tên loại vi phạm
    if (!form.name || !form.name.trim()) {
        errors.push('Tên loại vi phạm là bắt buộc.')
    } else if (form.name.length > 100) {
        errors.push('Tên loại vi phạm không được vượt quá 100 ký tự.')
    }

    // Mã loại vi phạm
    if (!form.code || !form.code.trim()) {
        errors.push('Mã loại vi phạm là bắt buộc.')
    } else if (form.code.length > 20) {
        errors.push('Mã loại vi phạm không được vượt quá 20 ký tự.')
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
    getTypes()
})

// Khi component mounted, lấy danh sách Type
onMounted(async () => {
    await getTypes()
})
</script>