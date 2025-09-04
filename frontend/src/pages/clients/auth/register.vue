<template>
  <div class="container my-5">
    <div class="row pb-5">
      <div class="col-lg-5 col-12 d-flex align-items-center justify-content-center">
        <div class="auth__content border p-4 rounded d-flex flex-column justify-content-center text-center w-100">
          <form @submit.prevent="register">
            <h3 class="fs-24 text-color text-uppercase mb-4">Đăng Ký</h3>
            <!-- Username -->
            <div class="mb-3 position-relative">
              <div class="input-group auth__input">
                <span class="input-group-text">
                  <i class="fas fa-user text-muted"></i>
                </span>
                <div class="form-floating flex-grow-1">
                  <input type="text" id="username" class="form-control no-border" placeholder=""
                    v-model="user.user_name" />
                  <label for="username">Tên người dùng</label>
                </div>
              </div>
              <p class="m-0 text-danger text-start" v-if="errors.user_name">
                {{
                  Array.isArray(errors.user_name)
                    ? errors.user_name[0]
                    : errors.user_name
                }}
              </p>
            </div>
            <!-- Email -->
            <div class="mb-3 position-relative">
              <div class="input-group auth__input">
                <span class="input-group-text">
                  <i class="ri-mail-fill text-muted"></i>
                </span>
                <div class="form-floating flex-grow-1">
                  <input type="email" id="email" class="form-control no-border" placeholder="" v-model="user.email" />
                  <label for="email">Email</label>
                </div>
              </div>
              <p class="m-0 text-danger text-start" v-if="errors.email">
                {{
                  Array.isArray(errors.email) ? errors.email[0] : errors.email
                }}
              </p>
            </div>
            <!-- Password -->
            <div class="mb-3 position-relative">
              <div class="input-group auth__input">
                <span class="input-group-text">
                  <i class="fas fa-lock text-muted"></i>
                </span>
                <div class="form-floating flex-grow-1">
                  <input :type="showPassword ? 'text' : 'password'" id="password" class="form-control no-border"
                    placeholder="" autocomplete="password" v-model="user.password" />
                  <label for="password">Mật khẩu</label>
                </div>
              </div>
              <p class="m-0 text-danger text-start" v-if="errors.password">
                {{
                  Array.isArray(errors.password)
                    ? errors.password[0]
                    : errors.password
                }}
              </p>
            </div>
            <!-- Password Confirmation -->
            <div class="mb-3 position-relative">
              <div class="input-group auth__input">
                <span class="input-group-text">
                  <i class="fas fa-lock text-muted"></i>
                </span>
                <div class="form-floating flex-grow-1">
                  <input :type="showPassword ? 'text' : 'password'" id="password_confirm" class="form-control no-border"
                    placeholder="" autocomplete="password_confirmation" v-model="user.password_confirmation" />
                  <label for="password_confirm">Xác nhận mật khẩu</label>
                </div>
              </div>
              <p class="m-0 text-danger text-start" v-if="errors.password_confirmation">
                {{
                  Array.isArray(errors.password_confirmation)
                    ? errors.password_confirmation[0]
                    : errors.password_confirmation
                }}
              </p>
            </div>
            <div class="d-flex align-items-center justify-content-start my-2 gap-2">
              <label for="show_pass" class="cursor-pointer">Hiện mật khẩu</label>
              <div class="checkbox">
                <label class="checkbox-label">
                  <input type="checkbox" id="show_pass" @change="showPassword = !showPassword" />
                  <div class="checkbox-wrapper">
                    <div class="checkbox-bg"></div>
                    <svg fill="none" viewBox="0 0 24 24" class="checkbox-icon">
                      <path stroke-linejoin="round" stroke-linecap="round" stroke-width="3" stroke="currentColor"
                        d="M4 12L10 18L20 6" class="check-path"></path>
                    </svg>
                  </div>
                </label>
              </div>
            </div>

            <div class="mb-3">
              <!-- <div class="g-recaptcha" :data-sitekey=siteKey></div> -->
            </div>

            <button type="submit" :disabled="is_loading"
              class="main-btn w-100 radius-25 py-2 fs-18 d-flex align-items-center justify-content-center gap-3">
              <loading__loader_circle v-if="is_loading" size="20px" color="#fff" border="2px" />
              Đăng ký
            </button>
            <div class="my-3 text-uppercase d-flex align-items-center">
              <hr class="flex-grow-1 me-2" />
              <span class="fs-12 fw-bold text-color">Hoặc</span>
              <hr class="flex-grow-1 ms-2" />
            </div>

            <div class="d-flex justify-content-center mb-3">
              <div id="g_id_onload" :data-client_id="googleClientId" data-context="signin" data-ux_mode="popup"
                data-callback="handleGoogleCallback" data-auto_prompt="false"></div>
              <div class="g_id_signin" data-type="standard"></div>
              <!-- <a href="#" class="auth__icon"><i class="fa-brands fa-google-plus-g"></i></a>
                            <a href="#" class="auth__icon"><i class="fa-brands fa-facebook-f"></i></a>
                            <a href="#" class="auth__icon"><i class="fa-brands fa-github"></i></a>
                            <a href="#" class="auth__icon"><i class="fa-brands fa-linkedin-in"></i></a> -->
            </div>
            <p class="fs-12 m-0">
              Bằng việc đăng kí, bạn đã đồng ý với EZShope về <br />
              <a href="" class="register__register">Đều khoảng dịch vụ</a> &
              <a href="" class="register__register">Chính sách bảo mật</a>
            </p>
            <p class="mb-0">
              Bạn đã có tài khoản EZShop?
              <router-link to="/dang-nhap" class="text-color">Đăng nhập</router-link>
            </p>
          </form>
        </div>
      </div>
      <div class="col-lg-7 d-none d-lg-inline">
        <div class="d-flex align-items-center justify-content-center">
          <img src="/imgs/bg.svg" alt="" class="img-fluid auth__img" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import api from "@/configs/api";

import message from "@/utils/messageState";
import loading__loader_circle from "@/components/loading/loading__loader-circle.vue";
import { set_user } from "@/utils/authState";
/*
    SUPPORT
*/
const siteKey = import.meta.env.VITE_RECAPTCHA_SITE_KEY;
const is_loading = ref(false);
const showPassword = ref(false);
const errors = ref({});

/*
    REGISTER
*/
const user = ref({
  user_name: "",
  email: "",
  password: "",
  password_confirmation: "",
});

const validate = () => {
  errors.value = {};

  if (!user.value.user_name.trim()) {
    errors.value.user_name = "Tên không được để trống.";
  } else if (user.value.user_name.trim().length < 6) {
    errors.value.user_name = "Tên người dùng tối thiểu 6 ký tự.";
  }

  if (!user.value.email) {
    errors.value.email = "Email không được để trống.";
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(user.value.email)) {
    errors.value.email = "Email không hợp lệ.";
  }

  if (!user.value.password) {
    errors.value.password = "Mật khẩu không được để trống.";
  } else if (user.value.password.trim().length < 8) {
    errors.value.password = "Mật khẩu phải có ít nhất 8 ký tự.";
  } else if (
    !/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/.test(user.value.password)
  ) {
    errors.value.password =
      "Mật khẩu phải có ít nhất 1 chữ hoa, 1 chữ thường và 1 ký tự đặc biệt.";
  }
  if (!user.value.password_confirmation) {
    errors.value.password_confirmation =
      "Mật khẩu xác nhận không được để trống.";
  } else if (user.value.password !== user.value.password_confirmation) {
    errors.value.password_confirmation = "Mật khẩu xác nhận không khớp.";
  }
  if (Object.keys(errors.value).length > 0) {
    return false;
  }
  return true;
};
// const register = async () => {
//     if (!validate()) return

//     try {
//         const token = await grecaptcha.execute(siteKey, { action: 'register' })

//         is_loading.value = true
//         const res = await api.post('/register-verify', {...user.value, recaptcha_token: token})
//         if (res.status == 201) {
//             message.emit('show-message', {
//                 title: 'Thông báo',
//                 text: res.data.message_success,
//                 type: 'success'
//             })
//             Object.assign(user.value = { user_name: '', email: '', password: '', password_confirmation: '' })
//             showPassword.value = false
//         }
//     } catch (error) {
//         if (error.response && error.response.status === 422) {
//             errors.value = error.response.data.errors
//         } else {
//             console.warn('Lỗi validation:', error)
//         }
//     } finally {
//         is_loading.value = false
//     }
// }

const register = async () => {
  if (!validate()) return;

  is_loading.value = true;

  try {
    const res = await api.post("/register", {
      ...user.value
    });

    if (res.status === 201) {
      message.emit("show-message", {
        title: "Thành công",
        text: res.data.message,
        type: "success",
      });

      // reset form
      Object.assign(
        (user.value = {
          user_name: "",
          email: "",
          password: "",
          password_confirmation: "",
        })
      );
    } else {
      message.emit("show-message", {
        title: "Lỗi",
        text: res.data.error,
        type: "error",
      });
    }
  } catch (err) {
    console.error("Lỗi đăng ký:", err);

  } finally {
    is_loading.value = false;
  }
};

// Google
const googleClientId = import.meta.env.VITE_GOOGLE_CLIENT_ID;

window.handleGoogleCallback = async (response) => {
  const id_token = response.credential;
  try {
    const res = await api.post("http://localhost:8000/api/auth/google-login", {
      id_token,
    });

    const data = res.data;

    message.emit("show-message", {
      title: "Thông báo",
      text: data.message,
      type: "success",
    });

    set_user(data.user_name);
    localStorage.setItem("token", data.token);
    localStorage.setItem("user_name", data.user_name);
    localStorage.setItem("role", data.role);

    router.push("/");
  } catch (err) {
    console.error(err);
    alert("Đăng nhập Google thất bại");
  }
};

onMounted(() => {
  if (window.google && window.google.accounts && window.google.accounts.id) {
    window.google.accounts.id.initialize({
      client_id: googleClientId,
      callback: window.handleGoogleCallback,
      auto_select: false,
      cancel_on_tap_outside: false,
    });
    window.google.accounts.id.renderButton(
      document.querySelector(".g_id_signin"),
      { theme: "outline", size: "large" }
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
