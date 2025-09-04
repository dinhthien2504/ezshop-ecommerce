<script setup>
import { ref, onMounted, computed } from "vue";
import { useRoute, useRouter } from "vue-router";
import { user_name_auth, avatar_user, google_auth } from '@/utils/authState'
import api from "@/configs/api";
import message from "@/utils/messageState";
import useGoTo from '@/utils/useGoto'
import { clear_user } from '@/utils/authState'
const { goTo } = useGoTo()
// Tự động mở menu account nếu có route con đang active
onMounted(() => {
    // const token = localStorage.getItem('token')
    // if (!token) {
    //     message.emit("show-message", { title: "Thông báo", text: 'Vui lòng đăng nhập để sử dụng chức năng này!', type: "warning" })
    //     goTo('/dang-nhap')
    //     return
    // }
    if (isAccountActive.value) {
        showAccountMenu.value = true;
    }
});
const route = useRoute();
const router = useRouter();
const goBack = () => {
    router.back();
}
const showMenu = ref(false);
const showAccountMenu = ref(false);
const toggleMenu = () => {
    showMenu.value = !showMenu.value;
};

const toggleAccountMenu = () => {
    showAccountMenu.value = !showAccountMenu.value;
};

const goToAccountAndToggle = () => {
    // Chuyển đến trang hồ sơ
    router.push({ name: 'user-profile' });
    // Mở menu account
    showAccountMenu.value = true;
};

const user_name = user_name_auth
const avatar = computed(() => avatar_user.value ? `/imgs/users/${avatar_user.value}` : '/imgs/user-default.jpg')
const google_id = google_auth
// console.log(typeof google_id.value)

// Kiểm tra xem có route nào trong account đang active không
const isAccountActive = computed(() => {
    return route.name === 'user-profile' || route.name === 'user-address' || route.name === 'user-change-password';
});

const logout = async () => {
    try {
        const res = await api.post('/logout');
        message.emit("show-message", { title: "Thông báo", text: res.data.message, type: "success" })
        clear_user()
        router.push("/")
    } catch (error) {
        console.error(error);
        message.emit("show-message", { title: "Lỗi", text: "Đăng xuất thất bại.", type: "error" })
    }
}
</script>
<template>
    <div class="container p-0">
        <div class="d-flex gap-1 flex-column flex-lg-row">
            <!-- Toggle button for small screens (always on top) -->
            <div class="d-lg-none w-100 bg-white shadow-sm p-2 mb-2">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="ms-3 fw-medium align-items-center d-flex gap-2" @click="goBack()"
                        style="cursor: pointer;">
                        <div class="text-decoration-none text-color">
                            <i class="fs-20 ri-arrow-left-line"></i>
                        </div>
                        Trở về
                    </div>
                    <div style="cursor: pointer;" class="fs-24 text-danger me-3" @click="toggleMenu">
                        <i class="fa fa-bars"></i>
                    </div>
                </div>

                <transition name="fade">
                    <nav v-if="showMenu" class="nav flex-column gap-2 mb-3 bg-white p-3 rounded">
                        <div>
                            <a @click="goToAccountAndToggle"
                                class="fw-medium fs-16 cursor-pointer d-flex justify-content-between align-items-center"
                                :class="{
                                    active: isAccountActive
                                }">
                                <span>
                                    <i class="fs-18 fa-regular fa-user me-1 text-primary"></i>
                                    Tài Khoản
                                </span>
                                <i :class="showAccountMenu ? 'ri-arrow-up-s-line' : 'ri-arrow-down-s-line'"></i>
                            </a>
                            <transition name="fade">
                                <div v-if="showAccountMenu" class="ms-3 mt-2">
                                    <router-link class="nav-link text-dark fw-medium fs-14 py-1"
                                        :to="{ name: 'user-profile' }" :class="{
                                            active: route.name === 'user-profile'
                                        }" @click="showMenu = false">
                                        Hồ Sơ
                                    </router-link>
                                    <router-link class="nav-link text-dark fw-medium fs-14 py-1"
                                        :to="{ name: 'user-address' }" :class="{
                                            active: route.name === 'user-address'
                                        }" @click="showMenu = false">Địa Chỉ</router-link>
                                    <router-link v-if="!google_id" class="nav-link text-dark fw-medium fs-14 py-1"
                                        :to="{ name: 'user-change-password' }" :class="{
                                            active: route.name === 'user-change-password'
                                        }" @click="showMenu = false">Đổi Mật Khẩu</router-link>
                                </div>
                            </transition>
                        </div>
                        <router-link :to="{ name: 'order-history' }" class="fw-medium fs-16" :class="{
                            active: route.name === 'order-history' || route.name === 'order-history-detail'
                        }" @click="showMenu = false">
                            <i class="fs-18 fa-regular fa-calendar me-1 text-primary"></i>
                            Đơn Hàng
                        </router-link>
                        <router-link :to="{ name: 'user-wishlist' }" class="fw-medium fs-16" :class="{
                            active: route.name === 'user-wishlist'
                        }" @click="showMenu = false">
                            <i class="fs-18 fa-regular fa-heart me-1 text-primary"></i>
                            Yêu Thích
                        </router-link>
                        <router-link :to="{ name: 'user-following' }" class="fw-medium fs-16" :class="{
                            active: route.name === 'user-following'
                        }" @click="showMenu = false">
                            <i class="bi bi-shop-window fs-18 me-1 text-primary"></i>
                            Theo Dõi
                        </router-link>
                        <div class="fw-medium fs-16 cursor-pointer" @click="logout">
                            <i class="ri-logout-circle-r-line me-1 text-primary fs-18"></i>
                            Đăng Xuất
                        </div>
                    </nav>
                </transition>
            </div>
            <!-- Sidebar menu for large screens -->
            <div class="profile__left d-none d-lg-block me-3 mt-3">
                <div class="d-flex align-items-center gap-2 border-bottom py-3">
                    <img :src="avatar" alt="Hình ảnh" class="shadow-sm" />
                    <div class="d-flex flex-column gap-0">
                        <p class="ms-1 fs-14 fw-medium">{{ user_name }}</p>
                        <div class="d-flex fs-14">
                            <i class="ri-pencil-fill text-secondary"></i>
                            <router-link class="text-decoration-none text-secondary" :to="{ name: 'user-profile' }">Sửa
                                hồ sơ</router-link>
                        </div>
                    </div>
                </div>
                <nav class="nav flex-column mt-4 gap-2">
                    <div>
                        <a @click="goToAccountAndToggle"
                            class="fw-medium fs-16 cursor-pointer d-flex justify-content-between align-items-center"
                            :class="{
                                active: isAccountActive
                            }">
                            <span>
                                <i class="fs-18 fa-regular fa-user me-1 text-primary"></i>
                                Tài Khoản
                            </span>
                            <i :class="showAccountMenu ? 'ri-arrow-up-s-line' : 'ri-arrow-down-s-line'"></i>
                        </a>
                        <transition name="fade">
                            <div v-if="showAccountMenu" class="ms-3 mt-2">
                                <router-link class="nav-link text-dark fw-medium fs-14 py-1"
                                    :to="{ name: 'user-profile' }" :class="{
                                        active: route.name === 'user-profile'
                                    }">
                                    Hồ Sơ
                                </router-link>
                                <router-link class="nav-link text-dark fw-medium fs-14 py-1"
                                    :to="{ name: 'user-address' }" :class="{
                                        active: route.name === 'user-address'
                                    }">Địa Chỉ</router-link>
                                <router-link v-if="!google_id" class="nav-link text-dark fw-medium fs-14 py-1"
                                    :to="{ name: 'user-change-password' }" :class="{
                                        active: route.name === 'user-change-password'
                                    }">Đổi Mật Khẩu</router-link>
                            </div>
                        </transition>
                    </div>
                    <router-link :to="{ name: 'order-history' }" class="fw-medium fs-16" :class="{
                        active: route.name === 'order-history' || route.name === 'order-history-detail'
                    }">
                        <i class="fs-18 fa-regular fa-calendar me-1 text-primary"></i>
                        Đơn Hàng
                    </router-link>
                    <router-link :to="{ name: 'user-wishlist' }" class="fw-medium fs-16" :class="{
                        active: route.name === 'user-wishlist'
                    }">
                        <i class="fs-18 fa-regular fa-heart me-1 text-primary"></i>
                        Yêu Thích
                    </router-link>
                    <router-link :to="{ name: 'user-following' }" class="fw-medium fs-16" :class="{
                        active: route.name === 'user-following'
                    }">
                        <i class="bi bi-shop-window fs-18 me-1 text-primary"></i>
                        Theo Dõi
                    </router-link>
                    <div class="fw-medium fs-16 cursor-pointer" @click="logout">
                        <i class="ri-logout-circle-r-line me-1 text-primary fs-18"></i>
                        Đăng Xuất
                    </div>
                </nav>
            </div>
            <router-view></router-view>
        </div>
    </div>
</template>
<style scoped>
.active {
    color: red !important;
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

nav a:hover {
    color: red !important;
}

.cursor-pointer {
    cursor: pointer;
}
</style>