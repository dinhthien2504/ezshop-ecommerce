<template>
    <main class="admin__content">
        <div
            class="p-3 bg-white border-bottom text-grey fs-14 fw-medium d-flex align-items-center justify-content-between">
            <p class="m-0">
                Danh Sách Vai Trò (<span class="text-color">{{ totalRole }}</span>)
            </p>
            <div class="d-flex align-items-center gap-2">
                <div class="admin__search">
                    <button @click="onSearch" type="button" class="search__button">
                        <i class="ri-search-2-line"></i>
                    </button>
                    <form @submit.prevent="onSearch">
                        <div class="d-flex align-items-center gap-1 position-relative">
                            <button type="submit" style="height: 38px" class="main-btn py-1 px-3 ">
                                <i class="ri-search-2-line"></i>
                            </button>
                            <input type="text" v-model="searchValue" placeholder="Tìm vai trò..."
                                class="form-control" />
                            <button v-if="searchValue" type="button" @click="searchValue = ''; onSearch()"
                                class="btn position-absolute top-50 end-0 translate-middle-y me-2 p-0 border-0 bg-transparent">
                                <i class="ri-close-line"></i>
                            </button>
                        </div>
                    </form>
                </div>
                <button @click="showAddModal" type="button" class="main-btn px-3" style="height: 38px;">
                    <span class="d-none d-lg-block">Thêm Vai Trò</span>
                    <span class="d-block d-lg-none"><i class="ri-add-fill"></i></span>
                </button>
            </div>
        </div>
        <div class="p-10 admin-user">
            <div v-if="loading.fetch" style="height: 60vh;"
                class="d-flex align-items-center justify-content-center">
                <loading__dot />
            </div>
            <div v-else class="table-responsive fade-in">
                <div v-if="roles.length === 0">
                    <no_data />
                </div>
                <table v-else class="table border">
                    <thead class="table-active" style="height: 50px;">
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
                            <th scope="col" class="fs-14 fw-semibold min-w-200">Vai Trò</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-200">Mô Tả</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-200">Quyền</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-150">Trạng Thái</th>
                            <th scope="col" class="fs-14 fw-semibold text-center min-w-100">Thao
                                Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="role in roles">
                            <td scope="row" class="align-middle py-4">
                                <div class="checkbox me-2">
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="check-item" :value="role.id"
                                            v-model="selectedRoles" />
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

                            <td class="align-middle py-4">{{ role.title }}</td>
                            <td class="align-middle py-4">{{ role.description }}</td>
                            <td class="align-middle py-4">
                                <span v-if="role.permissions.length != 0" v-for="per in role.permissions" class="badge radius-2 text-bg-danger m-1">{{ per.description }}</span>
                                <span v-else class="badge radius-2 text-bg-secondary m-1">Vô năng</span>
                            </td>
                            <td class="align-middle py-4">
                                <span v-if="role.role_status === 1"
                                    class="badge radius-2 text-bg-success">Hoạt động</span>
                                <span v-else class="badge radius-2 text-bg-warning">Tạm ngừng</span>
                            </td>
                            <td class="align-middle py-4 text-center">
                                <div class="more-wrapper">
                                    <button class="more-btn">
                                        <i class="ri-more-fill"></i>
                                    </button>
                                    <div class="more-popup" style="top: -70px;">
                                        <button @click="showPermissionModal(role.id)"
                                            class="bg-primary text-light">Quyền</button>
                                        <button v-if="role.role_status === 0" @click="changeStatus(role.id)"
                                            class="bg-success text-light">Mở</button>
                                        <button v-else @click="changeStatus(role.id)" class="bg-warning">Khóa</button>
                                        <button class="bg-warning" @click="showEditModal(role.id)">Sửa</button>
                                        <button @click="showConfirmModal(role.id)"
                                            class="bg-danger text-light">Xóa</button>
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
                                <div class="more-wrapper ">
                                    <button class="more-btn p-0">
                                        <i class="ri-more-fill"></i>
                                    </button>
                                    <div class="more-popup">
                                        <button @click="showConfirmModal()" class="text-white bg-danger px-2 py-0">
                                            Xóa Tất Cả
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

        <!-- Modal Permission -->
        <div v-if="isPermissionModal"
            class="modal-background container-fluid p-0 d-flex justify-content-center align-items-center">
            <div class="modal-edit-address modal-ezeshop bg-white shadow-sm px-4 pt-4 pb-2">
                <div class="modal-ezeshop__top d-flex justify-content-between align-items-center">
                    <div class="modal-ezeshop__name fs-18 fw-500">Phân Quyền</div>
                    <div @click="isPermissionModal = false" class="modal-ezeshop__cancel cursor-pointer"><i
                            class="fa-solid fa-xmark fs-18"></i></div>
                </div>
                <!-- Content -->
                <div class="modal-ezeshop__main w-100 p-1">
                    <form @submit.prevent="submitPermission" id="form-edit-cate" enctype="multipart/form-data">
                        <div v-for="per in permissions" class="mb-3 d-flex align-items-center">
                            <label class="switch">
                                <input type="checkbox" :checked="per.permission_value == 1"
                                    @change="per.permission_value = per.permission_value == 1 ? 0 : 1" />
                                <span class="slider round"></span>
                            </label>
                            <label for="name" class="form-label fs-16 my-auto ms-3">{{ per.title }}</label>
                        </div>

                    </form>
                </div>
                <div class="modal-ezeshop__bottom d-flex justify-content-end w-100 align-items-center border-top">
                    <div class="modal-ezeshop__cancel me-4"><button @click="isPermissionModal = false"
                            class="white-btn py-2 px-4 fs-14">Huỷ</button></div>
                    <div class="modal-ezeshop__save"><button form="form-edit-cate" class="main-btn py-2 px-4 fs-14"
                            type="submit">
                            <loading__loader_circle size="15px" color="#fff" border="2px" v-if="loading.permission" />
                            <div v-else>Lưu</div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Modal Permission -->

        <!-- Modal add -->
        <div v-if="isAddModal"
            class="modal-background container-fluid p-0 d-flex justify-content-center align-items-center">
            <div class="modal-edit-address modal-ezeshop bg-white shadow-sm px-4 pt-4 pb-2">
                <div class="modal-ezeshop__top d-flex justify-content-between align-items-center">
                    <div class="modal-ezeshop__name fs-18 fw-500">Thêm Vai Trò</div>
                    <div @click="isAddModal = false" class="modal-ezeshop__cancel cursor-pointer"><i
                            class="fa-solid fa-xmark fs-18"></i></div>
                </div>
                <!-- Content -->
                <div class="modal-ezeshop__main w-100 p-1">
                    <form @submit.prevent="submitAddRole" id="form-edit-cate" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="name" class="form-label fs-14">Vai Trò<span class="text-color">*</span></label>
                            <input v-model="newRole.title" placeholder="Nhập vai trò..." type="text"
                                class="form-control form-control__input">
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label fs-14">Mô Tả</label>
                            <textarea v-model="newRole.description" type="text"
                                placeholder="Người tiện tay vẽ hoa vẽ lá..."
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
        <!-- End Modal add -->

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
                            @click="isDeletingMultiple ? deleteSelectedRoles() : deleteRole()"
                            class="main-btn py-2 px-4 fs-14" type="submit">
                            <loading__loader_circle size="15px" color="#fff" border="2px" v-if="loading.delete" />
                            <div v-else>Xác Nhận</div>
                        </button></div>
                </div>
            </div>
        </div>
        <!-- End Confirm Delete Modal -->

        <!-- Modal edit -->
        <div v-if="isEditModal"
            class="modal-background container-fluid p-0 d-flex justify-content-center align-items-center">
            <div class="modal-edit-address modal-ezeshop bg-white shadow-sm px-4 pt-4 pb-2">
                <div class="modal-ezeshop__top d-flex justify-content-between align-items-center">
                    <div class="modal-ezeshop__name fs-18 fw-500">Sửa Vai Trò</div>
                    <div @click="isEditModal = false" class="modal-ezeshop__cancel cursor-pointer"><i
                            class="fa-solid fa-xmark fs-18"></i></div>
                </div>
                <!-- Content -->
                <div class="modal-ezeshop__main w-100 p-1">
                    <form @submit.prevent="submitEditRole(editRole.id)" id="form-edit-cate"
                        enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="name" class="form-label fs-14">Vai Trò<span class="text-color">*</span></label>
                            <input v-model="editRole.title" placeholder="Nhập vai trò..." type="text"
                                class="form-control form-control__input">
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label fs-14">Mô Tả</label>
                            <textarea v-model="editRole.description" type="text"
                                placeholder="Người tiện tay vẽ hoa vẽ lá..."
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
        <!-- End Modal edit -->
    </main>
</template>
<script setup>
// Import các thư viện và component cần thiết
import { ref, onMounted, watch } from 'vue'
import api from '@/configs/api'
import pagination from '@/components/pagination.vue'
import message from '@/utils/messageState'
import loading__dot from '@/components/loading/loading__dot.vue'
import loading__loader_circle from '@/components/loading/loading__loader-circle.vue'
import no_data from '@/components/no_data.vue'


// Biến phản ứng cho phân trang, tìm kiếm, dữ liệu vai trò, tổng số vai trò
const meta = ref({})
const currentPage = ref(1) // Trang hiện tại
const searchValue = ref('') // Giá trị tìm kiếm
const roles = ref([]) // Danh sách vai trò
const totalRole = ref(0) // Tổng số vai trò

// Biến cho phân quyền
const permissions = ref([]) // Danh sách quyền
const selectedRoleId = ref(null) // Id vai trò đang phân quyền

// Biến cho chọn/xóa nhiều vai trò
const selectedRoles = ref([]) // Danh sách vai trò được chọn
const checkAll = ref(false) // Checkbox chọn tất cả

// Biến cho modal và trạng thái loading
const loading = ref({
    fetch: false,
    save_add: false,
    save_edit: false,
    delete: false,
    permission: false
})

const isDeleteModal = ref(false) // Hiển thị modal xác nhận xóa
const roleIdToDelete = ref(null) // Id vai trò cần xóa
const isDeletingMultiple = ref(false) // Đang xóa nhiều vai trò

// Biến cho modal thêm vai trò
const isAddModal = ref(false)
const newRole = ref({
    title: '',
    description: ''
})

// Biến cho modal sửa vai trò
const isEditModal = ref(false)
const editRole = ref({
    id: 0,
    title: '',
    description: ''
})

// Biến cho modal phân quyền
const isPermissionModal = ref(false)

// ================== XỬ LÝ CHỨC NĂNG ==================

// Hiển thị modal xác nhận xóa (1 hoặc nhiều)
const showConfirmModal = (id = null) => {
    isDeleteModal.value = true
    roleIdToDelete.value = id
    isDeletingMultiple.value = !id
}

// Xóa 1 vai trò
const deleteRole = async () => {
    try {
        loading.value.delete = true
        await api.delete(`/role/${roleIdToDelete.value}`)
        message.emit('show-message', {
            title: 'Thành công',
            text: 'Xóa thành công',
            type: 'success'
        })
        getRoles()
        selectedRoles.value = []
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

// Xóa nhiều vai trò đã chọn
const deleteSelectedRoles = async () => {
    if (selectedRoles.value.length === 0) {
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
        await api.delete('/role/delete-multiple', {
            data: { ids: selectedRoles.value }
        })
        message.emit('show-message', {
            title: 'Thành công',
            text: 'Đã xóa các vai trò đã chọn',
            type: 'success'
        })
        selectedRoles.value = []
        checkAll.value = false
        getRoles()
    } catch (error) {
        message.emit('show-message', {
            title: 'Thất bại',
            text: 'Xóa thất bại',
            type: 'error'
        })
    } finally {
        isDeleteModal.value = false
        roleIdToDelete.value = null
        isDeletingMultiple.value = false
        loading.value.delete = false
    }
}

// Chọn hoặc bỏ chọn tất cả vai trò
const toggleAllSelection = () => {
    checkAll.value
        ? selectedRoles.value = roles.value.map(role => role.id)
        : selectedRoles.value = []
}

// Theo dõi selectedRoles để cập nhật trạng thái checkAll
watch(selectedRoles, (newSelected) => {
    if (newSelected.length === roles.value.length && roles.value.length > 0) {
        checkAll.value = true
    } else {
        checkAll.value = false
    }
})

// Hiển thị modal thêm vai trò
const showAddModal = () => {
    newRole.value = {}
    isAddModal.value = true
}

// Thêm vai trò mới
const submitAddRole = async () => {
    const isValid = validateAddRoleForm(newRole.value, roles.value)
    if (!isValid) return
    try {
        loading.value.save_add = true
        await api.post('/role', newRole.value)
        message.emit('show-message', {
            title: 'Thành công',
            text: 'Thêm vai trò thành công',
            type: 'success'
        })
        isAddModal.value = false
        newRole.value = { title: '', description: '' }
        getRoles()
    } catch (err) {
        console.error(err)
        message.emit('show-error', {
            title: 'Thất bại',
            text: 'Không thể thêm vai trò',
            type: 'error'
        })
    } finally {
        loading.value.save_add = false
    }
}

// Hiển thị modal sửa vai trò
const showEditModal = async (id) => {
    isEditModal.value = true
    const roleToEdit = roles.value.find(role => role.id === id)
    if (!roleToEdit) return
    editRole.value.id = roleToEdit.id
    editRole.value.title = roleToEdit.title
    editRole.value.description = roleToEdit.description
}

// Sửa vai trò
const submitEditRole = async (id) => {
    if (!validateRoleForm(editRole.value)) return
    try {
        loading.value.save_edit = true
        const payload = {
            title: editRole.value.title,
            description: editRole.value.description
        }
        await api.put(`/role/${id}`, payload)
        message.emit('show-message', {
            title: 'Thành công',
            text: 'Cập nhật thành công',
            type: 'success'
        })
        isEditModal.value = false
        await getRoles()
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

// Hiển thị modal phân quyền
const showPermissionModal = async (id) => {
    isPermissionModal.value = true
    selectedRoleId.value = id
    try {
        const res = await api.get(`/role/permissions/${id}`)
        permissions.value = res.data.permissions
    } catch (error) {
        console.error(error)
    }
}

// Đổi trạng thái vai trò (hoạt động/tạm ngừng)
const changeStatus = async (id) => {
    try {
        await api.post(`/role/change-status/${id}`)
        getRoles()
        message.emit('show-message', {
            title: 'Thành công',
            text: 'Đổi trạng thái thành công',
            type: 'success'
        })
    } catch (err) {
        console.error(err)
    }
}

// Lưu phân quyền cho vai trò
const submitPermission = async () => {
    try {
        loading.value.permission = true
        const data = {
            permissions: permissions.value.map(per => ({
                id: per.id,
                value: per.permission_value
            }))
        }
        await api.post(`/role/permissions/${selectedRoleId.value}`, data)
        isPermissionModal.value = false
        getRoles()
        message.emit('show-message', {
            title: 'Thành công',
            text: 'Phân quyền thành công',
            type: 'success'
        })
    } catch (err) {
        console.error(err)
        message.emit('show-error', {
            title: 'Thất bại',
            text: 'Phân quyền thất bại',
            type: 'error'
        })
    } finally {
        loading.value.permission = false
    }
}

// Đổi trang phân trang
const onChangePage = (page) => {
    currentPage.value = page
}

// Tìm kiếm vai trò
const onSearch = () => {
    currentPage.value = 1
    getRoles()
}

// Lấy danh sách vai trò từ API
const getRoles = async () => {
    try {
        loading.value.fetch = true
        const res = await api.get('/role', {
            params: {
                page: currentPage.value,
                search: searchValue.value,
            }
        })
        roles.value = res.data.roles
        totalRole.value = res.data.total
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

// Hàm kiểm tra hợp lệ form vai trò
function validateRoleForm(form) {
    const errors = []

    // 1. Kiểm tra tên vai trò
    if (!form.title || !form.title.trim()) {
        errors.push("Tên vai trò là bắt buộc.")
    } else if (form.title.length > 255) {
        errors.push("Tên vai trò không được vượt quá 255 ký tự.")
    }

    // 2. Mô tả (nếu có)
    if (form.description && typeof form.description !== 'string') {
        errors.push("Mô tả phải là chuỗi.")
    } else if (form.description && form.description.length > 1000) {
        errors.push("Mô tả không được vượt quá 1000 ký tự.")
    }

    // 3. Trạng thái (nếu có)
    if (form.role_status !== undefined && typeof form.role_status !== 'boolean') {
        errors.push("Trạng thái phải là true hoặc false.")
    }

    // 4. Thông báo lỗi
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

function validateAddRoleForm(form, roles) {
    const errors = []

    // 1. Kiểm tra tên vai trò
    if (!form.title || !form.title.trim()) {
        errors.push("Tên vai trò là bắt buộc.")
    } else if (form.title.length > 255) {
        errors.push("Tên vai trò không được vượt quá 255 ký tự.")
    } else {
        // Kiểm tra trùng tiêu đề trong danh sách roles
        const titleExists = roles.some(role => role.title.toLowerCase().trim() === form.title.toLowerCase().trim())
        if (titleExists) {
            errors.push("Tên vai trò đã tồn tại.")
        }
    }

    // 2. Mô tả (nếu có)
    if (form.description && typeof form.description !== 'string') {
        errors.push("Mô tả phải là chuỗi.")
    } else if (form.description && form.description.length > 1000) {
        errors.push("Mô tả không được vượt quá 1000 ký tự.")
    }

    // 3. Trạng thái (nếu có)
    if (form.role_status !== undefined && typeof form.role_status !== 'boolean') {
        errors.push("Trạng thái phải là true hoặc false.")
    }

    // 4. Hiển thị lỗi
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


// Theo dõi thay đổi trang để lấy lại dữ liệu
watch(currentPage, () => {
    getRoles()
})

// Khi component mounted, lấy danh sách vai trò
onMounted(async () => {
    await getRoles()
})
</script>
<style scoped>
.switch {
    position: relative;
    display: inline-block;
    width: 35px;
    /* giảm từ 60 */
    height: 20px;
    /* giảm từ 34 */
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: 0.4s;
}

.slider:before {
    position: absolute;
    content: "";
    height: 16px;
    /* giảm từ 26 */
    width: 16px;
    /* giảm từ 26 */
    left: 2px;
    /* giảm từ 4 */
    bottom: 2px;
    /* giảm từ 4 */
    background-color: white;
    transition: 0.4s;
}

input:checked+.slider {
    background-color: var(--main-color) !important;
}

input:checked+.slider:before {
    transform: translateX(15px);
    /* giảm từ 26px */
}

.slider.round {
    border-radius: 20px;
}

.slider.round:before {
    border-radius: 50%;
}
</style>