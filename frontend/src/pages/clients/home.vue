<template>
  <div v-if="loading.resources" style="height: 60vh;" class="d-flex align-items-center justify-content-center">
    <loading__dot />
  </div>
  <div v-else>
    <div class="container-fluid bg-white pb-10 d-none d-lg-block">
      <div class="container">
        <div class="banner mt-10 d-flex py-4">
          <div class="banner__slider col-8 me-10" @mouseenter="stopAutoSlide" @mouseleave="startAutoSlide">
            <div class="slider__wrapper" style="overflow:hidden; position:relative; height:260px;">
              <div class="slider__track" :style="{
                display: 'flex',
                transition: 'transform 0.5s cubic-bezier(.4,0,.2,1)',
                transform: `translateX(-${currentSlide * 100}%)`
              }">
                <div class="slider__item" v-for="(slide, idx) in slides" :key="idx" style="min-width:100%;">
                  <a :href="slide.link || '#'">
                    <img :src="`/imgs/banners/${slide.image}`" :alt="slide.title"
                      style="width:100%;object-fit:cover;" />
                  </a>
                </div>
              </div>
            </div>

            <div class="slider__prev">
              <button type="button" @click="prevSlide">
                <i class="fa-solid fa-caret-left text-white"></i>
              </button>
            </div>

            <div class="slider__next">
              <button type="button" @click="nextSlide">
                <i class="fa-solid fa-caret-right text-white"></i>
              </button>
            </div>

            <div class="slider__dots">
              <div v-for="(slide, idx) in slides" :key="idx" class="slider__dot"
                :class="{ active: idx === currentSlide }" @click="goToSlide(idx)"
                style="width:10px;height:10px;border-radius:50%;background:#ccc;margin:0 4px;cursor:pointer;"></div>
            </div>
          </div>

          <div class="banner__ads col">
            <a :href="banner1.link || '#'">
              <img :src="`/imgs/banners/${banner1.image}`" :alt="`${banner1.title}`" />
            </a>
            <a :href="banner2.link || '#'">
              <img :src="`/imgs/banners/${banner2.image}`" :alt="`${banner2.title}`" />
            </a>
          </div>
        </div>
      </div>
    </div>
    <!-- End Banner  -->

    <div class="container">
      <div class="ads-mobile-container d-lg-none d-sm-block mt-2">
        <div class="ads-mobile row g-2">
          <div class="ads-mobile__item col-3 position-relative">
            <div class="ads-mobile__img">
              <img :src="`/imgs/banners/${mobile1.image}`" alt="">
            </div>

            <a href=""><button class="ads-mobile__btn radius-2 sub-btn py-1 px-1 fs-12 position-absolute bottom-0">Xem
                ngay</button></a>
          </div>

          <div class="ads-mobile__item col-6 position-relative">
            <div class="ads-mobile__img">
              <img :src="`/imgs/banners/${mobile2.image}`" alt="">
            </div>

            <a href=""><button class="ads-mobile__btn radius-2 sub-btn py-1 px-1 fs-12 position-absolute bottom-0">Xem
                ngay</button></a>
          </div>

          <div class="ads-mobile__item col-3 position-relative">
            <div class="ads-mobile__img">
              <img :src="`/imgs/banners/${mobile3.image}`" alt="">
            </div>

            <a href=""><button class="ads-mobile__btn radius-2 sub-btn py-1 px-1 fs-12 position-absolute bottom-0">Xem
                ngay</button></a>
          </div>
        </div>
      </div>

      <div class="home-categories-title bg-white mt-16 pt-16 pb-16 px-3 fs-6 fw-medium text-grey">
        DANH MỤC
      </div>
      <div v-if="loading.categories" class="home-categories">
        <router-link to="#" v-for="n in 12" :key="n" class="category-item skeleton">
          <img src="/imgs/placeholder-skeleton.png" alt="Loading">
        </router-link>
      </div>
      <div v-else class="home-categories">
        <router-link v-for="cate in categories" class="category-item" :to="{
          name: 'products',
          query: {
            catp: getSlugFromId(cate.id, categories)
          }
        }">
          <img :src="`/imgs/categories/${cate.image}`" :alt="cate.name">
          <span class="p-1">{{ cate.name }}</span>
        </router-link>
      </div>

      <!-- End Category  -->

      <div class="top__view bg-white px-3 pt-16 pb-16 position-relative mt-sm-2 mt-lg-4">
        <div class="top__view__top d-flex justify-content-between align-items-center mb-3">
          <div class="top__view__top--left d-flex align-items-center">
            <div class="top__title fs-18 fw-semibold me-sm-2 me-lg-3">
              Top lượt xem 7 ngày qua
            </div>
          </div>
        </div>

        <div v-if="loading.products_top_view" class="top__view__bottom row g-2">
          <div v-for="n in 6" :key="n" class="col-6 col-md-4 col-lg-3 col-xl-2">
            <div class="view-product skeleton p-0">
              <div class="view-product__badge-top">TOP</div>
              <div class="view-product__img">
                <div class="skeleton-img"></div>
              </div>
              <div class="view-product__bottom">
                <div class="skeleton-line"></div>
              </div>
            </div>
          </div>
        </div>

        <div v-else class="top__view__bottom row g-3">
          <div v-for="p in products_top_view" :key="p.id" class="col-6 col-md-4 col-lg-3 col-xl-2">
            <div class="view-product p-0">
              <div class="view-product__badge-top">TOP</div>
              <router-link v-if="p && p.name && p.id" :to="{
                name: 'product-detail',
                params: {
                  slug: slugify(p.name),
                  id: p.id
                }
              }" class="text-decoration-none">
                <div class="view-product__img">
                  <img :src="`/imgs/products/${p.main_image}`" :alt="p.name" />
                </div>
              </router-link>
              <div class="view-product__bottom">
                <p>{{ formatToK(Number(p.view_count)) }}
                  lượt xem</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="container bg-white mt-lg-4 p-2">
        <img src="/imgs/banners/banner-section.jpg" class="shadow-sm" alt="Hình ảnh banner" style="width: 100%;">
      </div>
      <!-- End top__view  -->
      <div class="suggest-product bg-white mt-lg-4">
        <div class="fs-18 fw-semibold text-center">Gợi ý hôm nay</div>
      </div>
      <div class="suggest-product__box mt-10">
        <skeletonProduct v-if="loading.products" :isLoading="loading.products" />
        <div v-else class="row g-2">
          <div v-for="product in products" class="col-6 col-md-3 col-xl-2">
            <productItem :product="product" />
          </div>
        </div>
      </div>
      <!-- End suggest-product  -->
    </div>
    <!-- End body  -->
  </div>

</template>

<script setup>
import { onMounted, ref } from 'vue'
import api from '@/configs/api'
import loading__dot from '@/components/loading/loading__dot.vue'
import productItem from '@/components/productItem.vue'
import skeletonProduct from '@/components/skeletonProduct.vue'
import { getSlugFromId, slugify } from '@/utils/slugHelpers'
import { formatToK } from '@/utils/formatPrice'

const products = ref([])
const products_top_view = ref([])
const categories = ref([])
const loading = ref({
  resources: false,
  products: true,
  products_top_view: false,
  categories: false
})

const getHomepageProducts = async () => {
  try {
    loading.value.products = true
    const res = await api.get('/products/home-page')
    // Check if response contains error
    if (res.data && res.data.error) {
      console.error('API Error:', res.data.error)
      products.value = []
      return
    }
    // Ensure res.data is an array before filtering
    const data = Array.isArray(res.data) ? res.data : []
    // Filter out products without required fields
    products.value = data.filter(product => 
      product && product.id && product.name
    )
  } catch (error) {
    console.error(error)
    products.value = []
  } finally {
    loading.value.products = false
  }
}

const getTopViewedProducts = async () => {
  try {
    loading.value.products_top_view = true
    const res = await api.get('products/top-view')
    // Check if response contains error
    if (res.data && res.data.error) {
      console.error('API Error:', res.data.error)
      products_top_view.value = []
      return
    }
    // Ensure res.data is an array before filtering
    const data = Array.isArray(res.data) ? res.data : []
    // Filter out products without required fields
    products_top_view.value = data.filter(product => 
      product && product.id && product.name
    )
  } catch (error) {
    console.error(error)
    products_top_view.value = []
  } finally {
    loading.value.products_top_view = false
  }
}

const getRootCategoriesWithProducts = async () => {
  try {
    loading.value.categories = true
    const res = await api.get('/categories/root-with-products')
    categories.value = res.data
  } catch (error) {
    console.log(error)
  } finally {
    loading.value.categories = false
  }
}
const banner1 = ref({})
const banner2 = ref({})
const banner3 = ref({})
const mobile1 = ref({})
const mobile2 = ref({})
const mobile3 = ref({})
const slides = ref([])

const getBanners = async () => {
  try {
    const res = await api.get('/home-banner')
    // Nếu không có banner thì dùng ảnh mặc định
    banner1.value = res.data.home_1 || { image: 'banner1.jpg', link: null, title: 'bannerDefaul1' }
    banner2.value = res.data.home_2 || { image: 'banner1.jpg', link: null, title: 'bannerDefaul1' }
    banner3.value = res.data.home_3 || { image: 'banner1.jpg', link: null, title: 'bannerDefaul1' }
    mobile1.value = res.data.mobile_1 || { image: 'banner1.jpg', link: null, title: 'bannerDefaul1' }
    mobile2.value = res.data.mobile_2 || { image: 'banner1.jpg', link: null, title: 'bannerDefaul1' }
    mobile3.value = res.data.mobile_3 || { image: 'banner1.jpg', link: null, title: 'bannerDefaul1' }


    // Nếu sliders rỗng thì dùng mảng 3 ảnh mặc định
    if (res.data.sliders && res.data.sliders.length > 0) {
      slides.value = res.data.sliders
    } else {
      slides.value = [
        { image: 'banner1.jpg', link: null, title: 'bannerDefaul1' },
        { image: 'banner2.jpg', link: null, title: 'bannerDefaul2' },
        { image: 'banner3.jpg', link: null, title: 'bannerDefaul3' }
      ]
    }
  } catch (error) {
    console.log(error)
  }
}

const currentSlide = ref(0)
let slideInterval = null

const nextSlide = () => {
  currentSlide.value = (currentSlide.value + 1) % slides.value.length
}
const prevSlide = () => {
  currentSlide.value = (currentSlide.value - 1 + slides.value.length) % slides.value.length
}
const goToSlide = (idx) => {
  currentSlide.value = idx
}
const startAutoSlide = () => {
  slideInterval = setInterval(nextSlide, 4000)
}
const stopAutoSlide = () => {
  if (slideInterval) clearInterval(slideInterval)
}

onMounted(async () => {
  loading.value.resources = true
  await getBanners()
  loading.value.resources = false
  getRootCategoriesWithProducts()
  getTopViewedProducts()
  getHomepageProducts()
  startAutoSlide()
})

</script>