<script setup>
/* -------------------- Import -------------------- */
// Vue core
import { ref, watch, onMounted, nextTick, inject } from 'vue'
import { useRoute, useRouter } from 'vue-router'

// Components
import loading__dot from '@/components/loading/loading__dot.vue'
import loading__loader_circle from '@/components/loading/loading__loader-circle.vue'
import pagination from '@/components/pagination.vue'
import productItem from '@/components/productItem.vue'

// Utils
import api from '@/configs/api'
import message from '@/utils/messageState'
import { formatDate } from '@/utils/formatDate'
import { extractIdFromSlug } from '@/utils/slugHelpers'

const chat_product = inject('chat_product')
const handle_chat = () => {
  chat_product.open_chat_with_shop(shop.value.owner_id, 'user')
}

/* -------------------- Refs & State -------------------- */
const route = useRoute()
const router = useRouter()
const shopId = extractIdFromSlug(route.params.id)
const violationTypes = ref([])
const shop = ref({})
const features = ref([])
const products = ref([])
const pagePer = ref(10)
const meta = ref({})
const pagination_page = ref({
  current_page: 1,
  last_page: 1,
  total: 0,
})
const fullAddress = ref('')
const loading = ref({
  fetch: false,
  follow: false
})
const isInfoModal = ref(false)

/* -------------------- UI Events -------------------- */
const showInfoModal = () => {
  isInfoModal.value = true
}

const filter_sort_by = (sortValue) => {
  keepScroll(() =>
    router.replace({
      query: {
        ...route.query,
        sort_by: sortValue,
        page: 1,
      },
    })
  )
}

const onChangePage = (page) => {
  keepScroll(() =>
    router.replace({
      query: {
        ...route.query,
        page,
      },
    })
  )
}

const setPage = (page, event) => {
  event?.preventDefault?.()
  event?.stopPropagation?.()

  if (page === pagePer.value) return

  pagePer.value = page
  keepScroll(() =>
    router.replace({
      query: {
        ...route.query,
        page_per: page,
        page: 1,
      },
    })
  )
}

const resetFilters = () => {
  keepScroll(() =>
    router.replace({
      query: {}, // Xóa toàn bộ query
    })
  )
}

const keepScroll = async (callback) => {
  const y = window.scrollY
  await callback()
  requestAnimationFrame(() => {
    requestAnimationFrame(() => {
      window.scrollTo({ top: y, behavior: 'auto' })
    })
  })
}

/* -------------------- API Methods -------------------- */
const getShop = async () => {
  try {
    const res = await api.get(`shops/${shopId}/shop-info`)
    shop.value = res.data
  } catch (e) {
    console.error('Lỗi khi lấy thông tin shop:', e)
  }
}

const getFeatures = async () => {
  try {
    const res = await api.get('/products', {
      params: {
        shop_id: shopId || null,
      },
    })
    // Sắp xếp theo lượt bán giảm dần (bán chạy nhất trước)
    const sorted = res.data.data
      .sort((a, b) => (b.sold || 0) - (a.sold || 0))
      .slice(0, 6); // Lấy 6 sản phẩm đầu tiên sau khi sắp xếp

    features.value = sorted;
  } catch (error) {
    console.error('Lỗi khi lấy sản phẩm nổi bật:', error)
  }
}

const fetchProducts = async () => {
  try {
    const res = await api.get('/products', {
      params: {
        shop_id: shopId || null,
        page_per: pagePer.value ?? 10,
        page: route.query.page || 1,
        sort_by: route.query.sort_by || '',
      },
    })

    products.value = res.data.data
    meta.value = {
      current_page: res.data.current_page,
      last_page: res.data.last_page,
      per_page: res.data.per_page,
    }

    pagination_page.value.current_page = res.data.current_page
    pagination_page.value.last_page = res.data.last_page
    pagination_page.value.total = res.data.total
  } catch (err) {
    console.error('Lỗi lấy sản phẩm:', err)
  }
}

const fetchAddress = async () => {
  if (
    !shop.value ||
    !shop.value.province_id ||
    !shop.value.district_id ||
    !shop.value.ward_code
  ) {
    console.warn('Thiếu thông tin địa chỉ shop:', shop.value)
    fullAddress.value = shop.value?.detail_address || 'Chưa cập nhật địa chỉ'
    return
  }

  try {
    const res = await api.get('/shop/address', {
      params: {
        province_id: shop.value.province_id,
        district_id: shop.value.district_id,
        ward_code: shop.value.ward_code,
      },
    })

    const { province, district, ward } = res.data
    fullAddress.value = `${shop.value.detail_address}, ${ward}, ${district}, ${province}`
  } catch (err) {
    console.error('Lỗi khi lấy địa chỉ:', err)
    // Fallback về địa chỉ chi tiết nếu không lấy được địa chỉ đầy đủ
    fullAddress.value = shop.value?.detail_address || 'Lỗi khi tải địa chỉ'
  }
}

const isFollow = ref(false)
const checkFollow = async () => {
  // Kiểm tra xem user đã đăng nhập chưa
  const token = localStorage.getItem('token')
  if (!token) {
    isFollow.value = false
    return
  }

  // Kiểm tra xem shop đã được load chưa
  if (!shop.value || !shop.value.id) {
    console.warn('Shop chưa được load, không thể kiểm tra follow status')
    isFollow.value = false
    return
  }

  try {
    const res = await api.post('/shop/check-follow', {
      shop_id: shop.value.id
    })
    isFollow.value = res.data.is_following
    // console.log(isFollow.value)
  } catch (error) {
    console.error('Lỗi khi kiểm tra trạng thái follow:', error)
    isFollow.value = false
  }
}

const followShop = async () => {
  // Kiểm tra xem user đã đăng nhập chưa
  const token = localStorage.getItem('token')
  if (!token) {
    message.emit('show-message', {
      title: 'Thông báo',
      text: 'Vui lòng đăng nhập để theo dõi cửa hàng',
      type: 'warning',
    });
    return
  }

  // Kiểm tra xem shop đã được load chưa
  if (!shop.value || !shop.value.id) {
    message.emit('show-message', {
      title: 'Lỗi',
      text: 'Thông tin cửa hàng chưa được tải, vui lòng thử lại',
      type: 'error',
    });
    return
  }

  try {
    loading.value.follow = true
    const res = await api.post('/shop/follow', {
      shop_id: shop.value.id
    })

    // Cập nhật trạng thái follow ngay lập tức
    isFollow.value = !isFollow.value

    // Cập nhật số lượng followers ngay lập tức
    if (isFollow.value) {
      shop.value.followers_count = (shop.value.followers_count || 0) + 1
    } else {
      shop.value.followers_count = Math.max((shop.value.followers_count || 0) - 1, 0)
    }

    message.emit('show-message', {
      title: 'Thông báo',
      text: res.data.message,
      type: 'success',
    });
  } catch (error) {
    console.error('Lỗi khi follow/unfollow shop:', error)
    // Rollback trạng thái nếu có lỗi
    message.emit('show-message', {
      title: 'Lỗi',
      text: 'Có lỗi xảy ra, vui lòng thử lại',
      type: 'error',
    });
  } finally {
    loading.value.follow = false
  }
}

const copyShopLink = () => {
  const url = window.location.href
  navigator.clipboard.writeText(url)
    .then(() => {
      message.emit('show-message', {
        title: 'Đã sao chép',
        text: 'Liên kết cửa hàng đã được sao chép',
        type: 'success',
      })
    })
    .catch(err => {
      message.emit('show-message', {
        title: 'Lỗi',
        text: 'Không thể sao chép liên kết',
        type: 'error',
      })
    })
}

const getViolationTypes = async () => {
  try {
    const res = await api.get('/violation-types')
    violationTypes.value = res.data.types
    // console.log(violationTypes.value)
  } catch (error) {
    console.log(error)
  }
}

const isReportModal = ref(false)
const showReportModal = () => {
  report.value = {
    type_id: '',
    description: ''
  }
  isInfoModal.value = false
  isReportModal.value = true
}

const report = ref({
  type_id: '',
  description: ''
})

const sendReport = async () => {
  const type_id = report.value.type_id
  const description = report.value.description
  if (!type_id) {
    message.emit('show-message', {
      title: 'Chú ý',
      text: 'Vui lòng chọn loại vi phạm',
      type: 'warning',
    })
    return
  }
  if (description.length > 1000) {
    message.emit('show-message', {
      title: 'Chú ý',
      text: 'Mô tả không được vượt quá 1000 ký tự',
      type: 'warning',
    })
    return
  }
  try {
    const res = await api.post('/send-report', {
      shop_id: shop.value.id,
      type_id: type_id,
      description: description
    })
    if (res.status === 201) {
      message.emit('show-message', {
        title: 'Thành công',
        text: res.data.message,
        type: 'success',
      })
    }
    isReportModal.value = false
  } catch (error) {
    console.log(error)
  }
}


/* -------------------- Watchers -------------------- */
watch(
  () => route.query,
  async () => {
    await fetchProducts()
  },
  { deep: true }
)

// Theo dõi khi shop được load để chạy các tác vụ phụ thuộc
watch(
  () => shop.value?.id,
  async (newShopId) => {
    if (newShopId) {
      // Chạy song song khi đã có shop ID
      await Promise.all([
        checkFollow(),
        fetchAddress()
      ])
    }
  }
)

/* -------------------- Lifecycle -------------------- */
onMounted(async () => {
  loading.value.fetch = true

  if (route.query.sort_by) {
    const newQuery = { ...route.query }
    delete newQuery.sort_by
    router.replace({ query: newQuery })
  }

  try {
    // Chạy song song các tác vụ độc lập
    const shopPromise = getShop() // Watcher sẽ tự động chạy checkFollow và fetchAddress
    const featuresPromise = getFeatures()
    const productsPromise = fetchProducts()
    const violationPromise = getViolationTypes()
    
    await Promise.all([shopPromise, featuresPromise, productsPromise, violationPromise])
  } catch (error) {
    console.error('Lỗi khi load dữ liệu shop:', error)
  } finally {
    loading.value.fetch = false
  }
})

</script>

<template>
  <div v-if="loading.fetch" style="height: 60vh;" class="d-flex align-items-center justify-content-center">
    <loading__dot />
  </div>
  <div v-else class="container mt-4">
    <div class="shop-profile">
      <!-- Banner -->
      <img v-if="shop.background" class="shop-banner mx-auto shadow-sm" alt="Shop Banner"
        :src="`/imgs/shops/${shop.background}`" />

      <!-- Profile Section -->
      <div class="profile-section d-flex align-items-center mt-4 mx-auto" style="width: 100%;">
        <div class=" profile-left d-flex gap-3 align-items-center">
          <!-- Profile Avatar -->
          <img class="profile-avatar shadow-sm w-10"
            :src="shop.image ? `/imgs/shops/${shop.image}` : '/imgs/user-default.jpg'" alt="Avatar" />
          <!-- <img class="profile-avatar shadow-sm" src="/imgs/avtDefault.png" alt="Avatar"/> -->

          <!-- Profile Info -->
          <div class="profile-info mb-auto">
            <div class="profile-name fs-30 fw-bold">{{ shop.shop_name }}</div>
            <div class="profile-stats">
              <span class="stat-number text-danger">{{ shop.products_count }}</span>
              sản phẩm |
              <span class="stat-number text-danger">{{ shop.followers_count }}</span>
              theo dõi |
              <span class="stat-number text-danger">{{ shop.total_rates }}</span>
              đánh giá
            </div>
            <div class="profile-description mt-1">Thông tin khác <span @click="showInfoModal" class="fw-bold"
                style="cursor: pointer;">...xem thêm</span></div>
            <!-- Action Buttons -->
            <div
              class="row row-2 action-buttons  mt-2 gap-3 d-flex mx-auto justify-content-lg-end justify-content-start">
              <button v-if="loading.follow" class="col btn btn-outline-danger fw-semibold radius-2"
                style="min-width: 135px; max-width: 135px; height: 37px;">
                <loading__loader_circle size="15px" color="red" border="2px" class="mx-auto my-1" />
              </button>
              <button @click="followShop" v-else-if="!isFollow" class="col btn btn-outline-danger fw-semibold radius-2"
                style="min-width: 135px; max-width: 135px; height: 37px;">
                <span><i class="bi bi-plus-circle-fill"></i>
                  Theo dõi</span>
              </button>
              <button @click="followShop" v-else class="col main-btn fw-semibold radius-2 fs-14"
                style="min-width: 135px; max-width: 135px; height: 37px;">
                <span><i class="bi bi-check-circle-fill"></i>
                  Đã theo dõi</span>
              </button>
              <button class="col btn btn-secondary fw-semibold radius-2 fs-14" @click="handle_chat"
                style="min-width: 135px; max-width: 135px; height: 37px;">
                <i class="fas fa-comment btn-icon"></i>
                Nhắn tin
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="suggest-product bg-white mt-5">
      <div class="fs-20 fw-semibold text-center">Sản Phẩm Bán Chạy</div>
    </div>
    <div class="suggest-product__box mt-10">
      <div class="row g-2">
        <div v-for="product in features" class="col-6 col-md-3 col-xl-2">
          <productItem :product="product" />
        </div>
      </div>
    </div>


    <div class="suggest-product bg-white mt-5">
      <div class="fs-20 fw-semibold text-center">Tất Cả Sản Phẩm</div>
    </div>
    <div class="product__sort--bar ">
      <div class="d-flex align-items-center gap-1">
        <span class="fw-medium fs-14 text-grey d-none d-lg-block">Sắp xếp</span>
        <button @click="filter_sort_by('latest')" :class="{
          'sort__bar--active': $route.query.sort_by == 'latest'
        }">Mới Nhất</button>
        <button @click="filter_sort_by('bestseller')" :class="{
          'sort__bar--active': $route.query.sort_by == 'bestseller'
        }">Bán Chạy</button>
        <div class="dropdown">
          <button class="dropdown-toggle" :class="{
            'text-color': $route.query.sort_by == 'price_asc' || $route.query.sort_by == 'price_desc'
          }" type="button" id="product__sort--bar-price" data-bs-toggle="dropdown" aria-expanded="false">
            Giá
          </button>
          <ul class="dropdown-menu radius-2 border-none" aria-labelledby="product__sort--bar-price">
            <li @click="filter_sort_by('price_asc')">
              <a :class="{
                'active': $route.query.sort_by == 'price_asc'
              }" class="dropdown-item fs-14 fw-medium cursor-pointer">Giá: Thấp đến Cao</a>
            </li>
            <li @click="filter_sort_by('price_desc')">
              <a :class="{
                'active': $route.query.sort_by == 'price_desc'
              }" class="dropdown-item fs-14 fw-medium cursor-pointer">Giá: Cao đến Thấp</a>
            </li>
          </ul>
        </div>
        <div class="dropdown">
          <button type="button" class="dropdown-toggle radius-2 fw-medium" data-bs-toggle="dropdown">
            Hiển Thị
          </button>
          <ul class="dropdown-menu radius-2">
            <li v-for="option in [2, 10, 25, 50, 100, 500]" :key="option">
              <a href="#" class="dropdown-item fw-medium cursor-pointer fs-14" :class="{ 'active': option == pagePer }"
                @click="(e) => setPage(option, e)">
                {{ option }}
              </a>
            </li>

          </ul>
        </div>
        <button @click="resetFilters" class="white-btn fw-medium d-flex align-items-center justify-content-center">
          <i class="bi bi-arrow-clockwise me-1"></i>
          <span class="d-none d-sm-block">Đặt lại</span>
        </button>

      </div>
      <div class="d-none d-md-block">
        <div class="d-flex align-items-center gap-2">
          <p class="fs-14 fw-medium">
            <span class="text-color">
              {{ pagination_page.current_page }}
            </span>
            /
            {{ pagination_page.last_page }}
          </p>
          <div class="d-flex gap-1">
            <button class="py-0 px-2" :class="{
              'disabled': pagination_page.current_page <= 1
            }" @click="onChangePage(pagination_page.current_page - 1)" :disabled="pagination_page.current_page <= 1">
              <i class="fs-22 ri-arrow-drop-left-line"></i>
            </button>
            <button class="py-0 px-2" :class="{
              'disabled': pagination_page.current_page >= pagination_page.last_page
            }" @click="onChangePage(pagination_page.current_page + 1)"
              :disabled="pagination_page.current_page >= pagination_page.last_page">
              <i class="fs-22 ri-arrow-drop-right-line"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
    <div v-if="products && products.length == 0" class="product__search--empty">
      <img src="/imgs/search-empty.png" alt="Search empty">
      <p>Không tìm thấy sản phẩm nào phù hợp</p>
    </div>
    <div v-else class="row g-2 fade-in">
      <div v-for="pro in products" class="col-6 col-sm-4 col-lg-3 col-xl-2">
        <productItem :product="pro" />
      </div>
    </div>
    <div class="mt-4">
      <pagination :meta="meta" @changePage="onChangePage" />
    </div>

    <!-- Info Modal -->
    <div v-if="isInfoModal"
      class="modal-background container-fluid p-0 d-flex justify-content-center align-items-center">
      <div class="modal-edit-address modal-ezeshop bg-white shadow-sm px-4 pt-4 pb-2">
        <div class="modal-ezeshop__top d-flex justify-content-between align-items-center">
          <div class="modal-ezeshop__name fs-24 fw-bold">{{ shop.shop_name }}</div>
          <div @click="isInfoModal = false" class="modal-ezeshop__cancel cursor-pointer"><i
              class="fa-solid fa-xmark fs-30"></i></div>
        </div>
        <!-- Content -->
        <div class="modal-ezeshop__main w-100 p-1 mb-4">
          <div class="fs-24 fw-bold">
            Thông tin khác
          </div>
          <div class="fs-16 mt-3 fw-medium">
            <i class="bi bi-calendar2-event-fill me-2"></i>
            Ngày tham gia: {{ formatDate(shop.created_at) }}
          </div>
          <div class="fs-16 mt-3 fw-medium">
            <i class="bi bi-telephone-fill me-2"></i>
            Số điện thoại: {{ shop.phone }}
          </div>
          <div class="fs-16 mt-3 fw-medium">
            <i class="bi bi-geo-alt-fill me-2"></i>
            Địa chỉ: {{ fullAddress }}
          </div>
          <div class="fs-16 mt-3 fw-medium">
            <i class="bi bi-info-circle-fill me-2"></i>
            Mô tả: {{ shop.shop_name }}
          </div>
        </div>
        <div class="modal-ezeshop__bottom d-flex justify-content-end w-100 align-items-center border-top">
          <div class="modal-ezeshop__cancel me-3"><button class="btn btn-outline-secondary py-2 px-4 fs-14 radius-2"
              @click="copyShopLink">Sao
              chép liên kết</button></div>
          <div class="modal-ezeshop__save"><button @click="showReportModal" form="form-edit-cate"
              class="main-btn py-2 px-4 fs-14 radius-2" type="submit">
              <span>Báo cáo cửa hàng</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Report Modal -->
    <div v-if="isReportModal"
      class="modal-background container-fluid p-0 d-flex justify-content-center align-items-center">
      <div class="modal-edit-address modal-ezeshop bg-white shadow-sm px-4 pt-4 pb-2">
        <div class="modal-ezeshop__top d-flex justify-content-between align-items-center">
          <div class="modal-ezeshop__name fs-24 fw-bold">Báo cáo cửa hàng vi phạm</div>
          <div @click="isReportModal = false" class="modal-ezeshop__cancel cursor-pointer"><i
              class="fa-solid fa-xmark fs-30"></i></div>
        </div>
        <!-- Content -->
        <div class="modal-ezeshop__main w-100 p-1 mb-4">
          <div class="mb-3">
            <label class="form-label fs-18 fw-medium">Loại vi phạm</label>
            <select class="form-select form-control" v-model="report.type_id">
              <option value="" selected>-- Chọn loại vi phạm --</option>
              <option v-for="type in violationTypes" :value="type.id">{{ type.name }}</option>
            </select>
          </div>
          <div class="">
            <label class="form-label fs-18 fw-medium">Mô tả chi tiết</label>
            <textarea v-model="report.description" maxlength="1000" style="min-height: 150px;" class="form-control"
              placeholder="Người vô tình vẽ hoa vẽ lá, ta đa tình tưởng đó là tối đa 1000 ký tự..."></textarea>
            <div class="text-end text-muted fs-14 mt-1">
              {{ report.description.length }}/1000 ký tự
            </div>
          </div>
        </div>
        <div class="modal-ezeshop__bottom d-flex justify-content-end w-100 align-items-center border-top">
          <div class="modal-ezeshop__cancel me-3"><button class="btn btn-outline-secondary py-2 px-4 fs-14 radius-2"
              @click="isReportModal = false">Hủy</button></div>
          <div class="modal-ezeshop__save"><button @click="sendReport" form="form-edit-cate"
              class="main-btn py-2 px-4 fs-14 radius-2" type="submit">
              <span>Gửi báo cáo</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>