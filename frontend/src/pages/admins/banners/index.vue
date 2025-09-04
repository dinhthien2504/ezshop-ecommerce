<template>
    <main class="admin__content">
        <div
            class="p-3 bg-white border-bottom text-grey fs-14 fw-medium d-flex align-items-center justify-content-between">
            <p class="m-0">Danh Sách Banner (<span class="text-color">{{ totalBanner }}</span>)</p>
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
                            <input type="text" v-model="searchValue" placeholder="Tìm banner..." class="form-control" />
                            <button v-if="searchValue" type="button" @click="searchValue = ''; onSearch()"
                                class="btn position-absolute top-50 end-0 translate-middle-y me-2 p-0 border-0 bg-transparent">
                                <i class="ri-close-line"></i>
                            </button>
                        </div>
                    </form>
                </div>
                <button type="button" @click="showAddModal" class="main-btn px-3" style="height: 38px">
                    <span class="d-none d-lg-block">Thêm Banner</span>
                    <span class="d-block d-lg-none"><i class="ri-add-fill"></i></span>
                </button>
            </div>
        </div>
        <div class="p-10">
            <div v-if="loading.fetch" style="height: 60vh;" class="d-flex align-items-center justify-content-center">
                <loading__dot />
            </div>
            <div v-else class="table-responsive fade-in">
                <div v-if="banners.length === 0">
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
                            <th scope="col" class="fs-14 fw-semibold min-w-200">Hình Ảnh</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-200">Tiêu Đề</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-150">Vị Trí</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-150">Độ Ưu Tiên</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-150">Ngày Bắt Đầu</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-150">Ngày Kết Thúc</th>
                            <th scope="col" class="fs-14 fw-semibold text-center min-w-100">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="banner in banners">
                            <td scope="row" class="align-middle">
                                <div class="checkbox me-2">
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="check-item" :value="banner.id"
                                            v-model="selectedBanners" />
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
                            <td class="align-middle">
                                <img style="width: 200px; aspect-ratio: 21 / 9; object-fit: cover"
                                    :src="`/imgs/banners/${banner.image}`" alt />
                            </td>
                            <td class="align-middle">{{ banner.title }}</td>
                            <td v-if="banner.position" class="align-middle">{{ banner.position }}</td>
                            <td v-else class="align-middle">Chưa chọn</td>
                            <td class="align-middle">{{ banner.priority }}</td>
                            <td class="align-middle">{{ formatDate(banner.start_at) }}</td>
                            <td class="align-middle">{{ formatDate(banner.end_at) }}</td>

                            <td class="align-middle text-center">
                                <div class="more-wrapper">
                                    <button class="more-btn">
                                        <i class="ri-more-fill"></i>
                                    </button>
                                    <div class="more-popup">
                                        <button class="bg-warning" @click="showEditModal(banner.id)">Sửa</button>
                                        <button class="text-white bg-danger"
                                            @click="showConfirmModal(banner.id)">Xóa</button>
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
                            <th scope="col"></th>
                            <th scope="col"></th>
                            <td class="align-middle text-center">
                                <div class="more-wrapper">
                                    <button class="more-btn p-0">
                                        <i class="ri-more-fill"></i>
                                    </button>
                                    <div class="more-popup">
                                        <button class="text-white bg-danger px-2 py-0" @click="showConfirmModal()">
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
            <div class="modal-edit-address modal-ezeshop bg-white shadow-sm px-4 pt-4 pb-2" style="width: 600px;">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Thêm Banner</h5>
                    <i class="fa-solid fa-xmark fs-18 cursor-pointer" @click="isAddModal = false"></i>
                </div>

                <!-- Nội dung form -->
                <form id="form-add-banner" @submit.prevent="addBanner">
                    <!-- Ảnh banner -->
                    <div class="mb-3 text-center">
                        <!-- Khung ảnh có thể nhấp -->
                        <div class="img-upload-frame border rounded position-relative d-inline-block"
                            style="width: 100%; aspect-ratio: 21 / 9; cursor: pointer; overflow: hidden;"
                            @click="triggerFileInput">
                            <img v-if="imagePreview" :src="imagePreview" class="w-100 h-100"
                                style="object-fit: cover;" />
                            <div v-else
                                class="w-100 h-100 d-flex justify-content-center align-items-center text-muted bg-light">
                                Nhấp để chọn ảnh<span class="text-color">*</span>
                            </div>
                        </div>

                        <!-- Input file ẩn -->
                        <input type="file" ref="fileInput" @change="onImageChange" accept="image/*" class="d-none" />
                    </div>

                    <div class="mb-3 row">
                        <div class="col-12 col-md-6">
                            <label class="form-label">Tiêu Đề<span class="text-color">*</span></label>
                            <input type="text" class="form-control" v-model="form.title"
                                placeholder="Nhập tiêu đề banner" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Link</label>
                            <input type="text" class="form-control" placeholder="Nhập đường dẫn của banner"
                                v-model="form.link" />
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <div class="col-12 col-md-6">
                            <label class="form-label">Vị Trí</label>
                            <select class="form-select form-control" v-model="form.position">
                                <option value="" selected>-- Chọn vị trí --</option>
                                <option value="home_1">Home 1</option>
                                <option value="home_2">Home 2</option>
                                <option value="home_3">Home 3</option>
                                <option value="mobile_1">Mobile 1</option>
                                <option value="mobile_2">Mobile 2</option>
                                <option value="mobile_3">Mobile 3</option>
                                <option value="slider">Slider</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Độ Ưu tiên</label>
                            <input type="number" min="0" step="1" max="10" class="form-control"
                                v-model="form.priority" />
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <div class="col-12 col-md-6">
                            <label class="form-label">Ngày Bắt Đầu</label>
                            <input type="date" class="form-control" v-model="form.start_at" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Ngày Kết Thúc</label>
                            <input type="date" class="form-control" v-model="form.end_at" />
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="modal-ezeshop__bottom d-flex justify-content-end w-100 align-items-center border-top">
                        <div class="modal-ezeshop__cancel me-4"><button @click="isAddModal = false"
                                class="white-btn py-2 px-4 fs-14">Huỷ</button></div>
                        <div class="modal-ezeshop__save"><button class="main-btn py-2 px-4 fs-14" type="submit">
                                <loading__loader_circle size="15px" color="#fff" border="2px" v-if="loading.save_add" />
                                <div v-else>Thêm</div>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- End Modal add -->

        <!-- Modal edit -->
        <div v-if="isEditModal"
            class="modal-background container-fluid p-0 d-flex justify-content-center align-items-center">
            <div class="modal-edit-address modal-ezeshop bg-white shadow-sm px-4 pt-4 pb-2" style="width: 600px;">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Sửa Banner</h5>
                    <i class="fa-solid fa-xmark fs-18 cursor-pointer" @click="isEditModal = false"></i>
                </div>

                <!-- Nội dung form -->
                <form id="form-add-banner" @submit.prevent="editBanner()">
                    <!-- Ảnh banner -->
                    <div class="mb-3 text-center">
                        <!-- Khung ảnh có thể nhấp -->
                        <div class="img-upload-frame border rounded position-relative d-inline-block"
                            style="width: 100%; aspect-ratio: 21 / 9; cursor: pointer; overflow: hidden;"
                            @click="triggerFileInput">
                            <img v-if="imagePreview" :src="imagePreview" class="w-100 h-100"
                                style="object-fit: cover;" />
                            <div v-else
                                class="w-100 h-100 d-flex justify-content-center align-items-center text-muted bg-light">
                                Nhấp để chọn ảnh<span class="text-color">*</span>
                            </div>
                        </div>

                        <!-- Input file ẩn -->
                        <input type="file" ref="fileInput" @change="onImageChange" accept="image/*" class="d-none" />
                    </div>

                    <div class="mb-3 row">
                        <div class="col-12 col-md-6">
                            <label class="form-label">Tiêu Đề<span class="text-color">*</span></label>
                            <input type="text" class="form-control" v-model="form.title"
                                placeholder="Nhập tiêu đề banner" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Link</label>
                            <input type="text" class="form-control" placeholder="Nhập đường dẫn của banner"
                                v-model="form.link" />
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <div class="col-12 col-md-6">
                            <label class="form-label">Vị Trí</label>
                            <select class="form-select form-control" v-model="form.position">
                                <option value="" selected>-- Chọn vị trí --</option>
                                <option value="home_1">Home 1</option>
                                <option value="home_2">Home 2</option>
                                <option value="home_3">Home 3</option>
                                <option value="mobile_1">Mobile 1</option>
                                <option value="mobile_2">Mobile 2</option>
                                <option value="mobile_3">Mobile 3</option>
                                <option value="slider">Slider</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Độ Ưu tiên</label>
                            <input type="number" min="0" step="1" max="10" class="form-control"
                                v-model="form.priority" />
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <div class="col-12 col-md-6">
                            <label class="form-label">Ngày Bắt Đầu</label>
                            <input type="date" class="form-control" v-model="form.start_at" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Ngày Kết Thúc</label>
                            <input type="date" class="form-control" v-model="form.end_at" />
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="modal-ezeshop__bottom d-flex justify-content-end w-100 align-items-center border-top">
                        <div class="modal-ezeshop__cancel me-4"><button @click="isAddModal = false"
                                class="white-btn py-2 px-4 fs-14">Huỷ</button></div>
                        <div class="modal-ezeshop__save"><button class="main-btn py-2 px-4 fs-14" type="submit">
                                <loading__loader_circle size="15px" color="#fff" border="2px" v-if="loading.save_add" />
                                <div v-else>Lưu</div>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- End Modal edit -->

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
                            @click="isDeletingMultiple ? deleteSelectedBanners() : deleteBanner()"
                            class="main-btn py-2 px-4 fs-14" type="submit">
                            <loading__loader_circle size="15px" color="#fff" border="2px" v-if="loading.delete" />
                            <div v-else>Xác Nhận</div>
                        </button></div>
                </div>
            </div>
        </div>
        <!-- End Confirm Delete Modal -->

    </main>
</template>
<script setup>
// Import các thư viện và component cần thiết
import { ref, onMounted, watch } from 'vue'
import api from '@/configs/api'
import { formatDate } from '@/utils/formatDate'
import pagination from '@/components/pagination.vue'
import message from '@/utils/messageState'
import loading__dot from '@/components/loading/loading__dot.vue'
import loading__loader_circle from '@/components/loading/loading__loader-circle.vue'
import no_data from '@/components/no_data.vue'

// =======================
// Biến trạng thái & dữ liệu
// =======================

// Dữ liệu phân trang
const meta = ref({})
const currentPage = ref(1) // Trang hiện tại
const searchValue = ref('') // Giá trị tìm kiếm
const banners = ref([]) // Danh sách banner
const totalBanner = ref(0) // Tổng số banner

// Trạng thái loading cho các thao tác
const loading = ref({
    fetch: false,      // Đang tải dữ liệu
    save_add: false,   // Đang thêm banner
    save_edit: false,  // Đang sửa banner
    delete: false      // Đang xóa banner
})

// =======================
// Xử lý phân trang & tìm kiếm
// =======================

// Đổi trang
const onChangePage = (page) => {
    currentPage.value = page
}

// Tìm kiếm
const onSearch = () => {
    currentPage.value = 1
    getBanners()
}

// =======================
// Modal Thêm Banner
// =======================

const isAddModal = ref(false) // Hiện modal thêm
const form = ref({
    title: '',
    image: null,
    link: '',
    priority: 0,
    position: '',
    start_at: '',
    end_at: ''
})

// Hiển thị modal thêm và reset form
const showAddModal = () => {
    resetForm()
    isAddModal.value = true
}

// Reset form về trạng thái ban đầu
function resetForm() {
    form.value = {
        title: '',
        image: null,
        link: '',
        priority: 0,
        position: '',
        start_at: '',
        end_at: ''
    }
    imagePreview.value = null
    bannerIdToEdit.value = null
    if (fileInput.value) {
        fileInput.value.type = 'text'
        fileInput.value.type = 'file'
    }
}

// =======================
// Xử lý ảnh upload
// =======================

const imagePreview = ref(null) // Ảnh xem trước
const fileInput = ref(null)    // Tham chiếu input file

// Kích hoạt input file khi click vào khung ảnh
function triggerFileInput() {
    fileInput.value.click()
}

// Xử lý khi chọn ảnh mới
function onImageChange(event) {
    const file = event.target.files[0]
    if (file) {
        form.value.image = file
        imagePreview.value = URL.createObjectURL(file)
    }
}

// =======================
// Lấy danh sách banner từ API
// =======================

const getBanners = async () => {
    try {
        loading.value.fetch = true
        const res = await api.get('/banner', {
            params: {
                page: currentPage.value,
                search: searchValue.value,
            }
        })
        banners.value = res.data.banners
        totalBanner.value = res.data.total
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

// =======================
// Thêm banner mới
// =======================

const addBanner = async () => {
    if (!validateAddForm()) return
    try {
        loading.value.save_add = true
        const formData = new FormData()
        formData.append('title', form.value.title)
        formData.append('image', form.value.image)
        formData.append('link', form.value.link)
        formData.append('position', form.value.position)
        formData.append('priority', form.value.priority)
        formData.append('start_at', form.value.start_at)
        formData.append('end_at', form.value.end_at)

        const res = await api.post('/banner', formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
        })

        message.emit('show-message', {
            title: 'Thành công',
            text: 'Thêm banner thành công',
            type: 'success'
        })
        isAddModal.value = false
        resetForm()
        await getBanners()
    } catch (error) {
        console.error('Thêm banner thất bại:', error)
    } finally {
        loading.value.save_add = false
    }
}

// =======================
// Xóa banner (1 hoặc nhiều)
// =======================

const isDeleteModal = ref(false)        // Hiện modal xác nhận xóa
const bannerIdToDelete = ref(null)      // ID banner cần xóa
const isDeletingMultiple = ref(false)   // Đang xóa nhiều banner

const selectedBanners = ref([])         // Danh sách banner được chọn
const checkAll = ref(false)             // Trạng thái chọn tất cả

// Hiện modal xác nhận xóa
const showConfirmModal = (id = null) => {
    isDeleteModal.value = true
    bannerIdToDelete.value = id
    isDeletingMultiple.value = !id
}

// Xóa 1 banner
const deleteBanner = async () => {
    try {
        loading.value.delete = true
        await api.delete(`/banner/${bannerIdToDelete.value}`)
        message.emit('show-message', {
            title: 'Thành công',
            text: 'Xóa banner thành công',
            type: 'success'
        })
        getBanners()
        selectedBanners.value = []
        checkAll.value = false
    } catch (error) {
        message.emit('show-message', {
            title: 'Thất bại',
            text: 'Xóa banner thất bại',
            type: 'error'
        })
    } finally {
        isDeleteModal.value = false
        isDeletingMultiple.value = false
        loading.value.delete = false
    }
}

// Xóa nhiều banner đã chọn
const deleteSelectedBanners = async () => {
    if (selectedBanners.value.length === 0) {
        message.emit('show-message', {
            title: 'Chú ý',
            text: 'Chưa chọn banner nào để xóa',
            type: 'warning'
        })
        isDeleteModal.value = false
        checkAll.value = false
        return
    }
    try {
        loading.value.delete = true
        isDeletingMultiple.value = true
        await api.delete('/banner/delete-multiple', {
            data: { ids: selectedBanners.value }
        })
        message.emit('show-message', {
            title: 'Thành công',
            text: 'Đã xóa các banner đã chọn',
            type: 'success'
        })
        selectedBanners.value = []
        checkAll.value = false
        getBanners()
    } catch (error) {
        message.emit('show-message', {
            title: 'Thất bại',
            text: 'Xóa banner thất bại',
            type: 'error'
        })
    } finally {
        isDeleteModal.value = false
        bannerIdToDelete.value = null
        isDeletingMultiple.value = false
        loading.value.delete = false
    }
}

// Chọn hoặc bỏ chọn tất cả banner
const toggleAllSelection = () => {
    checkAll.value
        ? selectedBanners.value = banners.value.map(banner => banner.id)
        : selectedBanners.value = []
}

// =======================
// Modal Sửa Banner
// =======================

const isEditModal = ref(false)      // Hiện modal sửa
const bannerIdToEdit = ref(null)    // ID banner đang sửa

// Hiện modal sửa và gán dữ liệu banner vào form
const showEditModal = (id) => {
    const bannerToEdit = banners.value.find(banner => banner.id === id)
    if (!bannerToEdit) return

    bannerIdToEdit.value = id
    isEditModal.value = true

    form.value = {
        title: bannerToEdit.title || '',
        image: null, // Không giữ ảnh cũ
        link: bannerToEdit.link || '',
        position: bannerToEdit.position || '',
        priority: bannerToEdit.priority ?? 0,
        start_at: toDateInputValue(bannerToEdit.start_at),
        end_at: toDateInputValue(bannerToEdit.end_at)
    }
    // Hiển thị ảnh cũ nếu có
    imagePreview.value = bannerToEdit.image ? `/imgs/banners/${bannerToEdit.image}` : null
}

// Sửa banner
const editBanner = async () => {
    if (!validateEditForm()) return
    try {
        loading.value.save_edit = true
        const formData = new FormData()
        formData.append('title', form.value.title ?? '')
        formData.append('link', form.value.link ?? '')
        formData.append('position', form.value.position ?? '')
        formData.append('priority', form.value.priority ?? 0)
        formData.append('start_at', form.value.start_at ?? '')
        formData.append('end_at', form.value.end_at ?? '')
        // Chỉ thêm ảnh nếu có chọn mới
        if (form.value.image) {
            formData.append('image', form.value.image)
        }
        formData.append('_method', 'PUT') // Laravel PUT với multipart

        await api.post(`/banner/${bannerIdToEdit.value}`, formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
        })

        message.emit('show-message', {
            title: 'Thành công',
            text: 'Cập nhật banner thành công',
            type: 'success'
        })

        isEditModal.value = false
        resetForm()
        await getBanners()
    } catch (error) {
        const msg = error.response?.data?.message || 'Có lỗi xảy ra khi cập nhật'
        message.emit('show-message', {
            title: 'Lỗi',
            text: msg,
            type: 'error'
        })
        console.error('Sửa banner thất bại:', error.response?.data || error)
    } finally {
        loading.value.save_edit = false
    }
}

// =======================
// Validate Form
// =======================

// Kiểm tra form thêm banner
function validateAddForm() {
    const errors = []
    if (!form.value.title) {
        errors.push("Tiêu đề là bắt buộc.")
    }
    if (!form.value.image) {
        errors.push("Hình ảnh là bắt buộc.")
    }
    if (form.value.link && !isValidUrl(form.value.link)) {
        errors.push("Liên kết phải là một URL hợp lệ.")
    }
    const validPositions = ['home_1', 'home_2', 'home_3', 'mobile_1', 'mobile_2', 'mobile_3', 'slider']
    if (form.value.position && !validPositions.includes(form.value.position)) {
        errors.push("Vị trí không hợp lệ.")
    }
    if (form.value.priority < 0 || form.value.priority > 10) {
        errors.push("Ưu tiên phải từ 0 đến 10.")
    }
    if (form.value.start_at && isNaN(Date.parse(form.value.start_at))) {
        errors.push("Ngày bắt đầu không hợp lệ.")
    }
    if (form.value.end_at && isNaN(Date.parse(form.value.end_at))) {
        errors.push("Ngày kết thúc không hợp lệ.")
    }
    if (form.value.start_at && form.value.end_at) {
        const start = new Date(form.value.start_at)
        const end = new Date(form.value.end_at)
        if (end < start) {
            errors.push("Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.")
        }
    }
    if (errors.length > 0) {
        message.emit('show-message', {
            title: 'Lỗi',
            text: errors.join(' || '),
            type: 'error'
        })
        return false
    }
    return true
}

// Kiểm tra form sửa banner
function validateEditForm() {
    const errors = []
    if (!form.value.title) {
        errors.push("Tiêu đề là bắt buộc.")
    } else if (form.value.title.length > 255) {
        errors.push("Tiêu đề không được vượt quá 255 ký tự.")
    }
    if (form.value.image) {
        const file = form.value.image
        const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp']
        if (!validTypes.includes(file.type)) {
            errors.push("Hình ảnh phải có định dạng jpeg, png, jpg, gif hoặc webp.")
        }
        if (file.size > 2048 * 1024) {
            errors.push("Kích thước hình ảnh không được vượt quá 2048 KB.")
        }
    }
    if (form.value.link && !isValidUrl(form.value.link)) {
        errors.push("Liên kết phải là một URL hợp lệ.")
    }
    const validPositions = ['home_1', 'home_2', 'home_3', 'mobile_1', 'mobile_2', 'mobile_3', 'slider']
    if (form.value.position && !validPositions.includes(form.value.position)) {
        errors.push("Vị trí không hợp lệ.")
    }
    if (form.value.priority !== null && form.value.priority !== '') {
        const p = Number(form.value.priority)
        if (!Number.isInteger(p)) {
            errors.push("Ưu tiên phải là số nguyên.")
        } else if (p < 0 || p > 10) {
            errors.push("Ưu tiên phải từ 0 đến 10.")
        }
    }
    if (form.value.start_at && isNaN(Date.parse(form.value.start_at))) {
        errors.push("Ngày bắt đầu không hợp lệ.")
    }
    if (form.value.end_at && isNaN(Date.parse(form.value.end_at))) {
        errors.push("Ngày kết thúc không hợp lệ.")
    }
    if (form.value.start_at && form.value.end_at) {
        const start = new Date(form.value.start_at)
        const end = new Date(form.value.end_at)
        if (end < start) {
            errors.push("Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.")
        }
    }
    if (errors.length > 0) {
        message.emit('show-message', {
            title: 'Lỗi',
            text: errors.join(' || '),
            type: 'error'
        })
        return false
    }
    return true
}

// Kiểm tra URL hợp lệ
function isValidUrl(url) {
    try {
        new URL(url)
        return true
    } catch (_) {
        return false
    }
}

// Chuyển đổi ngày về dạng YYYY-MM-DD cho input date
function toDateInputValue(dateStr) {
    if (!dateStr) return ''
    const d = new Date(dateStr)
    return d.toISOString().split('T')[0]
}

// =======================
// Watchers - Theo dõi thay đổi
// =======================

// Theo dõi selectedBanners để cập nhật checkAll
watch(selectedBanners, (newSelected) => {
    if (newSelected.length === banners.value.length && banners.value.length > 0) {
        checkAll.value = true
    } else {
        checkAll.value = false
    }
})


// Theo dõi thay đổi trang để lấy lại dữ liệu
watch(currentPage, () => {
    getBanners()
})

// Khi component mounted, lấy danh sách banner
onMounted(async () => {
    await getBanners()
})
</script>
