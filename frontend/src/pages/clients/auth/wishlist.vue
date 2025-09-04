<script setup>
import { onMounted, ref } from 'vue';
import api from '@/configs/api';
import loading__dot from '@/components/loading/loading__dot.vue'
import productItem from '@/components/productItem.vue';


const loading = ref(false);
const products = ref([]);

const getFavoriteProducts = async () => {
  try {
    loading.value = true;
    const res = await api.get('/productWishlist-all');
    products.value = res.data.data.data || res.data.data; // Nếu backend trả về dạng paginate
  } catch (error) {
    console.error('Lỗi API sản phẩm yêu thích:', error);
  } finally {
    loading.value = false;
  }
}

onMounted(() => {
  const token = localStorage.getItem('token')
  if (token) {
    getFavoriteProducts();
  }
});
</script>
<template>
  <div class="profile__right overflow-hidden">
    <div v-if="loading" class="d-flex align-items-center justify-content-center p-4 text-center flex-column"
      style="min-height: 500px;">
      <loading__dot />
      <p class="mt-2">Đang tải sản phẩm yêu thích...</p>
    </div>
    <div v-else class="border py-3 px-5 bg-white shadow-sm mx-1 mt-lg-3 mb-2" style="min-height: 70vh;">
      <div class="mb-lg-4 border-bottom pb-3">
        <h5>Sản Phẩm Yêu Thích</h5>
        <p class="text-muted fw-medium">Quản lý sản phẩm yêu thích</p>
      </div>
      <div v-if="products.length === 0" class="text-center mt-3">
        <h5 class="text-muted">Không có sản phẩm yêu thích</h5>
        <p class="text-muted">Bạn chưa thêm sản phẩm yêu thích nào</p>
      </div>
      <div v-else class="row g-2">
        <div v-for="product in products" class="col-6 col-md-4 col-xl-3">
          <productItem :product="product" />
        </div>
      </div>
    </div>
  </div>
</template>
<style scoped>
@media (min-width: 992px) {
  .border-lg-start {
    border-left: 1px solid #dee2e6 !important;
  }
}
</style>
