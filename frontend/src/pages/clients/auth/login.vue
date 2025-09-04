<template>
  <div class="container my-5">
    <div class="row">
      <div class="col-lg-7 d-none d-lg-inline">
        <div class="d-flex align-items-center justify-content-center">
          <img src="/imgs/bg.svg" alt="" class="img-fluid auth__img" />
        </div>
      </div>
      <div class="col-lg-5 col-12 d-flex align-items-center justify-content-center">
        <div class="auth__content border p-4 rounded d-flex flex-column justify-content-center text-center w-100">
          <form @submit.prevent="handleLogin()">
            <h3 class="fs-24 text-color text-uppercase mb-4">Đăng nhập</h3>

            <!-- Username/email/phone -->
            <div class="mb-4 position-relative">
              <div class="input-group auth__input">
                <span class="input-group-text">
                  <i class="fas fa-user text-muted"></i>
                </span>

                <div class="form-floating flex-grow-1">
                  <input type="text" id="login" class="form-control no-border" placeholder="Tài khoản"
                    v-model="user.login" />

                  <label for="login">Tài Khoản</label>
                </div>
              </div>
              <p v-if="errors.login" class="text-start text-danger small">
                {{ errors.login }}
              </p>
            </div>

            <!-- Password -->
            <div class="mb-4 position-relative">
              <div class="input-group auth__input">
                <span class="input-group-text">
                  <i class="fas fa-lock text-muted"></i>
                </span>
                <div class="form-floating flex-grow-1">
                  <input :type="showPassword ? 'text' : 'password'" id="password" class="form-control no-border"
                    placeholder="Mật khẩu" v-model="user.password" autocomplete="password" />
                  <label>Mật khẩu</label>
                </div>

                <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'" class="toggle-password"
                  @click="showPassword = !showPassword"></i>
              </div>
              <p v-if="errors.password" class="text-start text-danger small">
                {{ errors.password }}
              </p>
            </div>

            <div class="text-end mb-2">
              <a href="#" class="text-secondary small text-decoration-none">Quên mật khẩu?</a>
            </div>

            <!-- <button type="submit" class="main-btn w-100 radius-25 py-2 fs-18">
              <loading__loader_circle v-if="is_loading" size="20px" color="#fff" border="2px" />
              Đăng nhập
            </button> -->
            <button type="submit" :disabled="is_loading"
              class="main-btn w-100 radius-25 py-2 fs-18 d-flex align-items-center justify-content-center gap-3">
              <loading__loader_circle v-if="is_loading" size="20px" color="#fff" border="2px" />
              Đăng nhập
            </button>

            <div class="my-3 text-uppercase d-flex align-items-center">
              <hr class="flex-grow-1 me-2" />
              <span class="fs-12 fw-bold text-color">Hoặc</span>
              <hr class="flex-grow-1 ms-2" />
            </div>

            <div class="d-flex justify-content-center mb-3">
              <!-- <div id="g_id_onload" :data-client_id="googleClientId" data-context="signin" data-ux_mode="popup" data-callback="handleGoogleCallback" data-auto_prompt="false"></div>
              <div class="g_id_signin" data-type="standard"></div> -->

              <!-- filepath: /Users/nguyenduchuy/Documents/Projects/eze_shop/frontend/src/pages/clients/auth/login.vue -->
              <div id="g_id_onload" :data-client_id="googleClientId" data-context="signin" data-ux_mode="popup"
                data-callback="handleGoogleCallback" data-auto_prompt="false"></div>
              <div class="g_id_signin" data-type="standard"></div>

              <!-- <a href="#" class="auth__icon" @click="loginWithGoogle"
                ><i class="fa-brands fa-google"></i
              ></a> -->
              <!-- Nút icon Google -->

              <!-- <a href="#" class="auth__icon"><i class="fa-brands fa-facebook-f"></i></a> -->
              <!-- <a href="#" class="auth__icon"><i class="fa-brands fa-github"></i></a> -->
              <!-- <a href="#" class="auth__icon"><i class="fa-brands fa-linkedin-in"></i></a> -->
            </div>

            <p class="mb-0">
              Bạn mới biết đến EZShop?
              <router-link to="/dang-ky"><span class="text-color">Đăng ký</span></router-link>
            </p>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onBeforeMount, onMounted } from "vue";
import { useRouter } from "vue-router";
import api from "@/configs/api";
import message from "@/utils/messageState";
import { set_user, user_role } from "@/utils/authState";
import loading__loader_circle from '@/components/loading/loading__loader-circle.vue'

const router = useRouter();

//đăng nhập rồi k cho đăng nhập tiếp
onBeforeMount(() => {
  const token = localStorage.getItem("token");
  if (token) {
    router.push("/");
    message.emit("show-message", {
      title: "Thông báo",
      text: "Bạn đã đăng nhập thành công!",
      type: "warning",
    });
  }
});
const is_loading = ref(false);
const user = ref({ login: "", password: "" });
const showPassword = ref(false);
const errors = ref({ login: "", password: "" });

const validate = () => {
  let is_error = false;

  errors.value.login = "";
  errors.value.password = "";

  if (!user.value.login.trim()) {
    errors.value.login = "Vui lòng nhập tài khoản!";
    is_error = true;
  } else if (user.value.login.length < 6) {
    errors.value.login = "Vui lòng nhập tài khoản có ít nhất 6 ký tự!";
    is_error = true;
  }

  if (!user.value.password.trim()) {
    errors.value.password = "Vui lòng nhập mật khẩu!";
    is_error = true;
  } else if (user.value.password.length < 8) {
    errors.value.password = "Vui lòng nhập mật khẩu có có ít nhất 8 kí tự!";
    is_error = true;
  }

  return is_error;
};

const handleLogin = async () => {
  const is_error = validate();

  if (is_error) return;

  try {
    is_loading.value = true;
    const res = await api.post("/login", user.value);
    if (res.status == 200 || res.status == 201) {
      message.emit("show-message", {
        title: "Thông báo",
        text: "Đăng nhập thành công.",
        type: "success",
      });
      const googleId = res.data.google_id || null;
      set_user(res.data.user_name, res.data.avatar, googleId);
      user_role.value = res.data.role || null;
      localStorage.setItem("token", res.data.token);
      localStorage.setItem("user_name", res.data.user_name);
      localStorage.setItem("avatar", res.data.avatar);
      localStorage.setItem("shop_id", res.data.shop_id);
      // Chỉ lưu nếu googleId tồn tại và là object
      if (googleId && typeof googleId === 'object') {
        localStorage.setItem("google_id", JSON.stringify(googleId));
      } else {
        localStorage.removeItem("google_id"); // Xóa nếu không có
      }
      if (res.data.role) {
        localStorage.setItem("role", JSON.stringify(res.data.role));
      } else {
        localStorage.removeItem("role");
      }
      await api.post("/rank/update-user-rank");
      router.push("/");
    }
  } catch (err) {
    const res = err.response.data.message;
    message.emit("show-message", {
      title: "Thông báo",
      text: res,
      type: "error",
    });
  } finally {
    is_loading.value = false;
  }
};

// Google
const googleClientId = import.meta.env.VITE_GOOGLE_CLIENT_ID;

window.handleGoogleCallback = async (response) => {
  const id_token = response.credential
  try {
    const res = await api.post('http://localhost:8000/api/auth/google-login', { id_token })

    const data = res.data

    message.emit("show-message", {
      title: "Thông báo",
      text: data.message,
      type: "success"
    })

    set_user(res.data.user_name, res.data.avatar, res.data.google_id);
    user_role.value = res.data.role || null;
    localStorage.setItem("token", res.data.token);
    localStorage.setItem("user_name", res.data.user_name);
    localStorage.setItem("avatar", res.data.avatar);
    localStorage.setItem("google_id", res.data.google_id);
    localStorage.setItem("shop_id", res.data.shop_id);
    if (res.data.role) {
      localStorage.setItem("role", JSON.stringify(res.data.role));
    } else {
      localStorage.removeItem("role");
    }

    router.push('/')
  } catch (err) {
    console.error(err)
    alert('Đăng nhập Google thất bại')
  }
}

onMounted(() => {
  if (window.google && window.google.accounts && window.google.accounts.id) {
    window.google.accounts.id.initialize({
      client_id: googleClientId,
      callback: window.handleGoogleCallback,
      auto_select: false,
      cancel_on_tap_outside: false,
    });
    window.google.accounts.id.renderButton(
      document.querySelector('.g_id_signin'),
      { theme: 'outline', size: 'large' }
    );
  }
});

</script>
<style scoped>
.no-border:focus {
  border: none !important;
  box-shadow: none !important;
}
</style>
