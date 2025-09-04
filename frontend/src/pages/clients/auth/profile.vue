<script setup>
import { ref, onMounted, computed } from 'vue'
import api from '@/configs/api'
import message from '@/utils/messageState'
import loading__dot from '@/components/loading/loading__dot.vue'
import loading__loader_circle from '@/components/loading/loading__loader-circle.vue'
import { formatDate } from '@/utils/formatDate'
import { formatCurrency } from '@/utils/formatCurrency'
import { set_user } from '@/utils/authState'

const loading = ref({
    fetch: false,
    save: false,
    verify: false,
    resend: false
})
const user = ref({
    user_name: '',
    email: '',
    phone: '',
    avatar: '',
})
const originUser = ref({})
const avatar = ref('/imgs/user-default.jpg')
const avatarFile = ref(null)
const fileInputRef = ref(null)

const getUser = async () => {
    try {
        loading.value.fetch = true
        const response = await api.get('/user-profile')
        user.value = response.data.user
        originUser.value = { ...user.value }
        if (user.value.avatar) {
            avatar.value = `/imgs/users/${user.value.avatar}`
        }
    } catch (error) {
        console.log(error);
    } finally {
        loading.value.fetch = false
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
    avatar.value = user.value.avatar ? `/imgs/users/${user.value.avatar}` : '/imgs/user-default.jpg'
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
const isModalVerify = ref(false)
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

const otpCode = ref('')
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



onMounted(async () => {
    const token = localStorage.getItem('token')
    if (token) {
        await getUser()
    }
})
</script>
<template>
    <div class="profile__right overflow-hidden">
        <div v-if="loading.fetch" class="d-flex align-items-center justify-content-center p-4 text-center flex-column"
            style="min-height: 500px;">
            <loading__dot />
            <p class="mt-2">Đang tải thông tin cá nhân...</p>
        </div>
        <div v-else class="border py-3 px-5 bg-white shadow-sm mx-1 mt-lg-3 mb-2" style="min-height: 55vh;">
            <div class="mb-lg-4 border-bottom pb-3">
                <h5>Hồ Sơ Của Tôi</h5>
                <p class="text-muted">Quản lý thông tin hồ sơ</p>
            </div>
            <form @submit.prevent="saveChanges" class="form">
                <div class="row">
                    <div class="col mb-3">
                        <div class="row mb-2 align-items-center">
                            <label class="col-3 col-lg-3 col-form-label fw-medium" style="min-width: 170px;">Tên người
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
                                <input v-model="user.email" type="text" class="form-control" placeholder="Nhập email">
                            </div>
                            <div @click="showModalVerify" v-if="!user.google_id && !user.email_verified_at"
                                class="col-3 text-color fw-bold fs-14"
                                style="min-width: 90px; cursor: pointer; text-decoration: underline;">
                                Xác thực
                            </div>
                            <div v-else-if="user.email_verified_at || user.google_id"
                                class="col-3 text-success fw-medium fs-14" style="min-width: 126px; cursor: pointer;">
                                <i class="bi bi-check-circle-fill text-success"></i>
                                Đã xác thực
                            </div>
                        </div>
                        <!-- <div class="row mb-2 align-items-center">
                            <label class="col-3 col-lg-3 col-form-label fw-medium" style="min-width: 170px;">Xếp
                                hạng</label>
                            <div class="col">
                                <span class="badge bg-danger">{{ user.rank ? user.rank : 'Chưa có xếp hạng' }}</span>
                            </div>
                        </div> -->
                        <div class="row mb-2 align-items-center">
                            <label class="col-3 col-lg-3 col-form-label fw-medium" style="min-width: 170px;">Ngày tham
                                gia</label>
                            <div class="col">
                                <span>{{ formatDate(user.created_at) }}</span>
                            </div>
                        </div>
                    </div>
                    <div
                        class="col-12 col-lg-4 d-flex justify-content-lg-center justify-content-center align-items-center flex-lg-column border-lg-start">
                        <img :src="avatar" alt="Hình ảnh"
                            class="img-fluid rounded-circle mb-lg-3 shadow-sm object-fit-cover mx-3"
                            style="width: 150px; height: 150px;">
                        <input ref="fileInputRef" @change="handleFileChange" type="file" class="d-none" id="avatarInput"
                            accept="image/*">
                        <button type="button" @click="triggerFileInput"
                            class="white-btn fs-14 px-2 py-1 radius-2 mx-3">Chọn ảnh</button>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12 d-flex justify-content-start">
                        <button v-if="loading.save" form="form-edit-cate"
                            class="main-btn fw-semibold radius-2 me-2" type="submit"
                            style="width: 136px; height: 41px;">
                            <loading__loader_circle size="15px" color="#fff" border="2px" class="mx-auto" />
                        </button>
                        <button v-else type="submit" class="main-btn px-3 py-2 fw-semibold radius-2 me-2">Lưu thay
                            đổi</button>
                        <button @click="resetForm" type="button"
                            class="white-btn px-3 py-2 fw-semibold radius-2">Đặt lại</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Phần thông tin rank riêng biệt -->
        <div v-if="user.current_rank || user.next_rank" class="border py-3 px-5 bg-white shadow-sm mx-1 mt-3 mb-2">
            <div class="mb-lg-4 border-bottom pb-3">
                <h5>
                    <i class="bi bi-trophy-fill text-warning me-2"></i>
                    Xếp Hạng & Lợi Ích
                </h5>
                <p class="text-muted">Thông tin về xếp hạng và tiến độ của bạn</p>
            </div>
            
            <!-- Thanh tiến độ chung -->
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-medium">Tiến độ xếp hạng</span>
                    <span class="fw-bold text-primary">
                        {{ user.next_rank ? user.next_rank.progress_to_next : (user.current_rank ? user.current_rank.progress : 0) }}%
                    </span>
                </div>
                <div class="progress mb-2 radius-2" style="height: 12px;">
                    <div class="progress-bar radius-2" 
                         :class="user.current_rank ? 'bg-success' : 'bg-warning'"
                         :style="{ width: (user.next_rank ? user.next_rank.progress_to_next : (user.current_rank ? user.current_rank.progress : 0)) + '%' }"></div>
                </div>
                <div class="d-flex justify-content-between">
                    <small class="text-muted">{{ formatCurrency(user.total_spent) }} (Đã chi tiêu)</small>
                    <small class="text-muted">
                        {{ user.next_rank ? formatCurrency(user.next_rank.remaining_amount) + ' (Còn thiếu)' : (user.current_rank ? formatCurrency(user.current_rank.max_spent) + ' (Hoàn thành)' : '') }}
                    </small>
                </div>
            </div>
            
            <!-- Rank hiện tại -->
            <div v-if="user.current_rank" class="mb-4">
                <div class="card border-success" style="border-radius: 2px 2px 0px 0px;">
                    <div class="card-header bg-success text-white" style="border-radius: 2px 2px 0px 0px;">
                        <h6 class="mb-0 fw-bold">
                            <i class="bi bi-star-fill me-2"></i>
                            Xếp Hạng Hiện Tại
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <h5 class="text-success mb-2">{{ user.current_rank.name }}</h5>
                                <div class="fw-bold text-success fs-5">{{ formatCurrency(user.total_spent) }}</div>
                                <small class="text-muted">Tổng chi tiêu</small>
                            </div>
                            <div class="col-md-8">
                                <strong class="text-success d-block mb-2">Lợi ích đang được hưởng:</strong>
                                <div class="alert alert-success py-2 mb-0 radius-2" v-if="user.current_rank.benefits">
                                    <small>{{ user.current_rank.benefits }}</small>
                                </div>
                                <div class="alert alert-light py-2 mb-0" v-else>
                                    <small class="text-muted">Chưa có thông tin lợi ích</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rank tiếp theo -->
            <div v-if="user.next_rank" class="mb-4">
                <div class="card border-primary" style="border-radius: 2px 2px 0px 0px;">
                    <div class="card-header bg-primary text-white" style="border-radius: 2px 2px 0px 0px;">
                        <h6 class="mb-0 fw-bold">
                            <i class="bi bi-arrow-up-circle-fill me-2"></i>
                            {{ user.current_rank ? 'Xếp Hạng Tiếp Theo' : 'Xếp Hạng Đầu Tiên' }}
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <h5 class="text-primary mb-2">{{ user.next_rank.name }}</h5>
                                <div class="fw-bold text-primary fs-5">{{ formatCurrency(user.next_rank.remaining_amount) }}</div>
                                <small class="text-muted">Còn thiếu để {{ user.current_rank ? 'lên rank' : 'đạt rank đầu tiên' }}</small>
                            </div>
                            <div class="col-md-8">
                                <strong class="text-primary d-block mb-2">Lợi ích khi đạt được:</strong>
                                <div class="alert alert-primary py-2 mb-0 radius-2" v-if="user.next_rank.benefits">
                                    <small>{{ user.next_rank.benefits }}</small>
                                </div>
                                <div class="alert alert-light py-2 mb-0" v-else>
                                    <small class="text-muted">Chưa có thông tin lợi ích</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Thông báo khuyến khích nếu chưa có rank -->
            <div v-if="!user.current_rank && user.next_rank" class="text-center">
                <div class="alert alert-warning py-3 radius-2">
                    <i class="bi bi-exclamation-circle-fill me-2"></i>
                    <strong>Hãy mua sắm để đạt xếp hạng đầu tiên và nhận được nhiều ưu đãi!</strong>
                </div>
            </div>
        </div>

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
                <div class="modal-ezeshop__bottom d-flex justify-content-end w-100 align-items-center border-top">
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
                    <div class="modal-ezeshop__save"><button form="form-edit-cate" class="main-btn py-2 px-4 fs-14"
                            type="button" @click="verifyCode">
                            <loading__loader_circle size="15px" color="#fff" border="2px" v-if="loading.verify"
                                class="mx-4 my-1" />
                            <div v-else>Xác thực</div>
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
