<template>
    <div class="min-vh-100 d-flex justify-content-center align-items-center bg-light">
        <!-- SUCCESS -->
        <div v-if="success == true" class="text-center d-flex align-items-center gap-4 flex-column P-5"
            style="max-width: 620px;">
            <img src="/imgs/order-success.png" alt="Success" class="img-fluid" />
            <h4 class="text-success fw-bold">ĐẶT HÀNG THÀNH CÔNG</h4>
            <p class="text-muted">
                🔔 Cảm ơn bạn đã đặt hàng, bộ phận chăm sóc khách hàng của chúng tôi sẽ
                liên hệ với bạn trong vòng 24h để xác nhận, hãy để ý điện thoại bạn nhé!
            </p>
            <div class="d-flex align-items-center justify-content-center gap-2">
                <router-link :to="{
                    name: 'home'
                }" class="sub-btn px-3 py-1 radius-2">
                    Quay về trang chủ
                </router-link>
                <router-link :to="{
                    name: 'order-history'
                }" class="main-btn px-3 py-1 radius-2">
                    Xem đơn hàng
                </router-link>
            </div>
            <div>
                <h3 class="fs-20">Theo dõi chúng tôi trên</h3>
                <div class="d-flex align-items-center justify-content-center gap-3 fs-30">
                    <router-link to="#">
                        <i class="ri-facebook-fill"></i>
                    </router-link>
                    <router-link to="#">
                        <i class="ri-tiktok-fill"></i>
                    </router-link>
                    <router-link to="#">
                        <i class="ri-youtube-fill"></i>
                    </router-link>
                    <router-link to="#">
                        <i class="ri-instagram-line"></i>
                    </router-link>
                </div>
            </div>
        </div>

        <!-- ERROR -->
        <div v-if="success == false" class="text-center d-flex align-items-center gap-4 flex-column P-5"
            style="max-width: 620px;">
            <img src="/imgs/order-false.png" alt="Error" class="img-fluid" />
            <h4 class="text-danger fw-bold">ĐẶT HÀNG THẤT BẠI</h4>
            <p class="text-muted">
                ⚠️ Rất tiếc, đã xảy ra lỗi trong quá trình đặt hàng. Vui lòng kiểm tra lại kết nối hoặc thử lại sau.
                Nếu bạn cần hỗ trợ, hãy liên hệ với chúng tôi qua hotline hoặc trang fanpage.
            </p>
            <div class="d-flex align-items-center justify-content-center gap-2">
                <router-link :to="{
                    name: 'home'
                }" class="sub-btn px-3 py-1 radius-2">
                    Quay về trang chủ
                </router-link>
                <router-link :to="{
                    name: 'order-history'
                }" class="main-btn px-3 py-1 radius-2">
                    Xem đơn hàng
                </router-link>
            </div>
            <div>
                <h3 class="fs-20">Theo dõi chúng tôi trên</h3>
                <div class="d-flex align-items-center justify-content-center gap-3 fs-30">
                    <router-link to="#">
                        <i class="ri-facebook-fill"></i>
                    </router-link>
                    <router-link to="#">
                        <i class="ri-tiktok-fill"></i>
                    </router-link>
                    <router-link to="#">
                        <i class="ri-youtube-fill"></i>
                    </router-link>
                    <router-link to="#">
                        <i class="ri-instagram-line"></i>
                    </router-link>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref, inject } from 'vue';
import { RouterLink, useRoute, useRouter } from 'vue-router';

const route = useRoute();
const router = useRouter();
const success = ref(null);
const cartUpdated = inject('cartUpdated');

onMounted(() => {
    const query = route.query;
    const completed = localStorage.getItem('order_completed');

    if (!completed) {
        router.replace('/gio-hang');
        return;
    }

    if (query.success === 'true') {
        success.value = true;
        cartUpdated.value++;
    } else if (query.success === 'false') {
        success.value = false;
    }

    localStorage.removeItem('order_completed');
});
</script>