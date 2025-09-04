<template>
    <main class="admin__content">
        <div
            class="p-3 bg-white border-bottom text-grey fs-14 fw-medium d-flex align-items-center justify-content-between">
            <p class="m-0">Cửa Hàng Vi Phạm (<span class="text-color">{{ totalShops }}</span>)</p>
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
                            <input type="text" v-model="searchValue" placeholder="Tìm cửa hàng..."
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
            <div v-if="loading.fetch" style="height: 60vh;" class="d-flex align-items-center justify-content-center">
                <loading__dot />
            </div>
            <div v-else class="table-responsive fade-in">
                <div v-if="shops.length === 0">
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
                            <th scope="col" class="fs-14 fw-semibold min-w-250">Thông Tin Đăng Ký</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-250">Vi Phạm</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-100">Trạng Thái</th>
                            <th scope="col" class="fs-14 fw-semibold text-center min-w-100">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="shop in shops">
                            <td scope="row" class="align-middle">
                                <div class="checkbox me-2">
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="check-item" :value="shop.id"
                                            v-model="selectedShops" />
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
                            <td class="align-middle">{{ shop.shop_name }}</td>
                            <td class="align-middle">
                                <p>{{ shop.pickup_name }}</p>
                                <p>{{ shop.phone }}</p>
                                <p>{{ shop.address }}</p>
                            </td>
                            <td class="align-middle">
                                <div v-for="violation in shop.violations" :key="violation.id">
                                    -- {{ violation.type.name }}
                                </div>
                            </td>
                            <td class="align-middle">
                                <span class="badge radius-2" :class="shop.status === 1 ? 'bg-success' : 'bg-warning text-black'">
                                    {{ shop.status === 1 ? 'Hoạt động' : 'Đã khóa' }}
                                </span>
                            </td>
                            <td class="align-middle text-center">
                                <div class="more-wrapper">
                                    <button class="more-btn">
                                        <i class="ri-more-fill"></i>
                                    </button>
                                    <div class="more-popup">
                                        <button @click="openNewTab(shop)" class="bg-primary text-white">Xem</button>
                                        <button v-if="shop.status === 0" @click="changeStatus(shop.id, 1)"
                                            class="bg-success text-white">Mở</button>
                                        <button v-else @click="changeStatus(shop.id, 0)"
                                            class="bg-warning">Khóa</button>
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
                                        <button @click="showModal(1)" class="text-white bg-success px-2 py-0">
                                            Mở</button>
                                        <button @click="showModal(0)" class="bg-warning px-2 py-0">Khóa</button>
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
                        <span v-if="statusTarget == 1">Mở</span><span v-if="statusTarget == 0">Khóa</span> cửa hàng
                    </div>
                    <div @click="isChangeModal = false" class="modal-ezeshop__cancel cursor-pointer"><i
                            class="fa-solid fa-xmark fs-18"></i></div>
                </div>
                <!-- Content -->
                <div class="modal-ezeshop__main w-100">
                    Bạn có chắc muốn <span v-if="statusTarget == 1">Mở</span><span v-if="statusTarget == 0">Khóa</span>
                    tất cả cửa hàng đã chọn không?
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

    </main>
</template>
<script setup>
// =======================
// Import các thư viện và component cần thiết
// =======================
import { ref, onMounted, watch, computed } from 'vue'
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

// Danh sách cửa hàng và tổng số
const shops = ref([]) // Danh sách cửa hàng
const totalShops = ref(0) // Tổng số cửa hàng


// Trạng thái loading cho các thao tác
const loading = ref({
    fetch: false,      // Đang tải dữ liệu
    save_add: false,   // Đang thêm cửa hàng
    save_edit: false,  // Đang sửa cửa hàng
    delete: false,      // Đang xóa cửa hàng
    change: false,     // Đang đổi trạng thái cửa hàng
})

const selectedShops = ref([]) // Danh sách id cửa hàng được chọn
const checkAll = ref(false)   // Checkbox chọn tất cả

// =======================
// Các hàm xử lý chính
// =======================

const fetchAddress = async (shop) => {
  if (
    !shop ||
    !shop.province_id ||
    !shop.district_id ||
    !shop.ward_code
  ) {
    console.warn('Thiếu thông tin địa chỉ shop:', shop)
    return
  }

  try {
    const res = await api.get('/shop/address', {
      params: {
        province_id: shop.province_id,
        district_id: shop.district_id,
        ward_code: shop.ward_code,
      },
    })
    const { province, district, ward } = res.data
    return `${shop.detail_address}, ${ward}, ${district}, ${province}`
  } catch (err) {
    console.error('Lỗi khi lấy địa chỉ:', err)
  }
}

// Lấy danh sách cửa hàng từ API
const getShops = async () => {
    try {
        loading.value.fetch = true
        const res = await api.get('/violation-shops', {
            params: {
                page: currentPage.value,
                search: searchValue.value,
            }
        })
        shops.value = res.data.shops
        // Thêm địa chỉ chi tiết vào mỗi cửa hàng bằng cách gọi hàm fetchAddress
        for (const shop of shops.value) {
            shop.address = await fetchAddress(shop)
        }
        totalShops.value = res.data.total

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
    getShops()
}

const changeStatus = async (id, status) => {
    try {
        const res = await api.post(`shop/change-status/${id}`, { status })
        // console.log(res.data)
        getShops()
        message.emit('show-message', {
            title: 'Thành công',
            text: 'Đổi trạng thái cửa hàng thành công',
            type: 'success'
        })
    } catch (error) {
        console.log(error)
    }
}

const statusTarget = ref(0)
const isChangeModal = ref(false)
const showModal = (status) => {
    isChangeModal.value = true
    statusTarget.value = status
}

const changeSelected = async () => {
    if (selectedShops.value.length === 0) {
        message.emit('show-message', {
            title: 'Chú ý',
            text: 'Chưa chọn cửa hàng',
            type: 'warning'
        })
        isChangeModal.value = false
        checkAll.value = false
        return
    }
    try {
        loading.value.change = true
        await api.post('/shop/change-multiple', {
            ids: selectedShops.value,
            status: statusTarget.value
        })
        message.emit('show-message', {
            title: 'Thành công',
            text: 'Đổi trạng thái các cửa hàng thành công',
            type: 'success'
        })
        selectedShops.value = []
        checkAll.value = false
        getShops()
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


// =======================
// Chọn tất cả / bỏ chọn tất cả
// =======================
const toggleAllSelection = () => {
    checkAll.value
        ? selectedShops.value = shops.value.map(shop => shop.id)
        : selectedShops.value = []
}

// Theo dõi selectedShops để cập nhật checkAll
watch(selectedShops, (newSelected) => {
    if (newSelected.length === shops.value.length && shops.value.length > 0) {
        checkAll.value = true
    } else {
        checkAll.value = false
    }
})

const openNewTab = (shop) => {
    const url = `/cua-hang-${slugify(shop.shop_name)}-${shop.id}`
    window.open(url, '_blank')
}


// Theo dõi thay đổi trang để lấy lại dữ liệu
watch(currentPage, () => {
    getShops()
})

// Khi component mounted, lấy danh sách Cửa Hàng
onMounted(async () => {
    await getShops()
})
</script>
