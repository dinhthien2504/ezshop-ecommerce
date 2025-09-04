<template>
    <main class="admin__content bg-light">
        <div class="fw-bold align-items-center d-flex gap-2" @click="goBack()" style="cursor: pointer;">
            <div class="text-decoration-none text-color">
                <i class="fs-30 ri-arrow-left-line"></i>
            </div>
            Trở về
        </div>
        <div v-if="loading" style="height: 60vh;" class="d-flex align-items-center justify-content-center">
            <loading__dot />
        </div>
        <div v-else-if="!product">
            <no_data />
        </div>
        <div v-else>
            <div class="bg-white radius-2">
                <div class="row pb-4">
                    <div class="col-12 col-lg-6 col-xl-5 pt-4">
                        <div class="row justify-content-center">
                            <!-- Ảnh sản phẩm chính -->
                            <div class="col-md-8 px-4 px-md-5" style="width: 100%">
                                <video v-if="video" controls autoplay muted loop
                                    class="product-img img-fluid responsive-media">
                                    <source :src="`/imgs/products/${mainImage.url}`" type="video/mp4" />
                                    Your browser does not support the video tag.
                                </video>
                                <img v-else :src="`/imgs/products/${mainImage.url}`" alt="Sản phẩm"
                                    class="product-img img-fluid responsive-media" />
                            </div>

                            <!-- Danh sách ảnh nhỏ -->
                            <div style="position: relative; width: 100%" class="col-md-8 mt-3 px-4 px-md-5">
                                <button style="position: absolute; left: 10%" @click="scrollThumbnails(-1)"
                                    class="nav-btn left d-none d-lg-flex">&#10094;</button>
                                <div class="thumb-scroll" ref="thumbContainer">
                                    <div class="thumb-wrapper d-flex" ref="thumbWrapper">
                                        <template v-for="img in medias">
                                            <!-- Hiển thị video thumbnail -->
                                            <video v-if="img.type === 'video'" :key="img.url" class="thumb-img"
                                                :class="{ active: mainImage.url === img.url }" @click="changeImage(img)"
                                                muted
                                                style="width: 80px; height: 80px; object-fit: cover; cursor: pointer;">
                                                <source :src="`/imgs/products/${img.url}`" type="video/mp4" />
                                            </video>

                                            <!-- Hiển thị ảnh thumbnail -->
                                            <img v-else :key="`image-${img.url}`" :src="`/imgs/products/${img.url}`"
                                                :class="{ 'thumb-img': true, 'active': mainImage.url === img.url }"
                                                @click="changeImage(img)"
                                                style="width: 80px; height: 80px; object-fit: cover; cursor: pointer;" />
                                        </template>

                                    </div>
                                </div>
                                <button style="position: absolute; right: 10%;" @click="scrollThumbnails(1)"
                                    class="nav-btn right d-none d-lg-flex">&#10095;</button>
                            </div>
                        </div>
                        <!-- <div class="d-flex align-items-center gap-3 mt-4 ms-4">
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-semibold">Chia sẻ:</span>
                                <a href="#" class="text-decoration-none">
                                    <img src="https://img.icons8.com/fluency/32/facebook-messenger.png"
                                        alt="Messenger" />
                                </a>
                                <a href="#" class="text-decoration-none">
                                    <img src="https://img.icons8.com/ios-filled/32/3b5998/facebook.png"
                                        alt="Facebook" />
                                </a>
                                <a href="#" class="text-decoration-none">
                                    <img src="https://img.icons8.com/ios-filled/32/e60023/pinterest.png"
                                        alt="Pinterest" />
                                </a>
                                <a href="#" class="text-decoration-none">
                                    <img src="https://img.icons8.com/ios-filled/32/1DA1F2/twitter.png" alt="Twitter" />
                                </a>
                            </div>

                            
                            <div class="border-start ps-3 d-flex align-items-center gap-2">
                                <img id="heart-icon" src="https://img.icons8.com/ios/32/ff0000/like--v1.png" alt="Like"
                                    onclick="toggleLike()" style="cursor: pointer" />
                                <span id="like-text" class="fw-semibold">Đã thích (220)</span>
                            </div>
                        </div> -->
                    </div>
                    <div class="col col-lg-6 col-xl-7 px-4 px-md-5 mt-4">
                        <span class="fs-24 fw-semibold">{{ product?.name }}</span>
                        <!-- <div class="d-flex align-items-center mt-2">
                            <span class="fw-semibold text-decoration-underline me-2">{{ averageRating }}</span>
                            <template v-for="i in 5">
                                <i :key="i" v-if="i <= Math.floor(averageRating)"
                                    class="fas fa-star star text-warning fs-10"></i>
                                <i :key="'half-' + i"
                                    v-else-if="i === Math.ceil(averageRating) && averageRating - Math.floor(averageRating) >= 0.5"
                                    class="fas fa-star-half-alt star text-warning fs-10"></i>
                                <i :key="'empty-' + i" v-else class="fas fa-star star fs-10"></i>
                            </template>

                            <span class="border-start border-2 mx-2 ps-2 fw-semibold text-decoration-underline">{{
                                totalRates }}</span>
                            <span class="text-secondary">Đánh giá</span>

                            <span class="border-start border-2 mx-2 ps-2 text-secondary">Đã bán</span>
                            <span class=" fw-semibold">{{ sales }}</span>

                            <span class="ms-auto text-secondary">Tố cáo</span>
                        </div> -->
                        <div v-if="minPrice == maxPrice" class="d-flex align-items-center mt-4 bg-light p-3 flex-wrap"
                            style="border-radius: 2px">
                            <span class="fs-24 fw-semibold text-danger ms-3">{{ minPrice }}</span>
                        </div>
                        <div v-else class="d-flex align-items-center mt-4 bg-light p-3 flex-wrap"
                            style="border-radius: 2px">
                            <span class="fs-24 fw-semibold text-danger ms-3">{{ minPrice }} - {{ maxPrice }}</span>
                        </div>

                        <div class="row mt-4 ms-1">
                            <!-- <div class="row">
                                <div class="col-4 d-none d-md-flex">
                                    <span class="fw-semibold text-secondary me-2">Vận chuyển</span>
                                </div>
                                <div class="col">
                                    <div class="d-flex">
                                        <i class="bi bi-truck text-primary me-1"></i>
                                        <span class="text-secondary">Nhận từ 24 Th03 - 28 Th03, phí giao đ0 &gt;</span>
                                    </div>
                                    <div class="text-muted small">
                                        Tặng Voucher 15.000đ nếu đơn giao sau thời gian trên
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-4 d-none d-md-flex">
                                    <span class="fw-semibold text-secondary me-2">An tâm mua sắm <br/>cùng EziShop</span>
                                </div>
                                <div class="col">
                                    <div class="d-flex">
                                        <i class="bi bi-shield-check text-danger me-1"></i>
                                        <span class="text-secondary">Trả hàng miễn phí 15 ngày. Bảo hiểm thiệt hại sản
                                            phẩm</span>
                                    </div>
                                </div>
                            </div> -->

                            <div v-for="attr in attributes" class="row mt-3">
                                <div class="col-3">
                                    <span class="fw-semibold text-secondary me-2">{{ attr.name }}</span>
                                </div>
                                <div class="col">
                                    <div class="d-flex flex-wrap gap-2">
                                        <!-- Value -->
                                        <button v-for="value in attr.values" :key="value.id" :class="[
                                            'border',
                                            'px-2',
                                            'py-1',
                                            'd-flex',
                                            'align-items-center',
                                            'attribute-option',
                                            { 'selected': selectedAttributes[attr.id] === value.id }
                                        ]" style="border-radius: 2px; cursor: pointer"
                                            @click="selectAttribute(attr.id, value.id)">
                                            {{ value.value }}

                                            <div v-if="selectedAttributes[attr.id] == value.id"
                                                class="product-variation__tick">
                                                <svg viewBox="0 0 12 12" class="shopee-svg-icon icon-tick-bold">
                                                    <g>
                                                        <path
                                                            d="M5.2 10.9c-.2 0-.5-.1-.7-.2l-4.2-3.7c-.4-.4-.5-1-.1-1.4s1-.5 1.4-.1l3.4 3 5.1-7c.3-.4 1-.5 1.4-.2s.5 1 .2 1.4l-5.7 7.9c-.2.2-.4.4-.7.4z">
                                                        </path>
                                                    </g>
                                                </svg>
                                            </div>
                                        </button>

                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-3 d-none d-md-flex">
                                    <span class="fw-semibold text-secondary me-2">Số lượng</span>
                                </div>

                                <div class="col">
                                    <span class="text-secondary">{{ variantFlag ? variantStock : totalStock }} sản phẩm
                                        có
                                        sẵn</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            

            <div class="mt-3 bg-white radius-2">
                <div class="pt-4 mx-2">
                    <div class="fs-24 fw-semibold bg-light p-3">CHI TIẾT SẢN PHẨM</div>
                </div>
                <div class="row mt-3 mx-2">
                    <div class="col-2">
                        <span class="fw-semibold text-secondary me-2">Danh mục</span>
                    </div>
                    <div class="col">
                        <a href="#" class="text-danger">EzShop </a><span v-for="cate in categories"> > <a href="#"
                                 class="text-danger">{{ cate.name }}</a></span>
                    </div>
                </div>
                <div class="row mt-3 mx-2">
                    <div class="col-2">
                        <span class="fw-semibold text-secondary me-2">Số sản phẩm còn lại</span>
                    </div>
                    <div v-if="totalStock > 1" class="col">CÒN HÀNG</div>
                    <div v-else class="col">HẾT HÀNG</div>
                </div>
                <div class="row mt-3 mx-2">
                    <div class="col-2">
                        <span class="fw-semibold text-secondary me-2">Trọng lượng</span>
                    </div>
                    <div class="col">{{ product.weight }} g</div>
                </div>
                <div class="row mt-3 mx-2">
                    <div class="col-2">
                        <span class="fw-semibold text-secondary me-2">Chiều cao</span>
                    </div>
                    <div class="col">{{ product.height }} cm</div>
                </div>
                <div class="row mt-3 mx-2">
                    <div class="col-2">
                        <span class="fw-semibold text-secondary me-2">Chiều dài</span>
                    </div>
                    <div class="col">{{ product.length }} cm</div>
                </div>
                <div class="row mt-3 mx-2">
                    <div class="col-2">
                        <span class="fw-semibold text-secondary me-2">Chiều rộng</span>
                    </div>
                    <div class="col">{{ product.width }} cm</div>
                </div>
                <!-- <div class="row mt-3 mx-2">
                    <div class="col-2">
                        <span class="fw-semibold text-secondary me-2">Gửi từ</span>
                    </div>
                    <div class="col">Khánh Hòa</div>
                </div> -->

                <div class="py-4 mx-2 mt-4">
                    <div class="fs-24 fw-semibold bg-light p-3">MÔ TẢ SẢN PHẨM</div>
                    <div class="fs-14 mx-2 mt-3 px-4" v-html="product?.description"></div>
                </div>

            </div>

            <div class="d-flex bg-white radius-2 py-3 px-4 my-3 d-footer justify-content-end me-2">
                <button v-if="product?.status === 0 || product?.status === 3"
                    class="btn btn-success px-3 py-2 fw-semibold radius-2 me-2" @click="changeStatus(1)">Duyệt</button>
                <button v-if="product?.status === 2" class="btn btn-success px-3 py-2 fw-semibold radius-2 me-2"
                    @click="changeStatus(1)">Mở</button>
                <button v-if="product?.status === 1" class="btn btn-warning px-3 py-2 fw-semibold radius-2 me-2"
                    @click="showLockModal">Khóa</button>
                <button v-if="product?.status === 0" class="btn btn-danger px-3 py-2 fw-semibold radius-2 me-2"
                    @click="showRejectModal">Từ
                    Chối</button>
                <button class="btn btn-outline-secondary px-3 py-2 fw-semibold radius-2 me-2"
                    @click="goBack">Hủy</button>
            </div>
        </div>


        <!-- lock modal -->
        <div v-if="isLockModal"
            class="modal-background container-fluid p-0 d-flex justify-content-center align-items-center">
            <div class="modal-edit-address modal-ezeshop bg-white shadow-sm px-4 pt-4 pb-2">
                <div class="modal-ezeshop__top d-flex justify-content-between align-items-center">
                    <div class="modal-ezeshop__name fs-18 fw-500">Xác Nhận Khóa</div>
                    <div @click="isLockModal = false" class="modal-ezeshop__cancel cursor-pointer"><i
                            class="fa-solid fa-xmark fs-30"></i></div>
                </div>
                <!-- Content -->
                <div class="modal-ezeshop__main w-100 p-1 mb-4">
                    <div class="">
                        <label class="form-label fs-18 fw-medium">Lý do khóa</label>
                        <textarea v-model="lockReason" maxlength="1000" style="min-height: 150px;"
                            class="form-control"
                            placeholder="Nhập lý do khóa sản phẩm... tối đa 1000 ký tự"></textarea>
                        <div class="text-end text-muted fs-14 mt-1">
                            {{ lockReason.length }}/1000 ký tự
                        </div>
                    </div>
                </div>
                <div class="modal-ezeshop__bottom d-flex justify-content-end w-100 align-items-center border-top">
                    <div class="modal-ezeshop__cancel me-3"><button
                            class="btn btn-outline-secondary py-2 px-4 fs-14 radius-2"
                            @click="isLockModal = false">Hủy</button></div>
                    <div class="modal-ezeshop__save">
                        <button @click="confirmLock" form="form-edit-cate"
                            class="main-btn py-2 px-4 fs-14 radius-2" type="submit" :disabled="loadingLock">
                            <loading__loader_circle v-if="loadingLock" size="15px" color="#fff" border="2px" />
                            <span v-else>Xác nhận</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- reject modal -->
        <div v-if="isRejectModal"
            class="modal-background container-fluid p-0 d-flex justify-content-center align-items-center">
            <div class="modal-edit-address modal-ezeshop bg-white shadow-sm px-4 pt-4 pb-2">
                <div class="modal-ezeshop__top d-flex justify-content-between align-items-center">
                    <div class="modal-ezeshop__name fs-18 fw-500">Xác Nhận Từ Chối</div>
                    <div @click="isRejectModal = false" class="modal-ezeshop__cancel cursor-pointer"><i
                            class="fa-solid fa-xmark fs-30"></i></div>
                </div>
                <!-- Content -->
                <div class="modal-ezeshop__main w-100 p-1 mb-4">
                    <div class="">
                        <label class="form-label fs-18 fw-medium">Lý do từ chối</label>
                        <textarea v-model="reason" maxlength="1000" style="min-height: 150px;"
                            class="form-control"
                            placeholder="Người vô tình vẽ hoa vẽ lá, ta đa tình tưởng đó là tối đa 1000 ký tự..."></textarea>
                        <div class="text-end text-muted fs-14 mt-1">
                            {{ reason.length }}/1000 ký tự
                        </div>
                    </div>
                </div>
                <div class="modal-ezeshop__bottom d-flex justify-content-end w-100 align-items-center border-top">
                    <div class="modal-ezeshop__cancel me-3"><button
                            class="btn btn-outline-secondary py-2 px-4 fs-14 radius-2"
                            @click="isRejectModal = false">Hủy</button></div>
                    <div class="modal-ezeshop__save">
                        <button @click="confirmReject" form="form-edit-cate"
                            class="main-btn py-2 px-4 fs-14 radius-2" type="submit" :disabled="loadingReject">
                            <loading__loader_circle v-if="loadingReject" size="15px" color="#fff" border="2px" />
                            <span v-else>Xác nhận</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>
</template>

<script setup>
import { onMounted, ref, nextTick } from 'vue';
import api from '@/configs/api';
import pagination from '@/components/pagination.vue';
import message from '@/utils/messageState';
import { useRoute, useRouter } from 'vue-router';
import { formatPrice } from '@/utils/formatPrice';
import { formatDate } from '@/utils/formatDate';
import { computed } from 'vue';
import { watch, inject } from 'vue';
import loading__dot from '@/components/loading/loading__dot.vue';
import loading__loader_circle from '@/components/loading/loading__loader-circle.vue';
import no_data from '@/components/no_data.vue';
import { getSlugFromId } from '@/utils/slugHelpers'


const route = useRoute();
const productId = route.params.id;
const product = ref(null);
const attributes = ref([]);
const minPrice = ref(0);
const maxPrice = ref(0);
const medias = ref([]);
const mainImage = ref({});
const video = ref(false);
const totalStock = ref(0);
const variantStock = ref(0);
const thumbWrapper = ref(null);
const selectedAttributes = ref({});
const variantFlag = ref(false);
const quantity = ref(1);
const loading = ref(false);
const thumbContainer = ref(null);
const rates = ref([]);
const averageRating = ref(0);
const meta = ref({});
const currentPage = ref(1);
const totalRates = ref(0);
const rate1 = ref(0)
const rate2 = ref(0)
const rate3 = ref(0)
const rate4 = ref(0)
const rate5 = ref(0)
const hasContent = ref(0)
const hasImage = ref(0)
const sales = ref(0);
const categories = ref([]);
const shop = ref([])
const router = useRouter();
const variant_id = ref();
const goBack = () => {
    router.back();
};


const selectAttribute = (attributeId, valueId) => {
    if (selectedAttributes.value[attributeId] === valueId) {
        // Nếu giá trị đã được chọn, xóa nó
        const { [attributeId]: _, ...rest } = selectedAttributes.value;
        selectedAttributes.value = rest;
    } else {
        // Nếu chưa được chọn, thêm giá trị
        selectedAttributes.value = {
            ...selectedAttributes.value,
            [attributeId]: valueId,
        };
    }
};

const selectedValues = computed(() => {
    return Object.values(selectedAttributes.value);
});

const changeImage = (img) => {
    if (img.type === 'video') {
        video.value = true;
    } else if (img.type === 'image') {
        video.value = false;
    }
    mainImage.value = img;
    scrollToActiveThumbnail(); // 🟢 Thêm dòng này
};

const scrollToActiveThumbnail = () => {
    nextTick(() => {
        const thumbs = thumbWrapper.value?.children || [];
        const index = medias.value.findIndex(m => m.url === mainImage.value.url);
        const activeThumb = thumbs[index];
        if (activeThumb && thumbContainer.value) {
            const container = thumbContainer.value;
            const thumbLeft = activeThumb.offsetLeft;
            const thumbRight = thumbLeft + activeThumb.offsetWidth;

            if (thumbLeft < container.scrollLeft) {
                container.scrollLeft = thumbLeft - 10;
            } else if (thumbRight > container.scrollLeft + container.clientWidth) {
                container.scrollLeft = thumbRight - container.clientWidth + 10;
            }
        }
    });
};

const scrollThumbnails = (direction) => {
    const container = thumbContainer.value;
    const wrapper = thumbWrapper.value;
    if (!container || !medias.value.length || !wrapper) return;

    const currentIndex = medias.value.findIndex(m => m.url === mainImage.value.url);
    let newIndex = currentIndex + direction;

    // Giới hạn index trong phạm vi
    if (newIndex < 0) newIndex = 0;
    if (newIndex >= medias.value.length) newIndex = medias.value.length - 1;

    const newMedia = medias.value[newIndex];
    if (newMedia) {
        mainImage.value = newMedia;
        video.value = newMedia.type === 'video';
    }

    // Scroll nhẹ theo direction
    const scrollAmount = 100; // hoặc 80 tùy thumbnail
    container.scrollLeft += direction * scrollAmount;

    // Đảm bảo thumbnail được hiển thị
    nextTick(() => {
        const thumbs = wrapper.children || [];
        const thumb = thumbs[newIndex];
        if (thumb && container) {
            const thumbLeft = thumb.offsetLeft;
            const thumbRight = thumbLeft + thumb.offsetWidth;

            if (thumbLeft < container.scrollLeft) {
                container.scrollLeft = thumbLeft - 10;
            } else if (thumbRight > container.scrollLeft + container.clientWidth) {
                container.scrollLeft = thumbRight - container.clientWidth + 10;
            }
        }
    });
};

const getProduct = async () => {
    try {

        const response = await api.get(`/products/${productId}`);
        product.value = response.data.product;
        minPrice.value = formatPrice(response.data.product.min_price);
        maxPrice.value = formatPrice(response.data.product.max_price);
        attributes.value = response.data.attributes;
        totalStock.value = response.data.total_stock;

        // Tách và sắp xếp medias
        const videos = response.data.medias.filter(media => media.type === "video");
        const mainImages = response.data.medias.filter(media => media.type === "image" && media.is_main == 1);
        const otherImages = response.data.medias.filter(media => media.type === "image" && media.is_main != 1);

        // Kết hợp lại theo thứ tự: video -> is_main == 1 -> ảnh thường
        medias.value = [...videos, ...mainImages, ...otherImages];

        // Ưu tiên video làm mainImage, nếu không có thì chọn ảnh chính
        if (videos.length > 0) {
            video.value = true;
            mainImage.value = videos[0];
        } else if (mainImages.length > 0) {
            video.value = false;
            mainImage.value = mainImages[0];
        } else if (otherImages.length > 0) {
            video.value = false;
            mainImage.value = otherImages[0];
        }

        console.log(response.data);
        // console.log(mainImage.value);
        // console.log(product.value);
    } catch (error) {
        console.log(error);
    }
};

const getVariant = () => {
    variant_id.value = null;

    if (!product.value || !product.value.variants || !selectedValues.value.length) {
        return;
    }

    // Sắp xếp selected values để so sánh chính xác
    const selected = [...selectedValues.value].map(String).sort().join('-');

    const matched = product.value.variants.find(variant => {
        const variantValues = variant.variant_attribute.map(v => String(v.attribute_value_id)).sort().join('-');
        return selected === variantValues;
    });

    if (matched) {
        // console.log("Biến thể phù hợp:", matched);
        variant_id.value = matched.id;
        quantity.value = 1;
        variantStock.value = matched.stock;
        minPrice.value = formatPrice(matched.price);
        maxPrice.value = formatPrice(matched.price);
        variantFlag.value = true;

        const variantImage = medias.value.find(media => media.url === matched.image);
        if (variantImage) {
            mainImage.value = variantImage;
            video.value = variantImage.type === 'video';
        }
    } else {
        variantFlag.value = false;
        message.emit('show-message', {
            title: 'Thông báo',
            text: 'Không tìm thấy biến thể phù hợp',
            type: 'error',
        });
    }
};

const getCategories = async () => {
    try {

        const res = await api.get(`/products/${product.value.category_id}/categories`);
        categories.value = res.data;
    } catch (error) {
        console.error("Lỗi khi lấy danh mục:", error);
    }
}

const changeStatus = async (status) => {
    try {
        const res = await api.post(`product/change-status/${productId}`, { status })
        console.log(res.data)
        message.emit('show-message', {
            title: 'Thành công',
            text: 'Đổi trạng thái sản phẩm thành công',
            type: 'success'
        })
        goBack()
    } catch (error) {
        console.log(error)
    }
}

const isLockModal = ref(false)
const lockReason = ref('')
const showLockModal = () => {
    isLockModal.value = true
}

const loadingLock = ref(false)
const confirmLock = async () => {
    if (lockReason.value.trim() === '') {
        message.emit('show-message', {
            title: 'Chú ý',
            text: 'Vui lòng nhập lý do khóa',
            type: 'warning'
        })
        return
    }
    loadingLock.value = true
    try {
        await api.post(`product/change-status/${productId}`, { status: 2, reason: lockReason.value })
        message.emit('show-message', {
            title: 'Thành công',
            text: 'Khóa sản phẩm thành công',
            type: 'success'
        })
        isLockModal.value = false
        lockReason.value = ''
        goBack();
    } catch (error) {
        console.error(error)
        message.emit('show-message', {
            title: 'Thất bại',
            text: 'Khóa sản phẩm thất bại',
            type: 'error'
        })
    } finally {
        loadingLock.value = false
    }
}

const isRejectModal = ref(false)
const reason = ref('')
const showRejectModal = () => {
    isRejectModal.value = true
}

const loadingReject = ref(false)
const confirmReject = async () => {
    if (reason.value.trim() === '') {
        message.emit('show-message', {
            title: 'Chú ý',
            text: 'Vui lòng nhập lý do từ chối',
            type: 'warning'
        })
        return
    }
    loadingReject.value = true
    try {
        await api.post(`product/change-status/${productId}`, { status: 3, reason: reason.value })
        message.emit('show-message', {
            title: 'Thành công',
            text: 'Từ chối sản phẩm thành công',
            type: 'success'
        })
        isRejectModal.value = false
        reason.value = ''
        goBack();
    } catch (error) {
        console.error(error)
        message.emit('show-message', {
            title: 'Thất bại',
            text: 'Từ chối sản phẩm thất bại',
            type: 'error'
        })
    } finally {
        loadingReject.value = false
    }
}

watch(selectedAttributes, (newVal) => {
    if (Object.keys(newVal).length === Object.keys(attributes.value).length) {
        getVariant(productId); // Gọi hàm getVariant khi đủ giá trị
    } else {
        // Khi không đủ giá trị, đặt lại về totalStock
        variantFlag.value = false;
        minPrice.value = formatPrice(product.value.min_price);
        maxPrice.value = formatPrice(product.value.max_price);
    }
});

onMounted(async () => {
    loading.value = true;
    await getProduct();
    await Promise.all([
        getCategories(),
    ]);
    loading.value = false;
});




</script>

<style scoped>
.attribute-option {
    border: 2px solid rgb(224, 224, 224) !important;
    position: relative;
    background: #fff;
    color: inherit;
    transition: border 0.2s;
}

.attribute-option.selected {
    border: 2px solid #ff0015 !important;
    color: #ff0015;
    background: #fff;
}

.attribute-option:hover {
    border: 2px solid #ff0015 !important;
    color: #ff0015;
    background: #fff;
}

.thumb-scroll {
    width: 100%;
    overflow-x: auto;
    -ms-overflow-style: none;
    /* IE, Edge */
    scrollbar-width: none;
    /* Firefox */
}

.thumb-scroll::-webkit-scrollbar {
    display: none;
    /* Chrome, Safari */
}

.thumb-wrapper {
    flex-wrap: nowrap;
}

.responsive-media {
    aspect-ratio: 5 / 4;
    /* hoặc 4 / 3, tùy tỷ lệ mong muốn */
    width: 100%;
    object-fit: cover;
}

.btn-active {
    border: #dc3545 1px solid !important;
    background-color: #dc3545 !important;
    color: #ffffff !important;
}
</style>
