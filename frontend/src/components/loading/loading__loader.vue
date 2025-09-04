<template>
    <div class="loading__loader" :style="loaderStyle">
        <div class="circle" v-for="n in 4" :key="n" :style="circleStyle"></div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    size: {
        type: String,
        default: '1.5rem',
    },
    circleSize: {
        type: String,
        default: '0.6rem',
    },
    color: {
        type: String,
        default: '#1cd425',
    },
});

const loaderStyle = computed(() => ({
    '--dim': props.size,
}));

const circleStyle = computed(() => ({
    '--dim': props.circleSize,
    backgroundColor: props.color,
}));
</script>

<style scoped>
.loading__loader {
    width: var(--dim);
    height: var(--dim);
    position: relative;
    animation: spin988 2s linear infinite;
}

.circle {
    width: var(--dim);
    height: var(--dim);
    border-radius: 50%;
    position: absolute;
    background-color: var(--text-color);
}

.circle:nth-child(1) {
    top: 0;
    left: 0;
}

.circle:nth-child(2) {
    top: 0;
    right: 0;
}

.circle:nth-child(3) {
    bottom: 0;
    left: 0;
}

.circle:nth-child(4) {
    bottom: 0;
    right: 0;
}

@keyframes spin988 {
    0% {
        transform: scale(1) rotate(0);
    }

    20%,
    25% {
        transform: scale(1.3) rotate(90deg);
    }

    45%,
    50% {
        transform: scale(1) rotate(180deg);
    }

    70%,
    75% {
        transform: scale(1.3) rotate(270deg);
    }

    95%,
    100% {
        transform: scale(1) rotate(360deg);
    }
}
</style>