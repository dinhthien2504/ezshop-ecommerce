<template>
    <div v-if="isLoading" class="row g-2">
        <div v-for="n in item" class="col-6 col-md-3 col-xl-2">
            <div class="product">
                <div class="skeleton product__skeleton">
                    <div class="skeleton-img"></div>
                    <div class="skeleton-line mt-2" style="width: 90%; height: 14px;"></div>
                    <div class="skeleton-line" style="width: 60%; height: 12px;"></div>
                    <div class="skeleton-line" style="width: 50%; height: 12px;"></div>
                    <div class="d-flex justify-content-between mt-2 w-100">
                        <div class="skeleton-line" style="width: 40%; height: 10px;"></div>
                        <div class="skeleton-line" style="width: 30%; height: 10px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
const props = defineProps({
    item: {
        type: Number,
        default: 6,
    },
    isLoading: Boolean,
});

</script>
<style scoped>
/* Giao diện chung */
.skeleton {
    pointer-events: none;
    user-select: none;
    animation: pulse 1.5s infinite;
}

.skeleton-img {
    width: 100%;
    aspect-ratio: 1 / 1;
    background-color: #f0f0f0;
    border-radius: 8px;
}

.skeleton-line {
    background-color: #e0e0e0;
    border-radius: 6px;
}

/* Hiệu ứng */
@keyframes pulse {
    0% {
        opacity: 1;
    }

    50% {
        opacity: 0.4;
    }

    100% {
        opacity: 1;
    }
}

/* Tuỳ chỉnh nếu cần */
.product__skeleton {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 8px;
    gap: 6px;
}
</style>