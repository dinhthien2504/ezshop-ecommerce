<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import api from '@/configs/api'
import message from '@/utils/messageState'
import loading__dot from '@/components/loading/loading__dot.vue'
import loading__loader_circle from '@/components/loading/loading__loader-circle.vue'
import { formatDate } from '@/utils/formatDate'

const loading = ref({
    fetch: false,
    save: false,
    resend: false,
    verify: false
})
const oldPassword = ref('')
const newPassword = ref('')
const confirmPassword = ref('')


const resetForm = () => {
    oldPassword.value = ''
    newPassword.value = ''
    confirmPassword.value = ''
}

const changePassword = async () => {
    // Validate form inputs
    if (await validateForm()) return

    try {
        const res = await api.post('/user/change-password', {
            old_password: oldPassword.value,
            new_password: newPassword.value,
        })
        if (res.data.success) {
            message.emit('show-message', {
                title: 'Thành công',
                text: res.data.message,
                type: 'success',
            })
        } else {
            message.emit('show-message', {
                title: 'Lỗi',
                text: res.data.message,
                type: 'error',
            })
        }
        resetForm()
    } catch (err) {
        message.emit('show-message', {
            title: 'Lỗi',
            text: err.response?.data?.message || 'Không thể đổi mật khẩu.',
            type: 'error',
        })
    }
}

const validateForm = async () => {
    const errors = []

    if (!oldPassword.value) {
        errors.push('Mật khẩu cũ không được để trống.')
    }

    // regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/
    if (!newPassword.value) {
        errors.push('Mật khẩu mới không được để trống.')
    } else if (newPassword.value.length < 8) {
        errors.push('Mật khẩu mới phải có ít nhất 8 ký tự.')
    } else if (!/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/.test(newPassword.value)) {
        errors.push('Mật khẩu mới phải chứa chữ hoa, chữ thường, số và ký tự đặc biệt.')
    }

    if (!confirmPassword.value) {
        errors.push('Xác nhận mật khẩu không được để trống.')
    } else if (confirmPassword.value !== newPassword.value) {
        errors.push('Xác nhận mật khẩu không khớp với mật khẩu mới.')
    }

    if (errors.length > 0) {
        message.emit('show-message', {
            title: 'Lỗi',
            text: errors.join(' || '),
            type: 'error',
        })
        return true
    }

    return false
}


// Verify email
const isModalVerify = ref(false)
const showModalVerify = async () => {
    if (await validateForm()) return
    try {
        loading.value.save = true
        const res = await api.post('/user/validate-password', {
            new_password: newPassword.value,
            old_password: oldPassword.value,
        })
        const result = res.data
        if (!result.success) {
            message.emit('show-message', {
                title: 'Lỗi',
                text: result.message,
                type: 'error',
            })
            return
        }
        if (!isVerifyEmail.value) {
            message.emit('show-message', {
                title: 'Lưu ý',
                text: 'Vui lòng xác thực email trước',
                type: 'warning',
            })
            return
        }

        await sendVerifyCode()
    } catch (err) {
        message.emit('show-message', {
            title: 'Lỗi',
            text: err.response?.data?.message || 'Mật khẩu cũ không đúng.',
            type: 'error',
        })
    } finally {
        loading.value.save = false
        isModalVerify.value = true
        startCountdown()
    }
}


const closeVerifyModal = () => {
    isModalVerify.value = false
    if (interval.value) {
        clearInterval(interval.value)
        interval.value = null
    }
    remainingTime.value = 300
}

const resendVerifyCode = async () => {
    try {
        loading.value.resend = true
        await sendVerifyCode()
        startCountdown()
    } catch (e) {
        console.log(e)
    } finally {
        loading.value.resend = false
    }
}


const sendVerifyCode = async () => {
    try {
        const res = await api.post('/email/send-otp', {
            type: 'change_password',
        })
        message.emit('show-message', {
            title: 'Thành công',
            text: res.data.message,
            type: 'success',
        })
    } catch (err) {
        message.emit('show-message', {
            title: 'Lỗi',
            text: err.response?.data?.message || 'Không thể gửi mã xác nhận.',
            type: 'error',
        })
    }
}

const otpCode = ref('')
const verifyCode = async () => {
    try {
        loading.value.verify = true
        const res = await api.post('/email/verify-otp', {
            otp: otpCode.value // lấy từ input người dùng
        })
        isModalVerify.value = false
        otpCode.value = ''
        clearInterval(interval.value)
        interval.value = null
        remainingTime.value = 300
        changePassword()
    } catch (err) {
        message.emit('show-message', {
            title: 'Lỗi',
            text: 'Mã không đúng hoặc đã hết hạn.',
            type: 'error',
        })
    } finally {
        loading.value.verify = false
    }
}

const interval = ref(null)
const remainingTime = ref(300) // 5 phút = 300 giây
const startCountdown = () => {
    // Luôn reset countdown
    if (interval.value) {
        clearInterval(interval.value)
        interval.value = null
    }

    remainingTime.value = 300
    interval.value = setInterval(() => {
        remainingTime.value--
        if (remainingTime.value <= 0) {
            clearInterval(interval.value)
            interval.value = null
        }
    }, 1000)
}


const formattedTime = computed(() => {
    const minutes = Math.floor(remainingTime.value / 60).toString().padStart(2, '0')
    const seconds = (remainingTime.value % 60).toString().padStart(2, '0')
    return `${minutes}:${seconds}`
})

const isVerifyEmail = ref(false)
const user = ref({})
const getUser = async () => {
    try {
        loading.value.fetch = true
        const response = await api.get('/user-profile')
        user.value = response.data.user
        if (user.value.email_verified_at) {
            isVerifyEmail.value = true
        }
        console.log(isVerifyEmail.value)
    } catch (error) {
        message.emit('show-message', {
            title: 'Lỗi',
            text: 'Không thể tải thông tin người dùng.',
            type: 'error',
        })
    } finally {
        loading.value.fetch = false
    }
}
onMounted(async () => {
    await getUser()
})
</script>
<template>
    <div class="profile__right overflow-hidden">
        <div v-if="loading.fetch" class="d-flex align-items-center justify-content-center p-4 text-center flex-column"
            style="min-height: 500px;">
            <loading__dot />
            <p class="mt-2">Đang tải thông tin cá nhân...</p>
        </div>
        <div v-else class="border py-3 px-5 bg-white shadow-sm mx-1 mt-lg-3 mb-2" style="min-height: 70vh;">
            <div class="mb-lg-4 border-bottom pb-3">
                <h5>Đổi Mật Khẩu</h5>
                <p class="text-muted">Để bảo mật tài khoản, vui lòng không chia sẻ mật khẩu cho người khác</p>
            </div>
            <form class="form">
                <div class="row mb-3">
                    <div class="row mb-2 align-items-center">
                        <label class="col-12 col-lg-3 col-form-label fw-medium">Mật khẩu cũ</label>
                        <div class="col col-lg-4">
                            <input v-model="oldPassword" type="password" class="form-control"
                                placeholder="Nhập mật khẩu cũ..." required>
                        </div>
                    </div>
                    <div class="row mb-2 align-items-center">
                        <label class="col-12 col-lg-3 col-form-label fw-medium">Mật khẩu mới</label>
                        <div class="col col-lg-4">
                            <input v-model="newPassword" type="password" class="form-control"
                                placeholder="Nhập mật khẩu mới..." required>
                        </div>
                    </div>
                    <div class="row mb-2 align-items-center">
                        <label class="col-12 col-lg-3 col-form-label fw-medium">Xác nhận mật khẩu</label>
                        <div class="col col-lg-4">
                            <input v-model="confirmPassword" type="password" class="form-control"
                                placeholder="Nhập mật khẩu mới..." required>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12 d-flex justify-content-start">
                        <button v-if="loading.save" form="form-edit-cate"
                            class="btn btn-danger fw-semibold radius-2 me-2" type="submit"
                            style="width: 136px; height: 41px;">
                            <loading__loader_circle size="15px" color="#fff" border="2px" class="mx-auto" />
                        </button>
                        <button v-else type="button" @click="showModalVerify"
                            class="btn btn-danger px-3 py-2 fw-semibold radius-2 me-2">Lưu thay
                            đổi</button>
                        <button @click="resetForm" type="button"
                            class="btn btn-outline-danger px-3 py-2 fw-semibold radius-2">Đặt lại</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Modal verify email -->
        <div v-if="isModalVerify"
            class="modal-background container-fluid p-0 d-flex justify-content-center align-items-center">
            <div class="modal-edit-address modal-ezeshop bg-white shadow-sm px-4 pt-4 pb-2">
                <div class="modal-ezeshop__top d-flex justify-content-between align-items-center">
                    <div class="modal-ezeshop__name fs-18 fw-500">Xác Nhận Đổi Mật Khẩu</div>
                    <div @click="closeVerifyModal" class="modal-ezeshop__cancel cursor-pointer"><i
                            class="fa-solid fa-xmark fs-18"></i>
                    </div>
                </div>
                <!-- Content -->
                <div class="modal-ezeshop__main w-100 p-1">
                    <form id="form-edit-cate" enctype="multipart/form-data">
                        <div class="mb-3">
                            <!-- <label for="name" class="form-label fs-14"><span
                                    class="text-color">*</span></label> -->
                            <input v-model="otpCode" placeholder="Nhập mã xác nhận..." type="text"
                                class="form-control form-control__input">
                        </div>
                        <div class="mb-3 text-center">
                            <p class="fs-16">Mã xác nhận đang được gửi đến email của bạn.</p>
                            <p class="fs-16">Mã sẽ hết hạn sau {{ formattedTime }}</p>
                        </div>
                    </form>
                </div>
                <div class="modal-ezeshop__bottom d-flex justify-content-end w-100 align-items-center border-top">
                    <div class="modal-ezeshop__cancel me-4"><button @click="closeVerifyModal"
                            class="white-btn py-2 px-4 fs-14">Huỷ</button></div>
                    <div class="modal-ezeshop__cancel me-4"><button v-if="loading.resend" @click="resendVerifyCode"
                            class="btn btn-outline-danger py-2 px-4 fs-14"
                            style="border-radius: 3px; width: 119px; height: 38px;">
                            <loading__loader_circle size="15px" color="red" border="2px" class="mx-auto" />
                        </button>
                        <button v-else @click="resendVerifyCode" class="btn btn-outline-danger py-2 px-4 fs-14"
                            style="border-radius: 3px;">Gửi lại
                            mã</button>
                    </div>
                    <div class="modal-ezeshop__save"><button form="form-edit-cate" class="main-btn py-2 px-4 fs-14"
                            type="button" @click="verifyCode">
                            <loading__loader_circle size="15px" color="#fff" border="2px" v-if="loading.verify"
                                class="mx-4 my-1" />
                            <div v-else>Xác nhận</div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<style scoped>
@media (min-width: 992px) {
    .border-lg-start {
        border-left: 1px solid #dee2e6 !important;
    }
}
</style>
