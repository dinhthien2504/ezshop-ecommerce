<template>
    <main class="admin__content">
        <div
            class="p-3 bg-white border-bottom text-grey fs-14 fw-medium d-flex align-items-center justify-content-between">
            <p class="m-0">Danh Sách Sản Phẩm</p>
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
                            <input type="text" v-model="searchValue" placeholder="Tìm sản phẩm..."
                                class="form-control" />
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
                <button class="white-btn px-2 py-1 fs-14" :class="{ 'btn-active': filterStatus === null }"
                    @click="filterStatus = null">Tất Cả ({{ totalProduct }})</button>
                <button class="white-btn px-2 py-1 fs-14" :class="{ 'btn-active': filterStatus === 0 }"
                    @click="filterStatus = 0">Chờ Duyệt ({{ pendingProduct }})</button>
                <button class="white-btn px-2 py-1 fs-14" :class="{ 'btn-active': filterStatus === 1 }"
                    @click="filterStatus = 1">Hoạt Động ({{ activeProduct }})</button>
                <button class="white-btn px-2 py-1 fs-14" :class="{ 'btn-active': filterStatus === 2 }"
                    @click="filterStatus = 2">Tạm Ngừng ({{ pausedProduct }})</button>
                <button class="white-btn px-2 py-1 fs-14" :class="{ 'btn-active': filterStatus === 3 }"
                    @click="filterStatus = 3">Từ Chối ({{ rejectedProduct }})</button>
                <!-- <button class="btn btn-outline-secondary" :class="{ 'btn-active': filterStatus === 4 }"
                    @click="filterStatus = 4">Vi Phạm ({{ violationProduct }})</button> -->
            </div>
            <div v-if="loading.fetch" style="height: 60vh;" class="d-flex align-items-center justify-content-center">
                <loading__dot />
            </div>
            <div v-else class="table-responsive mt-2 fade-in">
                <div v-if="products.length === 0">
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
                            <th scope="col" class="fs-14 fw-semibold min-w-300">Tên Sản Phẩm</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-200">Cửa Hàng</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-150">Ngày Tạo</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-150">Trạng Thái</th>
                            <th scope="col" class="fs-14 fw-semibold text-center min-w-100">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="product in products">
                            <td scope="row" class="align-middle py-4">
                                <div class="checkbox me-2">
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="check-item" :value="product.id"
                                            v-model="selectedProducts" />
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

                            <td class="align-middle py-4"> {{ product.name }} </td>
                            <td class="align-middle py-4"> {{ product.shop }} </td>
                            <td class="align-middle py-4"> {{ formatDate(product.updated_at) }} </td>
                            <td class="align-middle py-4">
                                <span v-if="product.status === 0" class="badge radius-2 text-bg-info">Chờ duyệt</span>
                                <span v-else-if="product.status === 1" class="badge radius-2 text-bg-success">Hoạt động</span>
                                <span v-else-if="product.status === 2" class="badge radius-2 text-bg-warning">Tạm ngừng</span>
                                <span v-else-if="product.status === 3" class="badge radius-2 text-bg-danger">Từ chối</span>
                                <!-- <span v-else-if="product.status === 4" class="badge text-bg-danger">Vi phạm</span> -->
                            </td>
                            <td class="align-middle text-center py-4">
                                <div class="more-wrapper">
                                    <button class="more-btn">
                                        <i class="ri-more-fill"></i>
                                    </button>
                                    <div class="more-popup" :class="{ 'two-row': product.status === 0 }">
                                        <button v-if="product.status === 2" class="text-white bg-success"
                                            @click="changeStatus(product.id, 1)">Mở</button>
                                        <button v-if="product.status === 1" class="bg-warning"
                                            @click="showLockModal(product.id)">Khóa</button>
                                        <!-- <button class="text-white bg-danger">Xóa</button> -->
                                        <button v-if="product.status === 0 || product.status === 3"
                                            class="text-white bg-success"
                                            @click="changeStatus(product.id, 1)">Duyệt</button>
                                        <button v-if="product.status === 0" class="text-white bg-danger"
                                            @click="showRejectModal(product.id)">Từ Chối</button>
                                        <button @click="goTo(`/admin/san-pham/chi-tiet/${product.id}`)"
                                            class="bg-info">Chi Tiết</button>
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
                                        <button @click="showModal(1)"
                                            v-if="filterStatus == null || filterStatus == 0 || filterStatus == 2 || filterStatus == 3"
                                            class="text-white bg-success px-2 py-0"><span v-if="filterStatus == null">Mở
                                                / Duyệt</span><span v-else-if="filterStatus == 2">Mở</span><span
                                                v-else>Duyệt</span></button>
                                        <button @click="showModal(2)" v-if="filterStatus == null || filterStatus == 1"
                                            class="bg-warning px-2 py-0">Khóa</button>
                                        <!-- <button @click="showModal(3)" v-if="filterStatus == 0" class="text-white bg-danger px-2 py-0">Từ
                                            Chối</button> -->
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

        <!-- Confirm Modal -->
        <div v-if="isChangeModal"
            class="modal-background container-fluid p-0 d-flex justify-content-center align-items-center">
            <div class="modal-edit-address modal-ezeshop bg-white shadow-sm px-4 pt-4 pb-2" style="max-width: 500px;">
                <div class="modal-ezeshop__top d-flex justify-content-between align-items-center">
                    <div class="modal-ezeshop__name fs-18 fw-500">Xác Nhận
                        <span v-if="statusTarget == 1">Mở / Duyệt</span><span v-if="statusTarget == 2">Khóa</span><span
                            v-if="statusTarget == 3">Từ chối</span> sản phẩm
                    </div>
                    <div @click="isChangeModal = false" class="modal-ezeshop__cancel cursor-pointer"><i
                            class="fa-solid fa-xmark fs-18"></i></div>
                </div>
                <!-- Content -->
                <div class="modal-ezeshop__main w-100">
                    Bạn có chắc muốn <span v-if="statusTarget == 1">Mở / Duyệt</span><span
                        v-if="statusTarget == 2">Khóa</span><span v-if="statusTarget == 3">Từ chối</span> tất cả sản
                    phẩm đã chọn không?
                </div>
                <div class="mt-3 modal-ezeshop__bottom d-flex justify-content-end w-100 align-items-center border-top">
                    <div class="modal-ezeshop__cancel me-4"><button @click="isChangeModal = false"
                            class="white-btn py-2 px-4 fs-14">Huỷ</button></div>
                    <div class="modal-ezeshop__save"><button @click="changeSelected" class="main-btn py-2 px-4 fs-14"
                            type="submit">
                            <loading__loader_circle size="15px" color="#fff" border="2px" v-if="loading.change" />
                            <div v-else>Xác Nhận</div>
                        </button></div>
                </div>
            </div>
        </div>


        <!-- reject modal -->
        <div v-if="isRejectModal"
            class="modal-background container-fluid p-0 d-flex justify-content-center align-items-center">
            <div class="modal-edit-address modal-ezeshop bg-white shadow-sm px-4 pt-4 pb-2">
                <div class="modal-ezeshop__top d-flex justify-content-between align-items-center">
                    <div class="modal-ezeshop__name fs-18 fw-500">Xác Nhận Từ Chối</div>
                    <div @click="isRejectModal = false" class="modal-ezeshop__cancel cursor-pointer"><i
                            class="fa-solid fa-xmark fs-30"></i></div>
                </div>
                <!-- Content -->
                <div class="modal-ezeshop__main w-100 p-1 mb-4">
                    <div class="">
                        <label class="form-label fs-18 fw-medium">Lý do từ chối</label>
                        <textarea v-model="reason" maxlength="1000" style="min-height: 150px;" class="form-control"
                            placeholder="Người vô tình vẽ hoa vẽ lá, ta đa tình tưởng đó là tối đa 1000 ký tự..."></textarea>
                        <div class="text-end text-muted fs-14 mt-1">
                            {{ reason.length }}/1000 ký tự
                        </div>
                    </div>
                </div>
                <div class="modal-ezeshop__bottom d-flex justify-content-end w-100 align-items-center border-top">
                    <div class="modal-ezeshop__cancel me-3"><button
                            class="btn btn-outline-secondary py-2 px-4 fs-14 radius-2"
                            @click="isRejectModal = false">Hủy</button></div>
                    <div class="modal-ezeshop__save">
                        <button @click="confirmReject" form="form-edit-cate" class="main-btn py-2 px-4 fs-14 radius-2"
                            type="submit" :disabled="loadingReject">
                            <loading__loader_circle v-if="loadingReject" size="15px" color="#fff" border="2px" />
                            <span v-else>Xác nhận</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- lock modal -->
        <div v-if="isLockModal"
            class="modal-background container-fluid p-0 d-flex justify-content-center align-items-center">
            <div class="modal-edit-address modal-ezeshop bg-white shadow-sm px-4 pt-4 pb-2">
                <div class="modal-ezeshop__top d-flex justify-content-between align-items-center">
                    <div class="modal-ezeshop__name fs-18 fw-500">Xác Nhận Khóa</div>
                    <div @click="isLockModal = false" class="modal-ezeshop__cancel cursor-pointer"><i
                            class="fa-solid fa-xmark fs-30"></i></div>
                </div>
                <!-- Content -->
                <div class="modal-ezeshop__main w-100 p-1 mb-4">
                    <div class="">
                        <label class="form-label fs-18 fw-medium">Lý do khóa</label>
                        <textarea v-model="lockReason" maxlength="1000" style="min-height: 150px;" class="form-control"
                            placeholder="Nhập lý do khóa sản phẩm... tối đa 1000 ký tự"></textarea>
                        <div class="text-end text-muted fs-14 mt-1">
                            {{ lockReason.length }}/1000 ký tự
                        </div>
                    </div>
                </div>
                <div class="modal-ezeshop__bottom d-flex justify-content-end w-100 align-items-center border-top">
                    <div class="modal-ezeshop__cancel me-3"><button
                            class="btn btn-outline-secondary py-2 px-4 fs-14 radius-2"
                            @click="isLockModal = false">Hủy</button></div>
                    <div class="modal-ezeshop__save">
                        <button @click="confirmLock" form="form-edit-cate" class="main-btn py-2 px-4 fs-14 radius-2"
                            type="submit" :disabled="loadingLock">
                            <loading__loader_circle v-if="loadingLock" size="15px" color="#fff" border="2px" />
                            <span v-else>Xác nhận</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>


    </main>
</template>
<script setup>
import { ref, onMounted, watch } from 'vue'
import api from '@/configs/api'
import { formatDate } from '@/utils/formatDate'
import { formatPrice } from '@/utils/formatPrice'
import pagination from '@/components/pagination.vue'
import message from '@/utils/messageState'
import loading__dot from '@/components/loading/loading__dot.vue'
import loading__loader_circle from '@/components/loading/loading__loader-circle.vue'
import no_data from '@/components/no_data.vue'
import useGoTo from '@/utils/useGoTo';

const { goTo } = useGoTo();

const meta = ref({})
const currentPage = ref(1) // Trang hiện tại
const searchValue = ref('') // Giá trị tìm kiếm

// Danh sách product và tổng số
const products = ref([])
const totalProduct = ref(0)
const pendingProduct = ref(0)    //0
const activeProduct = ref(0)     //1 
const pausedProduct = ref(0)     //2
const rejectedProduct = ref(0)   //3
// const violationProduct = ref(0)  //4

// Trạng thái loading cho các thao tác
const loading = ref({
    fetch: false,
    change: false
})

const getProducts = async () => {
    try {
        loading.value.fetch = true
        const res = await api.get('/product-all', {
            params: {
                page: currentPage.value,
                search: searchValue.value,
                status: filterStatus.value, // Thêm tham số status
            }
        })
        products.value = res.data.products

        // Sử dụng all_total cho nút "Tất cả" để giữ số ổn định
        totalProduct.value = res.data.all_total

        // Luôn cập nhật số đếm từng trạng thái
        pendingProduct.value = res.data.pending
        activeProduct.value = res.data.active
        pausedProduct.value = res.data.paused
        rejectedProduct.value = res.data.rejected

        meta.value = {
            current_page: res.data.current_page,
            last_page: res.data.last_page,
            total: res.data.total, // Sử dụng total đã filter cho pagination
        }
    } catch (error) {
        console.error(error)
    } finally {
        loading.value.fetch = false
    }
}

const filterStatus = ref(null) // null = tất cả, 0-4 là từng trạng thái

// Đổi trang
const onChangePage = (page) => {
    currentPage.value = page
}

// Tìm kiếm
const onSearch = () => {
    currentPage.value = 1
    getProducts()
}

const changeStatus = async (id, status) => {
    try {
        const res = await api.post(`product/change-status/${id}`, { status })
        console.log(res.data)
        getProducts()
        message.emit('show-message', {
            title: 'Thành công',
            text: 'Đổi trạng thái sản phẩm thành công',
            type: 'success'
        })
    } catch (error) {
        console.log(error)
    }
}

const selectedProducts = ref([])
const checkAll = ref(false)

// Chọn hoặc bỏ chọn tất cả vai trò
const toggleAllSelection = () => {
    checkAll.value
        ? selectedProducts.value = products.value.map(product => product.id)
        : selectedProducts.value = []
}

// Theo dõi selectedProducts để cập nhật trạng thái checkAll
watch(selectedProducts, (newSelected) => {
    if (newSelected.length === products.value.length && products.value.length > 0) {
        checkAll.value = true
    } else {
        checkAll.value = false
    }
})

watch(filterStatus, () => {
    selectedProducts.value = []
    checkAll.value = false
    currentPage.value = 1 // Reset về trang đầu khi filter
    getProducts() // Gọi lại API
})

const statusTarget = ref(0)
const isChangeModal = ref(false)
const showModal = (status) => {
    isChangeModal.value = true
    statusTarget.value = status
}

const changeSelected = async () => {
    if (selectedProducts.value.length === 0) {
        message.emit('show-message', {
            title: 'Chú ý',
            text: 'Chưa chọn sản phẩm',
            type: 'warning'
        })
        isChangeModal.value = false
        checkAll.value = false
        return
    }
    try {
        loading.value.change = true
        await api.post('/product/change-multiple', {
            ids: selectedProducts.value,
            status: statusTarget.value
        })
        message.emit('show-message', {
            title: 'Thành công',
            text: 'Đổi trạng thái các sản phẩm thành công',
            type: 'success'
        })
        selectedProducts.value = []
        checkAll.value = false
        getProducts()
    } catch (error) {
        message.emit('show-message', {
            title: 'Thất bại',
            text: 'Đổi trạng thái thất bại',
            type: 'error'
        })
    } finally {
        loading.value.change = false
        isChangeModal.value = false
    }
}

const isLockModal = ref(false)
const lockReason = ref('')
const selectedLockProductId = ref(null)
const showLockModal = (productId) => {
    isLockModal.value = true
    selectedLockProductId.value = productId
}

const loadingLock = ref(false)
const confirmLock = async () => {
    if (lockReason.value.trim() === '') {
        message.emit('show-message', {
            title: 'Chú ý',
            text: 'Vui lòng nhập lý do khóa',
            type: 'warning'
        })
        return
    }
    loadingLock.value = true
    try {
        await api.post(`product/change-status/${selectedLockProductId.value}`, { status: 2, reason: lockReason.value })
        message.emit('show-message', {
            title: 'Thành công',
            text: 'Khóa sản phẩm thành công',
            type: 'success'
        })
        isLockModal.value = false
        lockReason.value = ''
        getProducts()
    } catch (error) {
        console.error(error)
        message.emit('show-message', {
            title: 'Thất bại',
            text: 'Khóa sản phẩm thất bại',
            type: 'error'
        })
    } finally {
        loadingLock.value = false
    }
}

const isRejectModal = ref(false)
const reason = ref('')
const selectedProductId = ref(null)
const showRejectModal = (productId) => {
    isRejectModal.value = true
    selectedProductId.value = productId
}

const loadingReject = ref(false)
const confirmReject = async () => {
    if (reason.value.trim() === '') {
        message.emit('show-message', {
            title: 'Chú ý',
            text: 'Vui lòng nhập lý do từ chối',
            type: 'warning'
        })
        return
    }
    loadingReject.value = true
    try {
        await api.post(`product/change-status/${selectedProductId.value}`, { status: 3, reason: reason.value })
        message.emit('show-message', {
            title: 'Thành công',
            text: 'Từ chối sản phẩm thành công',
            type: 'success'
        })
        isRejectModal.value = false
        reason.value = ''
        getProducts()
    } catch (error) {
        console.error(error)
        message.emit('show-message', {
            title: 'Thất bại',
            text: 'Từ chối sản phẩm thất bại',
            type: 'error'
        })
    } finally {
        loadingReject.value = false
    }
}

watch(currentPage, () => {
    getProducts()
})


onMounted(async () => {
    await getProducts()
})
</script>
<style scoped>
.nav-button button {
    border-radius: 2px;
}

.btn-active {
    border: var(--main-color) 1px solid !important;
    background-color: var(--main-color) !important;
    color: #ffffff !important;
}
</style>