import { defineStore } from 'pinia';
import { ref } from 'vue';
export const useCheckoutStore = defineStore('checkout', () => {
    const checkoutItems = ref([]);
    return { checkoutItems };
});