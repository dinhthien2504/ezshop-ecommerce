<template>
    <div class="profile__right overflow-hidden">
        <div v-if="loading.fetch" class="d-flex align-items-center justify-content-center p-4 text-center flex-column"
            style="min-height: 500px;">
            <loading__dot />
            <p class="mt-2">Đang tải dữ liệu cửa hàng...</p>
        </div>
        <div v-else class="border py-3 px-5 bg-white shadow-sm mx-1 mt-lg-3 mb-2" style="min-height: 70vh;">
            <div class="border-bottom pb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5>Cửa Hàng Đang Theo Dõi</h5>
                        <p class="text-muted">Quản lý danh sách cửa hàng (<span class="text-color">{{ meta.total || 0 }}</span>)</p>
                    </div>
                </div>
            </div>
            
            <div v-if="!loading.fetch && followingShops.length === 0" 
                class="d-flex align-items-center justify-content-center flex-column"
                style="min-height: 400px;">
                <i style="font-size: 150px; width: 150px; height: 200px; color: gainsboro;" class="bi bi-shop"></i>
                <p class="text-muted">Bạn chưa theo dõi cửa hàng nào</p>
            </div>
            
            <div v-if="!loading.fetch && followingShops.length > 0" class="row">
                <div class="col-12 col-md-6" v-for="follow in followingShops" :key="follow.id">
                    <div class="p-3 d-flex">
                        <img 
                            :src="follow.shop.image ? `/imgs/shops/${follow.shop.image}` : '/imgs/avtDefault.png'" 
                            :alt="follow.shop.shop_name"
                            style="border-radius: 99px; width: 100px; height: 100px; object-fit: cover;" 
                            class="me-3 shadow-sm">
                        <div class="d-flex flex-column justify-content-center">
                            <div class="profile-name fs-20 fw-bold">{{ follow.shop.shop_name }}</div>
                            <div class="profile-stats">
                                <span class="stat-number text-danger">{{ follow.shop.products_count || 0 }}</span>
                                sản phẩm |
                                <span class="stat-number text-danger">{{ follow.shop.followers_count || 0 }}</span>
                                theo dõi |
                                <span class="stat-number text-danger">{{ follow.shop.rates_count || 0 }}</span>
                                đánh giá
                            </div>
                            <div class="profile-description mt-1 d-flex justify-content-between">
                                <span class="fw-bold cursor-pointer text-primary" @click="goToShop(follow.shop)">
                                    Xem cửa hàng
                                </span>
                                <span 
                                    class="text-danger fw-bold cursor-pointer" 
                                    @click="unfollowShop(follow.shop)"
                                    :disabled="loading.unfollow === follow.shop.id">
                                    Bỏ theo dõi
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="!loading.fetch && followingShops.length > 0 && meta.last_page > 1" 
                class="d-flex justify-content-center mt-4">
                <pagination 
                    :meta="meta"
                    @changePage="changePage" />
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/configs/api'
import message from '@/utils/messageState'
import loading__dot from '@/components/loading/loading__dot.vue'
import pagination from '@/components/pagination.vue'

const router = useRouter()

// Data
const followingShops = ref([])
const loading = ref({
    fetch: false,
    unfollow: null
})
const meta = ref({
    current_page: 1,
    last_page: 1,
    total: 0,
    per_page: 6
})

// Methods
const fetchFollowingShops = async (page = 1) => {
    try {
        loading.value.fetch = true
        const res = await api.get('/shop/following', {
            params: {
                page: page,
                per_page: meta.value.per_page
            }
        })
        
        if (res.status === 200) {
            followingShops.value = res.data.data
            meta.value = res.data.meta
        }
    } catch (error) {
        console.error('Lỗi khi lấy danh sách cửa hàng theo dõi:', error)
        message.emit('show-message', {
            title: 'Lỗi',
            text: 'Không thể tải danh sách cửa hàng đang theo dõi',
            type: 'error'
        })
    } finally {
        loading.value.fetch = false
    }
}

const unfollowShop = async (shop) => {
    try {
        loading.value.unfollow = shop.id
        const res = await api.post('/shop/follow', {
            shop_id: shop.id
        })
        
        if (res.status === 200) {
            message.emit('show-message', {
                title: 'Thành công',
                text: res.data.message,
                type: 'success'
            })
            
            // Refresh danh sách
            await fetchFollowingShops(meta.value.current_page)
        }
    } catch (error) {
        console.error('Lỗi khi bỏ theo dõi cửa hàng:', error)
        message.emit('show-message', {
            title: 'Lỗi',
            text: 'Không thể bỏ theo dõi cửa hàng',
            type: 'error'
        })
    } finally {
        loading.value.unfollow = null
    }
}

const goToShop = (shop) => {
    if (shop && shop.id) {
        // Tạo slug từ tên shop
        const shopSlug = shop.shop_name
            .toLowerCase()
            .replace(/\s+/g, '-')
            .replace(/[^a-z0-9-]/g, '')

        router.push({
            name: 'shop-profile',
            params: {
                slug: shopSlug,
                id: shop.id
            }
        })
    }
}

const changePage = (page) => {
    fetchFollowingShops(page)
}

// Lifecycle
onMounted(() => {
    fetchFollowingShops()
})
</script>