<template>
  <router-view></router-view>
  <message />
</template>
<script setup>
import message from '@/components/message.vue'
import { onMounted, watch } from 'vue'
import { useConfig } from '@/composables/useConfig'

const { mainColor, initConfig } = useConfig()

// Watch mainColor và cập nhật CSS variable
watch(mainColor, (newColor) => {
  if (newColor) {
    document.documentElement.style.setProperty('--main-color', newColor)
  }
}, { immediate: true })

onMounted(async () => {
  await initConfig()
})
</script>
