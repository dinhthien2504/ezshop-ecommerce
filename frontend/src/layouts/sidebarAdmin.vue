<template>
    <nav>
        <div class="navbar__left d-flex bg-white shadow-sm " :class="{ show: showMenu }" style="height: 100%;">
            <div class="my-3 px-2 w-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="mb-2 text-uppercase fs-14 fw-semibold">MENU</div>
                    <i @click="toggleMenu" class="fs-22 ri-close-line cursor-pointer d-md-none d-block"></i>
                </div>
                <div class="d-flex flex-column gap-3">
                    <div class="menu" v-if="hasPermission('dashboard')">
                        <router-link :to="{
                            name: 'admin-dashboard'
                        }" :class="{ 'text-color': $route.name === 'admin-dashboard' }">
                            <i class="fs-18 ri-home-line me-1"></i>
                            <span class="fs-14 fw-medium">Dashboard</span>
                        </router-link>
                    </div>
                    <div class="menu" v-if="hasPermission('shop')">
                        <a href="#" data-bs-toggle="collapse" data-bs-target="#submenu2"
                            :class="{ 'text-color': isShopMenuActive }">
                            <i class="fs-18 ri-store-2-line me-1"></i>
                            <span class="fs-14 fw-medium">Cửa Hàng</span>
                            <i class="fs-18 me-1 ri-arrow-down-s-line"></i>
                        </a>
                        <div id="submenu2" class="collapse">
                            <router-link to="/admin/cua-hang" class="mt-16 fs-14 fw-medium second-text d-block ps-23"
                                :class="{ 'text-color': $route.path === '/admin/cua-hang' }">
                                Danh Sách Cửa Hàng
                            </router-link>
                            <router-link to="/admin/cua-hang-vi-pham"
                                class="mt-10 fs-14 fw-medium second-text d-block ps-23"
                                :class="{ 'text-color': $route.path === '/admin/cua-hang-vi-pham' }">
                                Cửa Hàng Vi Phạm
                            </router-link>
                            <router-link to="/admin/cua-hang/to-cao"
                                class="mt-10 fs-14 fw-medium second-text d-block ps-23"
                                :class="{ 'text-color': $route.path === '/admin/cua-hang/to-cao' }">
                                Khách Hàng Tố Cáo
                            </router-link>
                        </div>
                    </div>
                    <div class="menu" v-if="hasPermission('category')">
                        <router-link to="/admin/danh-muc" :class="{ 'text-color': $route.path === '/admin/danh-muc' }">
                            <i class="fs-18 ri-apps-2-line me-1"></i>
                            <span class="fs-14 fw-medium">Ngành Hàng</span>
                        </router-link>
                    </div>
                    <!-- <div class="menu">
                        <a href="#" data-bs-toggle="collapse" data-bs-target="#submenu3">
                            <i class="fs-18 ri-article-line me-1"></i>
                            <span class="fs-14 fw-medium">Bài Viết</span>
                            <i class="fs-18 me-1 ri-arrow-down-s-line"></i>
                        </a>
                        <div id="submenu3" class="collapse">
                            <a href="#" class="mt-16 fs-14 fw-medium second-text d-block ps-23">
                                Tất Cả Bài Viết
                            </a>

                            <a href="#" class="mt-10 fs-14 fw-medium second-text d-block ps-23">
                                Danh Mục Bài Viết
                            </a>
                        </div>
                    </div> -->
                    <div class="menu">
                        <a href="#" data-bs-toggle="collapse" data-bs-target="#submenu4"
                            :class="{ 'text-color': isAccountMenuActive }">
                            <i class="fs-18 ri-group-line me-1"></i>
                            <span class="fs-14 fw-medium">Tài Khoản</span>
                            <i class="fs-18 me-1 ri-arrow-down-s-line"></i>
                        </a>
                        <div id="submenu4" class="collapse">
                            <router-link v-if="hasPermission('staff')" to="/admin/nhan-vien"
                                class="mt-16 fs-14 fw-medium second-text d-block ps-23"
                                :class="{ 'text-color': $route.path === '/admin/nhan-vien' }">
                                Nhân Viên
                            </router-link>
                            <router-link v-if="hasPermission('user')" to="/admin/nguoi-dung"
                                class="mt-10 fs-14 fw-medium second-text d-block ps-23"
                                :class="{ 'text-color': $route.path === '/admin/nguoi-dung' }">
                                Người Dùng
                            </router-link>
                            <router-link to="/admin/ho-so" class="mt-10 fs-14 fw-medium second-text d-block ps-23"
                                :class="{ 'text-color': $route.path === '/admin/ho-so' }">
                                Hồ Sơ
                            </router-link>
                        </div>
                    </div>
                    <div class="menu" v-if="hasPermission('product')">
                        <router-link to="/admin/san-pham" :class="{ 'text-color': $route.path === '/admin/san-pham' }">
                            <i class="fs-18 ri-box-3-line me-1"></i>
                            <span class="fs-14 fw-medium">Sản Phẩm</span>
                        </router-link>
                    </div>
                    <div class="menu" v-if="hasPermission('attribute')">
                        <router-link to="/admin/thuoc-tinh"
                            :class="{ 'text-color': $route.path === '/admin/thuoc-tinh' }">
                            <i class="fs-18 me-1 ri-equalizer-2-line"></i>
                            <span class="fs-14 fw-medium">Thuộc Tính</span>
                        </router-link>
                    </div>
                    <div class="menu" v-if="hasPermission('finance')">
                        <a href="#" data-bs-toggle="collapse" data-bs-target="#submenu6"
                            :class="{ 'text-color': isFinanceMenuActive }">
                            <i class="fs-18 me-1 ri-money-dollar-circle-line"></i>
                            <span class="fs-14 fw-medium">Tài Chính</span>
                            <i class="fs-18 me-1 ri-arrow-down-s-line"></i>
                        </a>
                        <div id="submenu6" class="collapse">
                            <router-link :to="{
                                name: 'admin-payouts'
                            }" class="mt-16 fs-14 fw-medium second-text d-block ps-23"
                                :class="{ 'text-color': $route.name === 'admin-payouts' }">
                                Thanh Toán Cho Shop
                            </router-link>
                            <router-link :to="{
                                name: 'admin-fees'
                            }" class="mt-16 fs-14 fw-medium second-text d-block ps-23" :class="{ 'text-color': $route.name === 'admin-fees' }">
                                Phí Sàn
                            </router-link>
                            <router-link :to="{
                                name: 'admin-taxes'
                            }" class="mt-16 fs-14 fw-medium second-text d-block ps-23" :class="{ 'text-color': $route.name === 'admin-taxes' }">
                                Thuế Nhà Nước
                            </router-link>
                            <!-- <a href="#" class="mt-10 fs-14 fw-medium second-text d-block ps-23">
                                Phương Thức Thanh Toán
                            </a> -->
                            <router-link :to="{
                                name: 'admin-platform-wallet'
                            }" class="mt-16 fs-14 fw-medium second-text d-block ps-23"
                                :class="{ 'text-color': $route.name === 'admin-platform-wallet' }">
                                Số Dư Sàn
                            </router-link>
                        </div>
                    </div>
                    <div class="menu" v-if="hasPermission('voucher')">
                        <a href="#" data-bs-toggle="collapse" data-bs-target="#submenu7"
                            :class="{ 'text-color': isVoucherMenuActive }">
                            <i class="fs-18 ri-price-tag-3-line me-1"></i>
                            <span class="fs-14 fw-medium">Mã Giảm Giá</span>
                            <i class="fs-18 me-1 ri-arrow-down-s-line"></i>
                        </a>
                        <div id="submenu7" class="collapse">
                            <router-link :to="{
                                name: 'admin-vouchers-platform'
                            }" class="mt-16 fs-14 fw-medium second-text d-block ps-23"
                                :class="{ 'text-color': $route.name === 'admin-vouchers-platform' }">
                                Mã Của Sàn
                            </router-link>
                            <!-- <a href="#" class="mt-10 fs-14 fw-medium second-text d-block ps-23">
                                Mã Của Shop
                            </a> -->
                        </div>
                    </div>
                    <div class="menu" v-if="hasPermission('order')">
                        <router-link to="/admin/don-hang" :class="{ 'text-color': $route.path === '/admin/don-hang' }">
                            <i class="fs-18 ri-shopping-cart-line me-1"></i>
                            <span class="fs-14 fw-medium">Đơn Hàng</span>
                        </router-link>
                    </div>
                    <div class="menu" v-if="hasPermission('rank')">
                        <router-link to="/admin/cap-bac" :class="{ 'text-color': $route.path === '/admin/cap-bac' }">
                            <i class="fs-18 ri-medal-line me-1"></i>
                            <span class="fs-14 fw-medium">Xếp Hạng</span>
                        </router-link>
                    </div>
                    <div class="menu" v-if="hasPermission('settings') || hasPermission('role')">
                        <a href="#" data-bs-toggle="collapse" data-bs-target="#submenu8"
                            :class="{ 'text-color': isSystemMenuActive }">
                            <i class="fs-18 ri-settings-5-line me-1"></i>
                            <span class="fs-14 fw-medium">Hệ Thống</span>
                            <i class="fs-18 me-1 ri-arrow-down-s-line"></i>
                        </a>
                        <div id="submenu8" class="collapse">
                            <router-link v-if="hasPermission('role')" to="/admin/cau-hinh-vai-tro"
                                class="mt-16 fs-14 fw-medium second-text d-block ps-23"
                                :class="{ 'text-color': $route.path === '/admin/cau-hinh-vai-tro' }">
                                Cấu Hình Vai Trò
                            </router-link>
                            <router-link to="/admin/cau-hinh-vi-pham"
                                class="mt-10 fs-14 fw-medium second-text d-block ps-23"
                                :class="{ 'text-color': $route.path === '/admin/cau-hinh-vi-pham' }">
                                Cấu Hình Vi Phạm
                            </router-link>
                            <router-link to="/admin/cau-hinh-banner"
                                class="mt-10 fs-14 fw-medium second-text d-block ps-23"
                                :class="{ 'text-color': $route.path === '/admin/cau-hinh-banner' }">
                                Cấu Hình Banner
                            </router-link>
                            <router-link to="/admin/cau-hinh-san"
                                class="mt-10 fs-14 fw-medium second-text d-block ps-23"
                                :class="{ 'text-color': $route.path === '/admin/cau-hinh-san' }">
                                Cấu Hình Sàn
                            </router-link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</template>
<script setup>
const props = defineProps({
    showMenu: {
        type: Boolean,
        required: true
    },
    toggleMenu: {
        type: Function,
        required: true
    }
})
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import { usePermissions } from '@/composables/usePermissions'

const route = useRoute()
const { hasPermission, hasAnyPermission, hasAllPermissions } = usePermissions()

// Computed để kiểm tra menu cha có active không
const isShopMenuActive = computed(() => {
    return route.path.includes('/admin/cua-hang')
})

const isAccountMenuActive = computed(() => {
    return route.path === '/admin/nhan-vien' ||
        route.path === '/admin/nguoi-dung' ||
        route.path === '/admin/ho-so'
})

const isFinanceMenuActive = computed(() => {
    return route.name === 'admin-payouts' ||
        route.name === 'admin-platform-wallet' ||
        route.path.includes('/admin/tai-chinh') ||
        route.path.includes('/admin/phi') ||
        route.path.includes('/admin/thue')
})

const isVoucherMenuActive = computed(() => {
    return route.name === 'admin-vouchers-platform' ||
        route.path.includes('/admin/voucher')
})

const isSystemMenuActive = computed(() => {
    return route.path === '/admin/cau-hinh-vai-tro' ||
        route.path === '/admin/cau-hinh-vi-pham' ||
        route.path === '/admin/cau-hinh-banner' ||
        route.path === '/admin/cau-hinh-san'
})
</script>