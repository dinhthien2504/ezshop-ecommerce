<template>
    <div class="__main ">
        <div class="__title mt-3 bg-white px-4 d-flex align-items-center border-bottom" style="height: 50px;"><span class="fs-14 fw-semibold text-grey">PROFILE</span></div>
        
        <div class="__content bg-white px-4 pt-2 pb-5 d-flex align-items-center border-bottom shadow-sm">
            <div class="__profile w-100">
                <div class="__head d-flex justify-content-between">
                    <div class="fs-16 fw-semibold">Thông tin cơ bản</div>
                    <div class="__task d-flex flex-wrap">
                        <button class="__viewshop fs-12 border px-2 radius-3 me-2 bg-white">Xem shop của tôi</button>
                        <button class="__editshop fs-12 border bg-white px-2 radius-3" @click="open_modal">Chỉnh sửa</button>
                    </div>
                </div>
                <div class="__body ps-5 mt-3">
                    <div class="__shopname fs-14 fw-500"><span>Tên shop:</span> {{ profile.shop_name }}</div>
                    <div class="__shoplogo d-flex align-items-center flex-wrap fs-14 mt-3">
                        <span class="fw-500 me-4">Logo shop:</span>
                        <div class="__img border me-4" style="width: 60px; height: 60px; border-radius: 50%;">
                            <!-- <img :src="profile.image ? `/imgs/shops/${profile.image}` : `/imgs/shops/default-logo.png`" alt="" style="width: 100%; height: 100%; object-fit: contain;"> -->
                            <img :src="profile.image ? `/imgs/shops/${profile.image}?v=${imageVersion}` : `/imgs/user-default.jpg`" style="width: 100%; height: 100%; object-fit: contain; border-radius: 50%;"/>

                        </div>
                        <div class="__note">
                            <span>• Kích thước hình ảnh tiêu chuẩn: chiều rộng 300px, chiều cao 300px</span><br>
                            <span>• Dung lượng file tối đa: 2.0MB</span><br>
                            <span>• Định dạng file được hỗ trợ: JPG, JPEG, PNC</span><br>
                        </div>
                    </div>
                    <div class="__descriptionshop fs-14 my-3">
                        <span class="fw-500">Mô tả:</span>
                        {{profile.description ? profile.description : 'Chưa có mô tả'}}
                    </div>
                    <div class="__background mx-auto shadow-sm text-light bg-secondary d-flex justify-content-center align-items-center fs-14"
                        style="border-radius: 5px; min-height: 300px;" alt="Shop Banner" v-if="!profile.background">Không có ảnh nền
                    </div>
                    <div class="__coverphotoshop my-3" style="width: 100%; height: 300px;" v-else>
                        <span class="fs-14 fw-500">Ảnh bìa shop:</span>
                        <img :src="`/imgs/shops/${profile.background}?v=${backgroundVersion}`" class="mt-1" style="width: 100%; height: 100%; object-fit: contain;"/>
                        <!-- <img :src="`/imgs/shops/${profile.background}`" alt="" class="mt-1" style="width: 100%; height: 100%; object-fit: contain;"> -->
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-background container-fluid d-flex justify-content-center align-items-center" v-if="editing_modal">
            <div class="modal-ezeshop bg-white shadow-sm px-4 pt-4 pb-2"  style="min-height: 500px;">
                <div class="modal-ezeshop__top d-flex justify-content-between align-items-center">
                    <div class="modal-ezeshop__name fs-18 fw-500">Sửa Profile</div>
                    <div class="modal-ezeshop__cancel cursor-pointer"><i class="fa-solid fa-xmark fs-18" @click="close_modal"></i></div>
                </div>
                <div class="modal-ezeshop__main w-100 overflow-y-auto">
                    <div class="__background-wrapper position-relative mx-auto shadow-sm text-light bg-secondary d-flex justify-content-center align-items-center fs-14"
                        style="border-radius: 5px; height: 100px; cursor: pointer;"
                        @click="select_background">
                        <template v-if="!profile.background && !profile.previewBackground">
                            Không có ảnh nền
                        </template>
                        <img v-else-if="profile.previewBackground" :src="profile.previewBackground" class="w-100 h-100" style="object-fit: cover; border-radius: 5px;"/>
                        <img v-else :src="`/imgs/shops/${profile.background}`" class="w-100 h-100" style="object-fit: cover; border-radius: 5px;"/>
                        <div class="__upload-overlay position-absolute top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center">
                            <i class="fa-solid fa-plus fs-24 text-white"></i>
                        </div>
                        <input type="file" ref="backgroundInput" style="display: none;" @change="handle_background_upload">
                    </div>
                    <div class="__info d-flex pt-2">
                        <div class="__logo-wrapper position-relative" style="height: 70px; width: 70px; border-radius: 50%; cursor: pointer;" @click="select_logo">
                            <img
                                :src="profile.previewLogo ? profile.previewLogo : (profile.image ? `/imgs/shops/${profile.image}` : `/imgs/user-default.jpg`)"
                                alt=""
                                style="width: 100%; height: 100%; object-fit: contain; border-radius: 50%;" />
                            <div class="__upload-overlay position-absolute top-0 w-100 h-100 d-flex justify-content-center align-items-center"
                                style="border-radius: 50%;">
                                <i class="fa-solid fa-camera text-white"></i>
                            </div>
                            <input type="file" ref="logoInput" style="display: none;" @change="handle_logo_upload">
                        </div>
                        <div class="__detail d-flex flex-column justify-content-between ms-3">
                            <div class="__name fw-semibold fs-14">{{ profile.shop_name }}</div>
                            <div class="__side fs-12"><span class="text-danger">0</span> Sản phẩm | <span class="text-danger">0</span> Theo dõi | <span class="text-danger">0</span> Đánh giá</div>
                            <div class="__others fs-12">Thông tin khác...</div>
                            <div class="__task d-flex gap-2">
                                <div class="__following">Theo dõi</div>
                                <div class="__chatting">Nhắn tin</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-ezeshop__bottom d-flex justify-content-end w-100 align-items-center border-top">
                    <div class="modal-ezeshop__cancel me-4"><button class="white-btn py-2 px-4 fs-14" @click="close_modal">Huỷ</button></div>
                    <div class="modal-ezeshop__save"><button class="main-btn py-2 px-4 fs-14" type="button" @click="update_profile">Lưu</button></div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref, reactive, watch } from 'vue';
import api from "@/configs/api"
import message from '@/utils/messageState';

const editing_modal = ref(false);
const open_modal = () => editing_modal.value = true;
const close_modal = () => editing_modal.value = false;

const imageVersion = ref(Date.now());
const backgroundVersion = ref(Date.now());

const profile = reactive({
    shop_name: '',
    image: '',
    background: '',
    description: '',
    previewLogo: null,
    previewBackground: null,
    logoFile: null,
    backgroundFile: null,
});

const logoInput = ref(null)
const backgroundInput = ref(null)

const select_logo = () => logoInput.value.click()
const select_background = () => backgroundInput.value.click()

const handle_logo_upload = (e) => {
    const file = e.target.files[0];
    if (!file) return;
    profile.logoFile = file;
    const reader = new FileReader();
    reader.onload = (ev) => {
        profile.previewLogo = ev.target.result;
    };
    reader.readAsDataURL(file);
};

const handle_background_upload = (e) => {
    const file = e.target.files[0];
    if (!file) return;
    profile.backgroundFile = file;
    const reader = new FileReader();
    reader.onload = (ev) => {
        profile.previewBackground = ev.target.result;
    };
    reader.readAsDataURL(file);
};

const get_profile = async () => {
    try {
        const response = await api.get('/get-shop');
        Object.assign(profile, response.data.shop);
        // Reset preview và file khi load lại profile
        profile.previewLogo = null;
        profile.previewBackground = null;
        profile.logoFile = null;
        profile.backgroundFile = null;

        imageVersion.value = Date.now();
        backgroundVersion.value = Date.now();
    } catch (error) {
        console.log(error);
    }
};

const update_profile = async () => {
    const formData = new FormData();
    // Thêm các trường khác nếu cần, ví dụ: formData.append('shop_name', profile.shop_name);
    if (profile.logoFile) formData.append('logo', profile.logoFile);
    if (profile.backgroundFile) formData.append('background', profile.backgroundFile);

    try {
        await api.post('/shop/update', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
        message.emit("show-message", { title: "Thành công", text: "Cập nhật profile thành công!", type: "success" });
        close_modal();
        window.location.reload()
    } catch (error) {
        message.emit("show-message", { title: "Lỗi", text: "Cập nhật profile thất bại!", type: "error" });
        console.error("Error updating profile:", error);
    }
};

onMounted(() => {
    get_profile();
});
</script>

<style scoped>
.__upload-overlay {
    background-color: rgba(0, 0, 0, 0.5);
    opacity: 0;
    transition: opacity 0.3s;
    z-index: 1;
    border-radius: inherit;
}

.__background-wrapper:hover .__upload-overlay,
.__logo-wrapper:hover .__upload-overlay {
    opacity: 1;
}

</style>