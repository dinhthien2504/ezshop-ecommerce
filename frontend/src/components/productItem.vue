<template>
    <div class="product">
        <router-link v-if="product && product.name && product.id" :to="{
            name: 'product-detail',
            params: { slug: slugify(product.name), id: product.id }
        }" class="text-decoration-none">
            <div class="product__img">
                <img :src="`/imgs/products/${product.main_image}`" :alt="product.name" />
            </div>
            <div class="d-flex flex-column p-1 gap-2">
                <p class="product__name">{{ product.name }}</p>
                <div class="product__rate">
                    <div class="d-flex align-items-center gap-1 cursor-pointer justify-content-center">
                        <p class="m-0 fs-10 text-dark fw-medium">{{ product.avg_rate }}</p>
                        <i class="fs-10 fa-solid fa-star"></i>
                    </div>
                </div>
                <p class="m-0 fs-16 text-color fw-medium">
                    {{ formatPrice(product.min_price) }}
                </p>
                <div class="d-flex align-items-center justify-content-between">
                    <p class="m-0 fs-10">
                        <i class="ri-map-pin-2-line"></i> {{ product.province_name }}
                    </p>
                    <span class="fs-12">Đã bán {{ formatToK(product.total_sell) }}</span>
                </div>
            </div>
        </router-link>
    </div>
</template>
<script setup>
import { formatPrice, formatToK } from '@/utils/formatPrice';
import { slugify } from '@/utils/slugHelpers'

const props = defineProps({
    product: Object,
});
</script>