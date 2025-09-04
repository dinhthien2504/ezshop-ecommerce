<template>
  <headerClient />
  <router-view></router-view>
  <footerClient />
</template>
<script setup>
import headerClient from '@/layouts/header.vue';
import footerClient from '@/layouts/footer.vue';
import { ref, provide, reactive, onMounted } from 'vue';
import api from '@/configs/api';
const cartUpdated = ref(0);
provide('cartUpdated', cartUpdated);

const chat_context = reactive({
  product_to_send : null,
  receiver_id: null,
  receiver_type: null,

  open_chat_with_product(product, receiverID, receiverType){
    chat_context.product_to_send = product
    chat_context.receiver_id = receiverID;
    chat_context.receiver_type = receiverType;
  },

  //open chat without product
  open_chat_with_shop(receiverID, receiverType = 'user'){
    chat_context.product_to_send = null   
    chat_context.receiver_id = receiverID;
    chat_context.receiver_type = receiverType;
  }
})
provide('chat_product', chat_context)
onMounted(async () => {
  const token = localStorage.getItem('token')
  if (token) {
    await api.post("/rank/update-user-rank");
  }
});
</script>
