<template>
    <div class="container">
        <div v-if="reactive_search && products.length == 0 && !loading.filter_panel && !loading.product_list"
            class="product__search--empty">
            <img src="/imgs/search-empty.png" alt="Search empty">
            <p>Không tìm thấy kết quả nào</p>
            <p class="text-grey">Hãy thử sử dụng các từ khóa chung chung hơn</p>
        </div>
        <div v-else class="container__product">
            <div v-if="loading.filter_panel" class="product__filter--panel">
                <div class="d-flex flex-column gap-3">
                    <div class="skeleton skeleton-title w-75"></div>
                    <div class="skeleton skeleton-line w-100" v-for="n in 6" :key="n"></div>
                </div>
            </div>
            <div v-if="!loading.filter_panel" class="product__filter--panel">
                <div class="d-flex flex-column gap-3">
                    <div v-if="!reactive_search" class="product__category">
                        <h6 class="fs-16 fw-semibold cursor-pointer">
                            <i class="ri-list-check"></i>
                            Tất Cả Danh Mục
                        </h6>
                        <router-link v-for="(cate, index) in categories_with_products" :to="{
                            query: {
                                ...route.query,
                                catc: index === 0 ? undefined : getSlugFromId(cate.id, categories_with_products),
                                page: 1
                            }
                        }" class="product__category--link" :class="{
                            'fw-semibold': index == 0,
                            'category__link--active': (!route.query.catc && index === 0) || (getIdFromSlug(route.query.catc, categories_with_products) == cate.id)
                        }">
                            {{ cate.name }}
                        </router-link>
                    </div>
                    <div class="product__filter">
                        <h6 class="fs-16 fw-semibold">
                            <i class="ri-filter-2-line"></i>
                            Bộ Lọc Tìm Kiếm
                        </h6>
                        <!-- Address -->
                        <div class="my-3" v-if="province_by_products && province_by_products.length > 0">
                            <h6 class="fw-medium fs-6">Nơi bán</h6>
                            <div v-for="province in province_by_products" class="mb-16 d-flex align-items-center gap-2">
                                <div class="checkbox">
                                    <label class="checkbox-label">
                                        <input @change="setLocation(province.ProvinceID)"
                                            :checked="selectedLocation.includes(province.ProvinceID)" type="checkbox"
                                            :id="province.ProvinceID" />
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
                                <label class="cursor-pointer fw-medium fs-14" :for="province.ProvinceID">
                                    {{ province.ProvinceName }}
                                </label>
                            </div>
                        </div>
                        <!-- Range Price -->
                        <div class="my-4 border-top pt-4">
                            <h6 class="fw-medium fs-6">Khoảng giá</h6>
                            <div class="relative w-full max-w-md mx-auto mt-3">
                                <Slider class="slider" v-model="priceRange" :min="0" :max="50000000" :step="100000"
                                    :format-tooltip="formatPrice" />
                                <div class="flex justify-between px-1 my-3">
                                    <div class="fs-14 fw-medium">
                                        ↑ đ {{ priceRange[0].toLocaleString() }}
                                    </div>
                                    <div class="fs-14 fw-medium">
                                        ↓ đ {{ priceRange[1].toLocaleString() }}
                                    </div>
                                </div>
                            </div>

                            <button class="main-btn radius-2 py-1 px-2 w-100" @click="applyPriceRange">
                                Áp Dụng
                            </button>
                        </div>
                        <!-- Rating -->
                        <div class="my-4 border-top pt-4">
                            <h6 class="fw-medium fs-6">Đánh Giá</h6>
                            <div class="d-flex flex-column gap-2">
                                <div v-for="star in [5, 4, 3, 2, 1]" :key="star" @click="setRating(star)"
                                    class="d-flex align-items-center gap-1 cursor-pointer ps-2 "
                                    :class="{ 'product__star--active': $route.query.min_rate == star }">
                                    <i v-for="n in 5" :key="n" class="text-warning"
                                        :class="n <= star ? 'ri-star-fill' : 'ri-star-line'"></i>
                                    <span v-if="star < 5" class="fs-14 fw-medium text-grey">Trở lên</span>
                                </div>
                            </div>
                        </div>

                        <button @click="clearFilter" type="button" class="main-btn w-100 py-1 radius-2">Xóa Tất
                            Cả</button>
                    </div>
                </div>
            </div>
            <div v-if="loading.product_list" class="product__list">
                <div class="product__sort--bar d-flex align-items-center gap-3 mb-3">
                    <div class="skeleton skeleton-button w-25"></div>
                    <div class="skeleton skeleton-button w-25"></div>
                    <div class="skeleton skeleton-button w-25"></div>
                </div>
                <div class="row g-2 fade-in">
                    <div v-for="n in 18" :key="n" class="col-6 col-sm-4 col-lg-3 col-xl-2">
                        <div class="skeleton-card">
                            <div class="skeleton skeleton-img mb-2"></div>
                            <div class="skeleton skeleton-text w-100 mb-1"></div>
                            <div class="skeleton skeleton-text w-75 mb-1"></div>
                            <div class="skeleton skeleton-text w-50"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="!loading.product_list" class="product__list">
                <div v-if="$route.query.keywords" class="d-flex align-items-center gap-2 fs-16 my-2">
                    <i class="ri-lightbulb-line"></i>
                    <p class="fw-medium">
                        Kết quả tìm kiếm cho từ khoá
                        <span class="fw-semibold text-color"> '{{ $route.query.keywords }}'</span>
                    </p>
                </div>
                <div class="product__sort--bar">
                    <div class="d-flex align-items-center gap-2">
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
                            }" type="button" id="product__sort--bar-price" data-bs-toggle="dropdown"
                                aria-expanded="false">
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
                                <li v-for="option in [2, 10, 25, 50, 100, 500]" @click="setPage(option)" :key="option">
                                    <a class="dropdown-item fw-medium cursor-pointer fs-14" :class="{
                                        'active': option == pagePer
                                    }">
                                        {{ option }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="d-none d-sm-block">
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
                                }" @click="onChangePage(pagination_page.current_page - 1)"
                                    :disabled="pagination_page.current_page <= 1">
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
                <div class="d-block d-md-none">
                    <div class="product__sort--bar-mobile">
                        <!-- Category  -->
                        <div v-if="!reactive_search" class="dropdown">
                            <button class="dropdown-toggle" type="button" id="filter__category"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                Danh Mục
                            </button>
                            <div class="dropdown-menu radius-2 w-100 border-none p-2 filter__category"
                                aria-labelledby="filter__category">
                                <div class="row g-2">
                                    <router-link @click.stop :to="{
                                        query: {
                                            ...route.query,
                                            catc: index === 0 ? undefined : cate.id,
                                            page: 1
                                        }
                                    }" v-for="(cate, index) in categories_with_products" class="col-6">
                                        <div class="filter__category--mobile" :class="{
                                            'fw-semibold': index == 0,
                                            'active': (!route.query.catc && index === 0) || (route.query.catc == cate.id)
                                        }">
                                            {{ cate.name }}
                                            <div v-if="(!route.query.catc && index === 0) || (route.query.catc == cate.id)"
                                                class="product__tick">
                                                <svg viewBox="0 0 12 12" class="shopee-svg-icon icon-tick-bold">
                                                    <g>
                                                        <path
                                                            d="M5.2 10.9c-.2 0-.5-.1-.7-.2l-4.2-3.7c-.4-.4-.5-1-.1-1.4s1-.5 1.4-.1l3.4 3 5.1-7c.3-.4 1-.5 1.4-.2s.5 1 .2 1.4l-5.7 7.9c-.2.2-.4.4-.7.4z">
                                                        </path>
                                                    </g>
                                                </svg>
                                            </div>
                                        </div>
                                    </router-link>
                                </div>
                            </div>
                        </div>
                        <!-- Address -->
                        <div class="dropdown">
                            <button class="dropdown-toggle" type="button" id="filter__address" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                Nơi Bán
                            </button>
                            <div class="dropdown-menu radius-2 w-100 border-none p-2 filter__address"
                                aria-labelledby="filter__address">
                                <div class="row g-2" v-if="province_by_products && province_by_products.length > 0">
                                    <div v-for="province in province_by_products" class="col-6">
                                        <div class="filter__address--mobile" :class="{
                                            'active': selectedLocation.includes(province.ProvinceID)
                                        }" @click.stop="setLocation(province.ProvinceID)">
                                            {{ province.ProvinceName }}
                                            <div class="product__tick"
                                                v-if="selectedLocation.includes(province.ProvinceID)">
                                                <svg viewBox="0 0 12 12" class="shopee-svg-icon icon-tick-bold">
                                                    <g>
                                                        <path
                                                            d="M5.2 10.9c-.2 0-.5-.1-.7-.2l-4.2-3.7c-.4-.4-.5-1-.1-1.4s1-.5 1.4-.1l3.4 3 5.1-7c.3-.4 1-.5 1.4-.2s.5 1 .2 1.4l-5.7 7.9c-.2.2-.4.4-.7.4z">
                                                        </path>
                                                    </g>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Range Price -->
                        <div class="dropdown">
                            <button class="dropdown-toggle " type="button" id="filter__range--price"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                Khoảng Giá
                            </button>
                            <div class="dropdown-menu radius-2 w-100 border-none filter__range--price"
                                aria-labelledby="filter__range--price" @click.stop>
                                <div class="my-2 p-2 px-5">
                                    <div class="relative w-full max-w-md mx-auto mt-4">
                                        <Slider class="slider" v-model="priceRange" :min="0" :max="50000000"
                                            :step="100000" :format-tooltip="formatPrice" />
                                        <div class="flex justify-between px-1 my-3">
                                            <div class="fs-14 fw-medium">
                                                ↑ đ {{ priceRange[0].toLocaleString() }}
                                            </div>
                                            <div class="fs-14 fw-medium">
                                                ↓ đ {{ priceRange[1].toLocaleString() }}
                                            </div>
                                        </div>
                                    </div>

                                    <button class="main-btn bg-main radius-2 py-1 px-2 w-100" @click="applyPriceRange">
                                        Áp Dụng
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- Rating -->
                        <div class="dropdown">
                            <button class="dropdown-toggle" type="button" id="filter__rating" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                Đánh giá
                            </button>
                            <div class="dropdown-menu radius-2 w-100 border-none p-2" aria-labelledby="filter__rating">
                                <div class="row g-2">
                                    <div class="d-flex flex-column gap-2 w-100">
                                        <div v-for="star in [5, 4, 3, 2, 1]" :key="star" @click="setRating(star)"
                                            class="d-flex align-items-center gap-1 cursor-pointer ps-2 fs-14"
                                            :class="{ 'product__star--active': $route.query.min_rate == star }">
                                            <i v-for="n in 5" :key="n" class="text-warning"
                                                :class="n <= star ? 'ri-star-fill' : 'ri-star-line'"></i>
                                            <span v-if="star < 5" class="fs-12 fw-medium text-grey">Trở lên</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <!-- ClearFilter -->
                        <button  @click="clearFilter" class="main-btn bg-main">Xóa Tất Cả</button>
                    </div>
                </div>
                <div v-if="products && products.length == 0" class="product__search--empty">
                    <img src="/imgs/search-empty.png" alt="Search empty">
                    <p>Không tìm thấy sản phẩm nào phù hợp</p>
                    <p class="text-grey">Hãy thử chọn lại bộ lọc</p>
                    <button @click="clearFilter" class="main-btn redius-2 py-1 px-3">Xóa Bộ Lọc</button>
                </div>
                <div v-else class="row g-2 fade-in">
                    <div v-for="pro in products" class="col-6 col-sm-4 col-lg-3 col-xl-2">
                        <productItem :product="pro" />
                    </div>
                </div>
                <div class="mt-4">
                    <pagination :meta="meta" @changePage="onChangePage" />
                </div>
            </div>
        </div>
    </div>
</template>
<style scoped>
.slider {
    --slider-connect-bg: #d52220;
    --slider-tooltip-bg: #d52220;
    --slider-tooltip-color: #fff;
}

::v-deep(.slider-tooltip) {
    display: none !important;
}
</style>
<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/configs/api'
import productItem from '@/components/productItem.vue'
import pagination from '@/components/pagination.vue'
import message from '@/components/message.vue'
import { formatPrice } from '@/utils/formatPrice.js'
import Slider from '@vueform/slider'
import { getIdFromSlug, getSlugFromId } from '@/utils/slugHelpers'

const priceRange = ref([0, 50000000])
const route = useRoute()
const router = useRouter()
const products = ref([])
const reactive_search = ref(false)
const province_by_products = ref([])
const categories_with_products = ref([])
const root_categories = ref([])
const loading = ref({
    filter_panel: false,
    product_list: false
})
// ============ //
// FILTER SORT BY
const pagePer = ref(10)
const meta = ref({})
const pagination_page = ref({
    current_page: 1,
    last_page: 1,
    total: 0,
})
const filter_sort_by = (sortValue) => {
    router.push({
        query: {
            ...route.query,
            sort_by: sortValue,
            page: 1
        }
    })
}

const setPage = (page) => {
    pagePer.value = page
    router.push({
        query: {
            ...route.query,
            page: 1
        }
    })
    fetchProducts()
}

const onChangePage = (page) => {
    router.push({
        query: {
            ...route.query,
            page: page
        }
    })
}

const setRating = (minRate) => {
    router.push({
        query: {
            ...route.query,
            min_rate: minRate,
            page: 1
        }
    })
}

const applyPriceRange = () => {
    const min = priceRange.value[0];
    const max = priceRange.value[1];

    if (!min && !max) {
        message.emit('show-message', {
            title: 'Thông báo',
            text: 'Vui lòng chọn khoảng giá trước khi áp dụng.',
            type: 'warning',
        });
        return;
    }

    if (min && max && min > max) {
        message.emit('show-message', {
            title: 'Thông báo',
            text: 'Giá tối thiểu không được lớn hơn giá tối đa.',
            type: 'error',
        });
        return;
    }

    router.push({
        query: {
            ...route.query,
            page: 1,
            min_price: min || undefined,
            max_price: max || undefined,
        },
    });
}

const selectedLocation = ref([])
const setLocation = (location_id) => {
    const index = selectedLocation.value.indexOf(location_id);

    if (index !== -1) {
        selectedLocation.value.splice(index, 1);
    } else {
        selectedLocation.value.push(location_id);
    }

    const locationSlugs = selectedLocation.value
        .map(id => getSlugFromId(id, province_by_products.value, 'ProvinceName', 'ProvinceID'))
        .filter(Boolean)

    const newQuery = { ...route.query, page: 1 }

    if (locationSlugs.length > 0) {
        newQuery.location = locationSlugs.join(',')
    } else {
        delete newQuery.location
    }

    router.push({ query: newQuery })
    console.log(selectedLocation);

}


const clearFilter = () => {
    const keepQuery = {}

    if (route.query.keywords) {
        keepQuery.keywords = route.query.keywords
    }

    if (route.query.catp) {
        keepQuery.catp = route.query.catp
    }

    router.push({
        name: 'products',
        query: {
            ...keepQuery,
            page: 1
        },
    })
}


const fetchProducts = async () => {
    const keywordString = route.query.keywords || ''
    const keywords = keywordString
        .split(',')
        .map(k => k.trim())
        .filter(Boolean)

    try {
        if (keywords.length > 0) {
            reactive_search.value = true
        } else {
            reactive_search.value = false
        }

        const categoryParentId = getIdFromSlug(route.query.catp, categories_with_products.value)
        const categoryId = getIdFromSlug(route.query.catc, categories_with_products.value)
        const res = await api.get('/products', {
            params: {
                page_per: pagePer.value ?? 10,
                page: route.query.page || 1,
                sort_by: route.query.sort_by || '',
                category_parent_id: categoryParentId || null,
                category_id: categoryId || null,
                min_price: route.query.min_price || '',
                max_price: route.query.max_price || '',
                keywords: keywords,
                min_rate: route.query.min_rate || null,
                province_id: selectedLocation.value
            }
        })

        products.value = res.data.data
        meta.value = {
            current_page: res.data.current_page,
            last_page: res.data.last_page,
            per_page: res.data.per_page
        }
        pagination_page.value.current_page = res.data.current_page
        pagination_page.value.last_page = res.data.last_page
        pagination_page.value.total = res.data.total
    } catch (err) {
        console.error('Lỗi lấy sản phẩm:', err)
    }
}

const getProviceByProducts = async () => {
    try {
        const categoryParentId = getIdFromSlug(route.query.catp, categories_with_products.value);
        const categoryIdsFromProducts = [...new Set(products.value.map(p => p.category_id))];
        const categoryParam = categoryParentId || categoryIdsFromProducts;

        const res = await api.get('/products/provinces', {
            params: {
                category_id: categoryParam
            }
        });
        province_by_products.value = res.data;
    } catch (error) {
        console.error(error);
    }
}


const getCategoriesParents = async () => {
    try {
        const res = await api.get('categories/parents')
        root_categories.value = res.data
    } catch (error) {
        console.log(error);
    }
}

const getCategoriesWithProducts = async () => {
    try {
        const categoryParentId = getIdFromSlug(route.query.catp, root_categories.value)
        const res = await api.get(`categories/leaf-with-products/${categoryParentId}`)
        categories_with_products.value = res.data
    } catch (error) {
        console.log(error);
    }
}
onMounted(async () => {
    loading.value.filter_panel = true
    loading.value.product_list = true
    const locationParam = route.query.location
    if (locationParam && province_by_products.value.length > 0) {
        const slugs = locationParam.split(',').map(s => s.trim())
        selectedLocation.value = slugs
            .map(slug => getIdFromSlug(slug, province_by_products.value, 'ProvinceName', 'ProvinceID'))
            .filter(id => !isNaN(id))
    }
    await getCategoriesParents()
    const keywordString = route.query.keywords || ''
    if (keywordString == '') {
        await getCategoriesWithProducts()
    }
    await fetchProducts()
    await getProviceByProducts()
    loading.value.filter_panel = false
    loading.value.product_list = false
})
watch(() => route.query, fetchProducts)
</script>
<style scoped>
.skeleton {
    background-color: #eee;
    border-radius: 4px;
    position: relative;
    overflow: hidden;
}

.skeleton::after {
    content: "";
    position: absolute;
    top: 0;
    left: -100%;
    height: 100%;
    width: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
    animation: shimmer 1.5s infinite;
}

@keyframes shimmer {
    100% {
        left: 100%;
    }
}

/* Custom skeleton shapes */
.skeleton-img {
    height: 150px;
    width: 100%;
}

.skeleton-text {
    height: 14px;
}

.skeleton-button {
    height: 30px;
}

.skeleton-title {
    height: 20px;
}
</style>