<template>
    <div class="d-flex justity-content-end fw-semibold fs-14 main-color">
        <span>
            {{ label }}
        </span>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    status: {
        type: Number,
        required: true
    }
})
// ['Chờ xác nhận', 'Đã xác nhận', 'Chờ giao hàng', 'Hoàn thành', 'Đã hủy', 'Trả hàng/Hoàn tiền']
const statusMap = {
    1: { label: 'Chờ xác nhận' },
    2: { label: 'Đã xác nhận' },
    3: { label: 'Chờ giao hàng' },
    4: { label: 'Đã giao hàng' },
    5: { label: 'Đã hoàn thành' },
    6: { label: 'Đã hủy' },
    7: { label: 'Trả hàng/Hoàn tiền' }
}


const currentStatus = computed(() => statusMap[props.status] || {
    label: 'Không xác định',
})

const label = computed(() => currentStatus.value.label)
</script>