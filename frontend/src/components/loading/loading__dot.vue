<template>
    <div class="loading__dot" :style="styleLoadingDot">
        <div class="dot" :style="styleDot" v-for="n in 8" :key="n"></div>
    </div>
</template>
<script setup>
import { computed } from 'vue';
import { onMounted, onBeforeUnmount } from 'vue';
const props = defineProps({
    size: {
        type: String,
        default: '30px'
    },
    color: {
        type: String,
        default: 'var(--text-color)'
    }
})

const styleLoadingDot = computed(() => ({
    '--uib-size': props.size
}))
const styleDot = computed(() => ({
    '--color': props.color
}))

// // Âm thanh
// let intervalId = null;
// const playSound = () => {
//     const audio = new Audio('/imgs/mih.mp3');
//     audio.volume = 1; // Âm lượng vừa phải
//     audio.play().then(() => console.log('🔊 Đã phát âm thanh'))
//         .catch((err) => console.warn('⚠️ Không phát được âm thanh', err));
// }

// onMounted(() => {
//     // Phát âm thanh theo nhịp animation (khoảng 0.125s)
//     intervalId = setInterval(playSound, 125);
// })

// onBeforeUnmount(() => {
//     clearInterval(intervalId);
// })

</script>
<style scoped>
.loading__dot {
    --uib-speed: .9s;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: flex-start;
    height: var(--uib-size);
    width: var(--uib-size);
}

.dot {
    position: absolute;
    top: 0;
    left: 0;
    display: flex;
    align-items: center;
    justify-content: flex-start;
    height: 100%;
    width: 100%;
}

.dot::before {
    content: '';
    height: 20%;
    width: 20%;
    border-radius: 50%;
    background-color: var(--color, #333);
    transform: scale(0);
    opacity: 0.5;
    animation: pulse0112 calc(var(--uib-speed) * 1.111) ease-in-out infinite;
    box-shadow: 0 0 20px rgba(18, 31, 53, 0.3);
}

.dot:nth-child(2) {
    transform: rotate(45deg);
}

.dot:nth-child(2)::before {
    animation-delay: calc(var(--uib-speed) * -0.875);
}

.dot:nth-child(3) {
    transform: rotate(90deg);
}

.dot:nth-child(3)::before {
    animation-delay: calc(var(--uib-speed) * -0.75);
}

.dot:nth-child(4) {
    transform: rotate(135deg);
}

.dot:nth-child(4)::before {
    animation-delay: calc(var(--uib-speed) * -0.625);
}

.dot:nth-child(5) {
    transform: rotate(180deg);
}

.dot:nth-child(5)::before {
    animation-delay: calc(var(--uib-speed) * -0.5);
}

.dot:nth-child(6) {
    transform: rotate(225deg);
}

.dot:nth-child(6)::before {
    animation-delay: calc(var(--uib-speed) * -0.375);
}

.dot:nth-child(7) {
    transform: rotate(270deg);
}

.dot:nth-child(7)::before {
    animation-delay: calc(var(--uib-speed) * -0.25);
}

.dot:nth-child(8) {
    transform: rotate(315deg);
}

.dot:nth-child(8)::before {
    animation-delay: calc(var(--uib-speed) * -0.125);
}

@keyframes pulse0112 {

    0%,
    100% {
        transform: scale(0);
        opacity: 0.5;
    }

    50% {
        transform: scale(1);
        opacity: 1;
    }
}
</style>