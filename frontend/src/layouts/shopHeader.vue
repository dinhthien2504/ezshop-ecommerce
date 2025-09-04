<template>
  <div
    class="shop-chanel__header shadow-sm d-flex justify-content-between align-items-center px-4 bg-white position-fixed">
    <div class="header__left d-flex align-items-center">
      <router-link :to="{
        name: 'home'
      }" class="logo">
        <img src="/imgs/logo.png" alt="logo" />
      </router-link>

      <span class="ms-16 d-none d-md-block">Kênh Người Bán</span>
      <span class="ms-16 d-block d-md-none fs-14">Kênh Người Bán</span>
    </div>

    <div class="header__right">
      <div class="auth d-flex align-items-center position-relative">
        <div class="auth__avatar">
          <img :src="`/imgs/users/${avatar_user}`" alt="" v-if="avatar_user" />
          <img :src="`/imgs/user-default.jpg`" alt="" v-else />
        </div>

        <div class="auth__user--name ms-2 d-none d-sm-block">
          <span class="auth__user--name--text fs-12 fw-500">{{
            user_name
          }}</span>
        </div>

        <div class="auth__information d-flex flex-column position-absolute bg-white shadow-sm">
          <div class="information__user pt-4 pb-1 d-flex justify-content-between align-items-center flex-column">
            <div class="information__avatar">
              <img :src="`/imgs/users/${avatar_user}`" alt="" v-if="avatar_user" />
              <img :src="`/imgs/user-default.jpg`" alt="" v-else />
            </div>

            <div class="information__name fs-14 fw-semibold">
              {{ user_name }}
            </div>
          </div>

          <div class="information__logout h-100 d-flex align-items-center" @click="logout">
            <i class="fa-solid fa-right-from-bracket mx-2"></i>Đăng xuất
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- header-shop-chanel  -->

</template>

<script setup>
import api from "@/configs/api";
import { useRouter } from "vue-router";
import message from "@/utils/messageState";
import { user_name_auth, clear_user, avatar_user } from "@/utils/authState";
import { onBeforeMount } from "vue";
import { clearShopData } from '@/utils/shopGuardUtils'
import { clearPermissionsCache } from '@/utils/permissionUtils'


const router = useRouter();
//Chưa đăng nhập thì cút
onBeforeMount(() => {
  const token = localStorage.getItem("token");
  // if (!token) {
  //   router.push("/");
  //   message.emit("show-message", {
  //     title: "Thông báo",
  //     text: "Vui lòng đăng nhập để sử dụng chức năng này!",
  //     type: "warning",
  //   });
  // }
});

const user_name = user_name_auth;

const logout = async () => {
  try {
    const res = await api.post("/logout");
    clearPermissionsCache();
    router.push("/");
    message.emit("show-message", {
      title: "Thông báo",
      text: res.data.message,
      type: "success",
    });
  } catch (error) {
    console.error(error);
    message.emit("show-message", {
      title: "Lỗi",
      text: "Đăng xuất thất bại.",
      type: "error",
    });
  } finally {
    clear_user();
    clearShopData()
  }
};
</script>
