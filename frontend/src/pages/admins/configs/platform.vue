<template>
    <main class="admin__content">
        <div
            class="p-3 bg-white border-bottom text-grey fs-14 fw-medium d-flex align-items-center justify-content-between">
            <p class="m-0">Thông Tin Cấu Hình Sàn</p>
        </div>

        <div class="p-5">
            <div v-if="loading.fetch" style="height: 60vh;" class="d-flex align-items-center justify-content-center">
                <loading__dot />
            </div>
            <form v-else @submit.prevent="saveConfig">
                <div class="row g-4">
                    <!-- Logo Header -->
                    <div class="col">
                        <h5>Logo Header</h5>
                        <label for="logoHeader" class="img-config mt-3 d-block cursor-pointer">
                            <img :src="logoHeaderPreview" class="w-100 h-100 object-fit-cover" />
                        </label>
                        <input id="logoHeader" type="file" accept="image/*" class="d-none"
                            @change="onFileChange($event, 'header')" />
                    </div>

                    <!-- Logo Footer -->
                    <div class="col">
                        <h5>Logo Footer</h5>
                        <label for="logoFooter" class="img-config mt-3 d-block cursor-pointer">
                            <img :src="logoFooterPreview" class="w-100 h-100 object-fit-cover" />
                        </label>
                        <input id="logoFooter" type="file" accept="image/*" class="d-none"
                            @change="onFileChange($event, 'footer')" />
                    </div>

                    <!-- Màu Chủ Đạo -->
                    <div class="col">
                        <h5>Màu Chủ Đạo</h5>
                        <label for="mainColor" class="img-config mt-3 d-flex align-items-center justify-content-center"
                            :style="{ backgroundColor: mainColor, color: textColor(mainColor) }">
                            <span class="fs-16 fw-bold p-2">{{ mainColor }}</span>
                        </label>
                        <input id="mainColor" type="color" class="d-none" v-model="mainColor" />
                    </div>
                </div>

                <!-- Các input còn lại -->
                <div class="row g-4 mt-5">
                    <div class="col-12 col-lg-6">
                        <h5>Tiêu Đề Trang</h5>
                        <input type="text" class="form-control mt-3" v-model="form.title" />
                    </div>
                    <div class="col-12 col-lg-6">
                        <h5>Mô Tả Ngắn</h5>
                        <input type="text" class="form-control mt-3" v-model="form.description" />
                    </div>
                    <div class="col-12 col-lg-6">
                        <h5>Đường Dây Nóng</h5>
                        <input type="text" class="form-control mt-3" v-model="form.phone" />
                    </div>
                    <div class="col-12 col-lg-6">
                        <h5>Email</h5>
                        <input type="text" class="form-control mt-3" v-model="form.email" />
                    </div>
                    <div class="col-12 col-lg-6">
                        <h5>Địa Chỉ</h5>
                        <input type="text" class="form-control mt-3" v-model="form.address" />
                    </div>
                    <div class="col-12 col-lg-6">
                        <h5>Facebook</h5>
                        <input type="text" class="form-control mt-3" v-model="form.facebook" />
                    </div>
                    <div class="col-12 col-lg-6">
                        <h5>Youtube</h5>
                        <input type="text" class="form-control mt-3" v-model="form.youtube" />
                    </div>
                    <div class="col-12 col-lg-6">
                        <h5>TikTok</h5>
                        <input type="text" class="form-control mt-3" v-model="form.tiktok" />
                    </div>
                    <div class="col-12 col-lg-6">
                        <h5>Twitter</h5>
                        <input type="text" class="form-control mt-3" v-model="form.twitter" />
                    </div>
                    <div class="col-12 col-lg-6">
                        <h5>Instagram</h5>
                        <input type="text" class="form-control mt-3" v-model="form.instagram" />
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 mt-5">
                    <button v-if="loading.edit" form="form-edit-cate" class="main-btn py-2 px-4 fs-14" type="submit">
                        <loading__loader_circle size="15px" color="#fff" border="2px" />
                    </button>
                    <input v-else type="submit" class="main-btn px-3" style="height: 38px" value="Lưu Chỉnh Sửa" />
                    <!-- Nút đặt lại -->
                    <button type="button" class="sub-btn px-3" style="height: 38px" @click="resetForm">
                        Đặt Lại
                    </button>

                </div>
            </form>
        </div>
    </main>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/configs/api'
import message from '@/utils/messageState';
import loading__dot from '@/components/loading/loading__dot.vue';
import loading__loader_circle from '@/components/loading/loading__loader-circle.vue';
import { useConfig } from '@/composables/useConfig';

const loading = ref({
    fetch: false,
    edit: false
})

// Dữ liệu reactive
const form = ref({})
const mainColor = ref('#000000')

// Ảnh và file ảnh
const logoHeaderPreview = ref('')
const logoFooterPreview = ref('')
const logoHeaderFile = ref(null)
const logoFooterFile = ref(null)

// Lưu cấu hình ban đầu để reset
const originalConfig = ref({})

// Lấy dữ liệu config khi mounted
const { refreshConfig } = useConfig()

onMounted(async () => {
    try {
        loading.value.fetch = true
        const res = await api.get('/config')
        const config = res.data.config

        originalConfig.value = { ...config }

        form.value = {
            title: config.title || '',
            description: config.description || '',
            phone: config.phone || '',
            email: config.email || '',
            address: config.address || '',
            facebook: config.facebook || '',
            youtube: config.youtube || '',
            tiktok: config.tiktok || '',
            twitter: config.twitter || '',
            instagram: config.instagram || ''
        }

        logoHeaderPreview.value = config.logo_header ? `/imgs/${config.logo_header}` : ''
        logoFooterPreview.value = config.logo_footer ? `/imgs/${config.logo_footer}` : ''
        mainColor.value = config.main_color || '#000000'
    } catch (error) {
        console.error('Lỗi khi lấy cấu hình:', error)
    } finally {
        loading.value.fetch = false
    }
})

// Xử lý khi chọn ảnh
function onFileChange(event, type) {
    const file = event.target.files[0]
    if (file) {
        const reader = new FileReader()
        reader.onload = (e) => {
            if (type === 'header') {
                logoHeaderPreview.value = e.target.result // base64 preview
                logoHeaderFile.value = file
            } else if (type === 'footer') {
                logoFooterPreview.value = e.target.result
                logoFooterFile.value = file
            }
        }
        reader.readAsDataURL(file)
    }
}


// Tính màu chữ tương phản với nền
function textColor(bgColor) {
    const r = parseInt(bgColor.substr(1, 2), 16)
    const g = parseInt(bgColor.substr(3, 2), 16)
    const b = parseInt(bgColor.substr(5, 2), 16)
    const brightness = (r * 299 + g * 587 + b * 114) / 1000
    return brightness > 128 ? '#000' : '#fff'
}

// Reset form về cấu hình gốc
function resetForm() {
    const config = originalConfig.value
    form.value = {
        title: config.title || '',
        description: config.description || '',
        phone: config.phone || '',
        email: config.email || '',
        address: config.address || '',
        facebook: config.facebook || '',
        youtube: config.youtube || '',
        tiktok: config.tiktok || '',
        twitter: config.twitter || '',
        instagram: config.instagram || ''
    }

    logoHeaderPreview.value = config.logo_header ? `/imgs/${config.logo_header}` : ''
    logoFooterPreview.value = config.logo_footer ? `/imgs/${config.logo_footer}` : ''

    logoHeaderFile.value = null
    logoFooterFile.value = null
    mainColor.value = config.main_color || '#000000'
}

async function saveConfig(event) {
    event.preventDefault()

    if (!validateForm()) return

    try {
        loading.value.edit = true;

        const formData = new FormData()

        // Duyệt qua tất cả các field trong form
        for (const key in form.value) {
            const inputValue = form.value[key]

            // Nếu là null, undefined, hoặc rỗng sau khi trim thì dùng giá trị gốc
            const finalValue = typeof inputValue === 'string'
                ? inputValue.trim()
                : inputValue

            formData.append(
                key,
                finalValue !== '' && finalValue !== null && finalValue !== undefined
                    ? finalValue
                    : (originalConfig.value[key] || '')
            )
        }


        // main_color cũng xử lý tương tự
        formData.append(
            'main_color',
            mainColor.value?.trim() !== '' ? mainColor.value : (originalConfig.value.main_color || '')
        )

        // Nếu có thay ảnh mới thì gửi file mới, còn không bỏ qua
        if (logoHeaderFile.value) {
            formData.append('logo_header', logoHeaderFile.value, logoHeaderFile.value.name)
        }
        if (logoFooterFile.value) {
            formData.append('logo_footer', logoFooterFile.value, logoFooterFile.value.name)
        }


        for (let [key, value] of formData.entries()) {
            console.log(`${key}: ${value}`);
        }


        await api.post('/config', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        })

        // Refresh config cache sau khi update thành công
        await refreshConfig()

        message.emit('show-message', {
            title: 'Thành công',
            text: 'Cập nhật cấu hình thành công',
            type: 'success',
        });
    } catch (error) {
        console.error('Lỗi khi lưu cấu hình:', error)
        message.emit('show-message', {
            title: 'Thất bại',
            text: 'Lỗi cập nhật cấu hình',
            type: 'error',
        });
    } finally {
        loading.value.edit = false
    }
}

function validateForm() {
    const errors = []

    // title: required, max 255
    if (!form.value.title || form.value.title.trim() === '') {
        errors.push('Tiêu đề không được để trống')
    } else if (form.value.title.length > 255) {
        errors.push('Tiêu đề không được vượt quá 255 ký tự')
    }

    // description: nullable, max 1000
    if (form.value.description && form.value.description.length > 1000) {
        errors.push('Mô tả không được vượt quá 1000 ký tự')
    }

    // phone: nullable, max 20, regex
    const phoneRegex = /^(\+?\d{1,3}[- ]?)?\d{10,15}$/
    if (form.value.phone) {
        if (form.value.phone.length > 20) {
            errors.push('Số điện thoại không được vượt quá 20 ký tự')
        } else if (!phoneRegex.test(form.value.phone)) {
            errors.push('Số điện thoại không hợp lệ')
        }
    }

    // email: nullable, email format, max 255
    if (form.value.email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
        if (form.value.email.length > 255) {
            errors.push('Email không được vượt quá 255 ký tự')
        } else if (!emailRegex.test(form.value.email)) {
            errors.push('Email không hợp lệ')
        }
    }

    // address: nullable, max 255
    if (form.value.address && form.value.address.length > 255) {
        errors.push('Địa chỉ không được vượt quá 255 ký tự')
    }

    // Các trường URL: nullable, max 255, phải là URL hợp lệ nếu có
    const urlFields = ['facebook', 'youtube', 'tiktok', 'twitter', 'instagram']
    const urlRegex = /^(https?:\/\/)?([\w.-]+)\.[a-z]{2,}.*$/i

    for (const field of urlFields) {
        const value = form.value[field]
        if (value) {
            if (value.length > 255) {
                errors.push(`URL ${field} không được vượt quá 255 ký tự`)
            } else if (!urlRegex.test(value)) {
                errors.push(`URL ${field} không hợp lệ`)
            }
        }
    }

    // main_color: nullable, regex mã màu hex
    const hexColorRegex = /^#[0-9A-Fa-f]{6}$/
    if (mainColor.value && !hexColorRegex.test(mainColor.value)) {
        errors.push('Màu chính phải là mã màu hex hợp lệ (ví dụ: #FFFFFF)')
    }

    // logo_header / logo_footer: nullable, image types, max 2MB
    const imageFields = [
        { file: logoHeaderFile.value, name: 'Logo header' },
        { file: logoFooterFile.value, name: 'Logo footer' }
    ]
    const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif']

    for (const { file, name } of imageFields) {
        if (file) {
            if (!allowedTypes.includes(file.type)) {
                errors.push(`${name} phải có định dạng jpeg, png, jpg hoặc gif`)
            } else if (file.size > 2 * 1024 * 1024) {
                errors.push(`${name} không được vượt quá 2MB`)
            }
        }
    }

    // Nếu có lỗi thì cảnh báo và trả về false
    if (errors.length > 0) {
        message.emit('show-message', {
            title: 'Lỗi xác thực',
            text: errors.join(' || '), // dùng <br> để hiển thị mỗi lỗi 1 dòng
            type: 'error',
            html: true // nếu component hỗ trợ HTML
        })
        return false
    }


    return true
}


</script>
