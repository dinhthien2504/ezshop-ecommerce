<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import api from '@/configs/api'
import message from '@/utils/messageState'
import loading__dot from '@/components/loading/loading__dot.vue'
import loading__loader_circle from '@/components/loading/loading__loader-circle.vue'
import { formatDate } from '@/utils/formatDate'
import { set_user } from '@/utils/authState'


// Loading states
const loading = ref({
    fetch: false,
    save: false,
    verify: false,
    resend: false,
    savePassword: false  // Thêm loading riêng cho password
})
// User profile data
const user = ref({
    user_name: '',
    email: '',
    phone: '',
    avatar: '',
})
const originUser = ref({})
const avatar = ref('/imgs/avtDefault.png')
const avatarFile = ref(null)
const fileInputRef = ref(null)
const isVerifyEmail = ref(false)

// Password change data
const oldPassword = ref('')
const newPassword = ref('')
const confirmPassword = ref('')

// Email verification
const isModalVerify = ref(false) // for email verification
const isModalVerifyPassword = ref(false) // for password change verification
const otpCode = ref('')
const interval = ref(null)
const remainingTime = ref(300) // 5 phút = 300 giây

const getUser = async () => {
    try {
        loading.value.fetch = true
        const response = await api.get('/user-profile')
        user.value = response.data.user
        originUser.value = { ...user.value }
        if (user.value.avatar) {
            avatar.value = `/imgs/users/${user.value.avatar}`
        }
        // Kiểm tra email đã xác thực
        if (user.value.email_verified_at) {
            isVerifyEmail.value = true
        }
        // console.log('User data:', user.value)
        // console.log('Origin user data:', originUser.value)
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

const role = ref(null)
const permissions = ref([])
const getRoleUser = async () => {
    try {
        const response = await api.get('/role/user')
        role.value = response.data.role
        permissions.value = response.data.permissions
    } catch (error) {
        console.error('Error fetching user role:', error)
        return null
    }
}

const handleFileChange = (event) => {
    const file = event.target.files[0]
    if (!file) return

    // Kiểm tra dung lượng ảnh (2MB = 2 * 1024 * 1024 bytes)
    const maxSize = 2 * 1024 * 1024
    if (file.size > maxSize) {
        message.emit('show-message', {
            title: 'Lỗi',
            text: 'Ảnh không được vượt quá 2MB.',
            type: 'error',
        })
        // Reset input
        event.target.value = ''
        return
    }

    const reader = new FileReader()
    reader.onload = (e) => {
        avatar.value = e.target.result
        avatarFile.value = file
    }
    reader.readAsDataURL(file)
}


const triggerFileInput = () => {
    fileInputRef.value?.click()
}

const resetForm = () => {
    user.value = { ...originUser.value }
    avatar.value = user.value.avatar ? `/imgs/users/${user.value.avatar}` : '/imgs/avtDefault.png'
    avatarFile.value = null
    if (fileInputRef.value) {
        fileInputRef.value.value = ''
    }
}

const saveChanges = async () => {
    if (await validateForm()) return
    try {
        loading.value.save = true
        const formData = new FormData()
        if (user.value.email !== null) {
            formData.append('email', user.value.email)
        }
        if (user.value.phone !== null) {
            formData.append('phone', user.value.phone)
        }
        if (avatarFile.value) {
            formData.append('avatar', avatarFile.value)
        }

        const response = await api.post('/user-profile', formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        })

        if (response.data.success) {
            message.emit('show-message', {
                title: 'Thành công',
                text: 'Thông tin hồ sơ đã được cập nhật thành công.',
                type: 'success',
            })
            await getUser()
        } else {
            message.emit('show-message', {
                title: 'Lỗi',
                text: response.data.message || 'Không thể cập nhật thông tin hồ sơ.',
                type: 'error',
            })
        }

    } catch (error) {
        message.emit('show-message', {
            title: 'Lỗi',
            text: 'Đã xảy ra lỗi khi cập nhật thông tin hồ sơ.',
            type: 'error',
        })
    } finally {
        set_user(user.value.user_name, user.value.avatar, user.value.google_id);
        loading.value.save = false
    }
}

const validateForm = async () => {
    const errors = []

    // Validate phone (Việt Nam): 0xxxxxxxxx hoặc +84xxxxxxxxx
    if (user.value.phone) {
        const phoneRegex = /^(0|\+84)[0-9]{9}$/
        if (!phoneRegex.test(user.value.phone)) {
            errors.push('Số điện thoại không đúng định dạng.')
        }
    }

    if (!user.value.email) {
        errors.push('Email không được để trống.')
    } else {
        const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.(com|net|org|edu|gov|vn|info|co|io|ai)$/
        if (!emailRegex.test(user.value.email)) {
            errors.push('Email không đúng định dạng.')
        } else {
            try {
                const response = await api.get('/user-list')
                const users = response.data.users || []

                const emailExisted = users.find(u =>
                    u.email === user.value.email && u.id !== user.value.id
                )

                if (emailExisted) {
                    errors.push('Email đã được sử dụng bởi người dùng khác.')
                }
            } catch (err) {
                errors.push('Không thể kiểm tra trùng email.')
            }
        }
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
const showModalVerify = async () => {
    // Kiểm tra email rỗng
    if (!user.value.email) {
        message.emit('show-message', {
            title: 'Lỗi',
            text: 'Bạn cần nhập email trước khi xác thực.',
            type: 'error',
        })
        return
    }

    // Kiểm tra email đã bị thay đổi so với DB
    if (user.value.email !== originUser.value.email) {
        message.emit('show-message', {
            title: 'Lỗi',
            text: 'Bạn cần lưu lại email mới trước khi xác thực.',
            type: 'error',
        })
        return
    }

    // Mở modal và reset timer
    isModalVerify.value = true
    otpCode.value = ''  // Reset OTP code khi mở modal
    await sendVerifyCode()
    startCountdown()
}

const closeVerifyModal = () => {
    isModalVerify.value = false
    if (interval.value) {
        clearInterval(interval.value)
        interval.value = null
    }
    remainingTime.value = 300
    otpCode.value = ''  // Reset OTP code khi đóng modal
}

const closeVerifyPasswordModal = () => {
    isModalVerifyPassword.value = false
    if (interval.value) {
        clearInterval(interval.value)
        interval.value = null
    }
    remainingTime.value = 300
    otpCode.value = ''  // Reset OTP code khi đóng modal
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
            type: 'verify_email',
        })
        remainingTime.value = 300
        message.emit('show-message', {
            title: 'Thành công',
            text: res.data.message,
            type: 'success',
        })
    } catch (err) {
        message.emit('show-message', {
            title: 'Lỗi',
            text: err.response?.data?.message || 'Không thể gửi mã xác thực.',
            type: 'error',
        })
    }
}

const verifyCode = async () => {
    try {
        loading.value.verify = true
        const res = await api.post('/email/verify-otp', {
            otp: otpCode.value // lấy từ input người dùng
        })
        message.emit('show-message', {
            title: 'Thành công',
            text: res.data.message,
            type: 'success',
        })
        isModalVerify.value = false
        await getUser()
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

// Password reset function
const resetPasswordForm = () => {
    oldPassword.value = ''
    newPassword.value = ''
    confirmPassword.value = ''
}

const changePassword = async () => {
    // Validate form inputs
    if (await validatePasswordForm()) return

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
        resetPasswordForm()
    } catch (err) {
        message.emit('show-message', {
            title: 'Lỗi',
            text: err.response?.data?.message || 'Không thể đổi mật khẩu.',
            type: 'error',
        })
    }
}

const validatePasswordForm = async () => {
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

// Verify email for password change
const showModalVerifyPassword = async () => {
    if (await validatePasswordForm()) return
    try {
        loading.value.savePassword = true  // Sử dụng loading riêng
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

        await sendVerifyCodePassword()
    } catch (err) {
        message.emit('show-message', {
            title: 'Lỗi',
            text: err.response?.data?.message || 'Mật khẩu cũ không đúng.',
            type: 'error',
        })
    } finally {
        loading.value.savePassword = false  // Sử dụng loading riêng
        isModalVerifyPassword.value = true
        otpCode.value = ''  // Reset OTP code khi mở modal
        startCountdownPassword()
    }
}

const sendVerifyCodePassword = async () => {
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

const verifyCodePassword = async () => {
    try {
        loading.value.verify = true
        const res = await api.post('/email/verify-otp', {
            otp: otpCode.value // lấy từ input người dùng
        })
        isModalVerifyPassword.value = false
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

const startCountdownPassword = () => {
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

const resendVerifyCodePassword = async () => {
    try {
        loading.value.resend = true
        await sendVerifyCodePassword()
        startCountdownPassword()
    } catch (e) {
        console.log(e)
    } finally {
        loading.value.resend = false
    }
}

onMounted(async () => {
    await getUser()
    // Kiểm tra email đã xác thực
    if (user.value.email_verified_at) {
        isVerifyEmail.value = true
    }
    await getRoleUser()
})
</script>
<template>
    <div class="admin__content" style="background: 0;">
        <div v-if="loading.fetch" class="d-flex align-items-center justify-content-center p-4 text-center flex-column"
            style="min-height: 500px;">
            <loading__dot />
            <p class="mt-2">Đang tải thông tin cá nhân...</p>
        </div>
        <div v-else>
            <div class="shadow-sm py-4 px-5 bg-white mb-3">
                <div class="mb-lg-4 border-bottom pb-3">
                    <h5>Hồ Sơ Của Tôi</h5>
                    <p class="text-muted">Quản lý thông tin hồ sơ</p>
                </div>
                <form @submit.prevent="saveChanges" class="form">
                    <div class="row">
                        <div class="col mb-3">
                            <div class="row mb-2 align-items-center">
                                <label class="col-3 col-lg-3 col-form-label fw-medium" style="min-width: 170px;">Tên
                                    người
                                    dùng</label>
                                <div class="col">
                                    <span>{{ user.user_name }}</span>
                                </div>
                            </div>
                            <div class="row mb-2 align-items-center">
                                <label class="col-12 col-lg-3 col-form-label fw-medium">Số điện thoại</label>
                                <div class="col col-lg-6">
                                    <input v-model="user.phone" type="text" class="form-control"
                                        placeholder="Nhập số điện thoại">
                                </div>
                                <!-- <div class="col-3 text-color fw-bold fs-14" style="min-width: 90px; cursor: pointer;">
                                Xác thực
                            </div> -->
                            </div>
                            <div v-if="user.google_id" class="row mb-2 align-items-center">
                                <label class="col-3 col-lg-3 col-form-label fw-medium"
                                    style="min-width: 100px;">Email</label>
                                <div class="col">
                                    <span>{{ user.email }}</span>
                                </div>
                            </div>
                            <div v-else class="row mb-2 align-items-center">
                                <label class="col-12 col-lg-3 col-form-label fw-medium">Email</label>
                                <div class="col col-lg-6">
                                    <input v-model="user.email" type="text" class="form-control"
                                        placeholder="Nhập email">
                                </div>
                                <div @click="showModalVerify" v-if="!user.google_id && !user.email_verified_at"
                                    class="col-3 text-color fw-bold fs-14"
                                    style="min-width: 90px; cursor: pointer; text-decoration: underline;">
                                    Xác thực
                                </div>
                                <div v-else-if="user.email_verified_at || user.google_id"
                                    class="col-3 text-success fw-medium fs-14"
                                    style="min-width: 126px; cursor: pointer;">
                                    <i class="bi bi-check-circle-fill text-success"></i>
                                    Đã xác thực
                                </div>
                            </div>
                            <div class="row mb-2 align-items-center">
                                <label class="col-3 col-lg-3 col-form-label fw-medium" style="min-width: 170px;">Xếp
                                    hạng</label>
                                <div class="col">
                                    <span class="badge bg-danger radius-2">{{ user.rank ? user.rank : 'Chưa có xếp hạng'
                                        }}</span>
                                </div>
                            </div>
                            <div class="row mb-2 align-items-center">
                                <label class="col-3 col-lg-3 col-form-label fw-medium" style="min-width: 170px;">Ngày
                                    tham
                                    gia</label>
                                <div class="col">
                                    <span>{{ formatDate(user.created_at) }}</span>
                                </div>
                            </div>
                        </div>
                        <div
                            class="col-12 col-lg-4 d-flex justify-content-lg-center justify-content-center align-items-center flex-lg-column border-lg-start">
                            <img :src="avatar" alt=""
                                class="img-fluid rounded-circle mb-lg-3 shadow-sm object-fit-cover mx-3"
                                style="width: 150px; height: 150px;">
                            <input ref="fileInputRef" @change="handleFileChange" type="file" class="d-none"
                                id="avatarInput" accept="image/*">
                            <button type="button" @click="triggerFileInput"
                                class="white-btn px-2 py-1 fs-14 radius-2 mx-3">Chọn ảnh</button>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12 d-flex justify-content-start mt-3 mt-lg-0">
                            <button v-if="loading.save" form="form-edit-cate" class="main-btn fw-semibold radius-2 me-2"
                                type="submit" style="width: 136px; height: 41px;">
                                <loading__loader_circle size="15px" color="#fff" border="2px" class="mx-auto" />
                            </button>
                            <button v-else type="submit" class="main-btn px-3 py-2 fw-semibold radius-2 me-2">Lưu thay
                                đổi</button>
                            <button @click="resetForm" type="button"
                                class="white-btn px-3 py-2 fw-semibold radius-2">Đặt lại</button>
                        </div>
                    </div>
                </form>
                <!-- Modal verify email -->
                <div v-if="isModalVerify"
                    class="modal-background container-fluid p-0 d-flex justify-content-center align-items-center">
                    <div class="modal-edit-address modal-ezeshop bg-white shadow-sm px-4 pt-4 pb-2">
                        <div class="modal-ezeshop__top d-flex justify-content-between align-items-center">
                            <div class="modal-ezeshop__name fs-18 fw-500">Xác Thực Email</div>
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
                                    <input v-model="otpCode" placeholder="Nhập mã xác thực..." type="text"
                                        class="form-control form-control__input">
                                </div>
                                <div class="mb-3 text-center">
                                    <p class="fs-16">Mã xác thực đang được gửi đến email của bạn.</p>
                                    <p class="fs-16">Mã sẽ hết hạn sau {{ formattedTime }}</p>
                                </div>
                            </form>
                        </div>
                        <div
                            class="modal-ezeshop__bottom d-flex justify-content-end w-100 align-items-center border-top">
                            <div class="modal-ezeshop__cancel me-4"><button @click="closeVerifyModal"
                                    class="white-btn py-2 px-4 fs-14">Huỷ</button></div>
                            <div class="modal-ezeshop__cancel me-4">
                                <button v-if="loading.resend" @click="resendVerifyCode"
                                    class="btn btn-outline-danger py-2 px-4 fs-14"
                                    style="border-radius: 3px; width: 119px; height: 38px;">
                                    <loading__loader_circle size="15px" color="red" border="2px" class="mx-auto" />
                                </button>
                                <button v-else @click="resendVerifyCode" class="btn btn-outline-danger py-2 px-4 fs-14"
                                    style="border-radius: 3px;">Gửi lại
                                    mã</button>
                            </div>
                            <div class="modal-ezeshop__save"><button form="form-edit-cate"
                                    class="main-btn py-2 px-4 fs-14" type="button" @click="verifyCode">
                                    <loading__loader_circle size="15px" color="#fff" border="2px" v-if="loading.verify"
                                        class="mx-4 my-1" />
                                    <div v-else>Xác thực</div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-lg-6 mb-3">
                    <div class="shadow-sm py-4 px-5 bg-white">
                        <div class="mb-lg-4 border-bottom pb-3">
                            <h5>Đổi Mật Khẩu</h5>
                        </div>
                        <form class="form">
                            <div class="row mb-3">
                                <div class="row mb-2 align-items-center">
                                    <label class="col-12 col-lg-5 col-form-label fw-medium">Mật khẩu cũ</label>
                                    <div class="col col-lg-7">
                                        <input v-model="oldPassword" type="password" class="form-control"
                                            placeholder="Nhập mật khẩu cũ..." required>
                                    </div>
                                </div>
                                <div class="row mb-2 align-items-center">
                                    <label class="col-12 col-lg-5 col-form-label fw-medium">Mật khẩu mới</label>
                                    <div class="col col-lg-7">
                                        <input v-model="newPassword" type="password" class="form-control"
                                            placeholder="Nhập mật khẩu mới..." required>
                                    </div>
                                </div>
                                <div class="row mb-2 align-items-center">
                                    <label class="col-12 col-lg-5 col-form-label fw-medium">Xác nhận mật khẩu</label>
                                    <div class="col col-lg-7">
                                        <input v-model="confirmPassword" type="password" class="form-control"
                                            placeholder="Nhập lại mật khẩu mới..." required>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12 d-flex justify-content-start">
                                    <button v-if="loading.savePassword" class="main-btn fw-semibold radius-2 me-2"
                                        type="button" style="width: 136px; height: 41px;">
                                        <loading__loader_circle size="15px" color="#fff" border="2px" class="mx-auto" />
                                    </button>
                                    <button v-else type="button" @click="showModalVerifyPassword"
                                        class="main-btn px-3 py-2 fw-semibold radius-2 me-2">Đổi mật khẩu</button>
                                    <button @click="resetPasswordForm" type="button"
                                        class="white-btn px-3 py-2 fw-semibold radius-2">Đặt lại</button>
                                </div>
                            </div>
                        </form>
                        <!-- Modal verify email for password change -->
                        <div v-if="isModalVerifyPassword"
                            class="modal-background container-fluid p-0 d-flex justify-content-center align-items-center">
                            <div class="modal-edit-address modal-ezeshop bg-white shadow-sm px-4 pt-4 pb-2">
                                <div class="modal-ezeshop__top d-flex justify-content-between align-items-center">
                                    <div class="modal-ezeshop__name fs-18 fw-500">Xác Nhận Đổi Mật Khẩu</div>
                                    <div @click="closeVerifyPasswordModal" class="modal-ezeshop__cancel cursor-pointer">
                                        <i class="fa-solid fa-xmark fs-18"></i>
                                    </div>
                                </div>
                                <!-- Content -->
                                <div class="modal-ezeshop__main w-100 p-1">
                                    <form id="form-edit-cate" enctype="multipart/form-data">
                                        <div class="mb-3">
                                            <input v-model="otpCode" placeholder="Nhập mã xác nhận..." type="text"
                                                class="form-control form-control__input">
                                        </div>
                                        <div class="mb-3 text-center">
                                            <p class="fs-16">Mã xác nhận đang được gửi đến email của bạn.</p>
                                            <p class="fs-16">Mã sẽ hết hạn sau {{ formattedTime }}</p>
                                        </div>
                                    </form>
                                </div>
                                <div
                                    class="modal-ezeshop__bottom d-flex justify-content-end w-100 align-items-center border-top">
                                    <div class="modal-ezeshop__cancel me-4"><button @click="closeVerifyPasswordModal"
                                            class="white-btn py-2 px-4 fs-14">Huỷ</button></div>
                                    <div class="modal-ezeshop__cancel me-4"><button v-if="loading.resend"
                                            @click="resendVerifyCodePassword"
                                            class="btn btn-outline-danger py-2 px-4 fs-14"
                                            style="border-radius: 3px; width: 119px; height: 38px;">
                                            <loading__loader_circle size="15px" color="red" border="2px"
                                                class="mx-auto" />
                                        </button>
                                        <button v-else @click="resendVerifyCodePassword"
                                            class="btn btn-outline-danger py-2 px-4 fs-14"
                                            style="border-radius: 3px;">Gửi lại
                                            mã</button>
                                    </div>
                                    <div class="modal-ezeshop__save"><button form="form-edit-cate"
                                            class="main-btn py-2 px-4 fs-14" type="button" @click="verifyCodePassword">
                                            <loading__loader_circle size="15px" color="#fff" border="2px"
                                                v-if="loading.verify" class="mx-4 my-1" />
                                            <div v-else>Xác nhận</div>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6 mb-3">
                    <div class="shadow-sm py-4 px-5 bg-white">
                        <div class="mb-3 border-bottom pb-3 d-flex justify-content-between align-items-center">
                            <h5>Vai Trò Và Quyền Hạn</h5>
                            <span v-if="role" class="badge bg-success radius-2">{{ role.title }}</span>
                            <span v-else class="badge bg-secondary">Đang tải...</span>
                        </div>
                        <template v-if="permissions && permissions.length > 0">
                            <span v-for="permission in permissions" :key="permission.permission_id"
                                class="badge bg-danger me-2 mb-1 radius-2">{{ permission.description }}</span>
                        </template>
                        <p v-else-if="role && permissions.length === 0" class="text-muted">Không có quyền nào được phân.
                        </p>
                        <p v-else class="text-muted">Đang tải quyền...</p>
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
