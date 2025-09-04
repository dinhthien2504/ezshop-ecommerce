<!-- components/RecaptchaModal.vue -->
<template>
  <div v-if="visible" class="recaptcha-overlay">
    <div class="recaptcha-box">
      <div
        class="g-recaptcha"
        :data-sitekey="siteKey"
        data-callback= "onCaptchaVerified"
      ></div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, onUnmounted, ref } from 'vue'

const props = defineProps({
  visible: Boolean
})

const emit = defineEmits(['verified'])

const siteKey = import.meta.env.VITE_RECAPTCHA_SITE_KEY

const scriptLoaded = ref(false)

onMounted(() => {
  // Load script nếu chưa có
  if (!window.grecaptcha) {
    const script = document.createElement('script')
    script.src = 'https://www.google.com/recaptcha/api.js?onload=onRecaptchaLoadCallback&render=explicit'
    script.async = true
    script.defer = true
    document.head.appendChild(script)
  } else {
    renderCaptcha()
  }

  window.onRecaptchaLoadCallback = renderCaptcha
  window.onCaptchaVerified = (token) => {
    emit('verified', token)
  }
})

onUnmounted(() => {
  window.onCaptchaVerified = null
})

const renderCaptcha = () => {
  if (scriptLoaded.value || !props.visible) return
  scriptLoaded.value = true

  setTimeout(() => {
    if (window.grecaptcha) {
      grecaptcha.render(document.querySelector('.g-recaptcha'), {
        sitekey: siteKey,
        callback: window.onCaptchaVerified // Sửa ở đây
      })
    }
  }, 100)
}
</script>

<style scoped>
.recaptcha-overlay {
  position: fixed;
  top: 0;
  left: 0;
  z-index: 10000;
  background-color: rgba(0, 0, 0, 0.4);
  width: 100vw;
  height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
}

.recaptcha-box {
  background: white;
  padding: 20px;
  border-radius: 12px;
  box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
}
</style>
