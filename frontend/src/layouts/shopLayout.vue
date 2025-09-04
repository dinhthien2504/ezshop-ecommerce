<template>
    <div class="container-fluid p-0 m-0 position-relative">
        <shopHeader />
        <div class="container-shop">
            <shopMenuSidebar></shopMenuSidebar>
            <router-view ></router-view>
            <shopSidebar></shopSidebar>

        </div>
    </div>
        
</template>
<script setup>
import shopHeader from '@/layouts/shopHeader.vue';
import shopMenuSidebar from '@/layouts/shopMenuSidebar.vue';
import shopSidebar from '@/layouts/shopSidebar.vue';
import { onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { hasShop, refreshShopId } from '@/utils/shopGuardUtils'
import message from '@/utils/messageState'

const route = useRoute()
const router = useRouter()

onMounted(async () => {
    // Kiểm tra shop_id khi load layout
    if (!hasShop() && !route.meta?.allowWithoutShop) {
        // Thử refresh shop_id từ server
        const shop_id = await refreshShopId()
        
        if (shop_id === 0) {
            // Hiển thị thông báo và chuyển về trang đăng ký
            message.emit("show-message", {
                title: "Thông báo",
                text: route.query.message || "Bạn cần đăng ký shop để truy cập chức năng này",
                type: "warning"
            })
            
            if (route.name !== 'register-shop') {
                router.push({ name: 'register-shop', query: { redirect: route.fullPath } })
            }
        }
    }
})
</script>