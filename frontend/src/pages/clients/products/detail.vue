<template>
    <main class="container">
        <div v-if="loading">
            <!-- Breadcrumb -->
            <div class="d-flex align-items-center py-3 gap-2">
                <div class="skeleton" style="width:100px; height:18px;"></div>
                <div class="skeleton" style="width:80px; height:18px;"></div>
                <div class="skeleton" style="width:120px; height:18px;"></div>
            </div>

            <!-- Gallery + Info -->
            <div class="bg-white radius-2 mt-3">
                <div class="row pb-4">
                    <div class="col-lg-6 px-4 pt-4">
                        <div class="skeleton" style="width:100%; height:400px;"></div>
                        <div class="d-flex gap-2 mt-3">
                            <div v-for="n in 4" :key="n" class="skeleton" style="width:80px; height:80px;"></div>
                        </div>
                    </div>
                    <div class="col-lg-6 px-4 pt-4">
                        <div class="skeleton mb-3" style="width:70%; height:28px;"></div>
                        <div class="skeleton mb-2" style="width:40%; height:20px;"></div>
                        <div class="skeleton mb-2" style="width:50%; height:20px;"></div>
                        <div class="skeleton mb-4" style="width:60%; height:28px;"></div>
                        <div v-for="n in 3" :key="n" class="skeleton mb-2" style="width:100%; height:36px;"></div>
                    </div>
                </div>
            </div>

            <!-- Shop info -->
            <div class="bg-white radius-2 mt-3 p-3 d-flex gap-3">
                <div class="skeleton rounded-circle" style="width:80px; height:80px;"></div>
                <div class="flex-grow-1">
                    <div class="skeleton mb-2" style="width:150px; height:20px;"></div>
                    <div class="skeleton" style="width:100px; height:20px;"></div>
                </div>
            </div>

            <!-- Chi tiết sản phẩm -->
            <div class="bg-white radius-2 mt-3 p-3">
                <div class="skeleton mb-3" style="width:200px; height:28px;"></div>
                <div v-for="n in 5" :key="n" class="d-flex gap-3 mb-2">
                    <div class="skeleton" style="width:120px; height:20px;"></div>
                    <div class="skeleton" style="flex:1; height:20px;"></div>
                </div>
            </div>

            <!-- Mô tả sản phẩm -->
            <div class="bg-white radius-2 mt-3 p-3">
                <div class="skeleton mb-3" style="width:200px; height:28px;"></div>
                <div v-for="n in 4" :key="n" class="skeleton mb-2" style="width:100%; height:18px;"></div>
            </div>

            <!-- Đánh giá sản phẩm -->
            <div class="bg-white radius-2 mt-3 p-3">
                <div class="skeleton mb-3" style="width:200px; height:28px;"></div>
                <div v-for="n in 3" :key="n" class="d-flex gap-3 mb-3">
                    <div class="skeleton rounded-circle" style="width:50px; height:50px;"></div>
                    <div class="flex-grow-1">
                        <div class="skeleton mb-2" style="width:40%; height:18px;"></div>
                        <div v-for="m in 2" :key="m" class="skeleton mb-1" style="width:100%; height:14px;"></div>
                    </div>
                </div>
            </div>
        </div>
        <div v-else-if="!product">
            <no_data />
        </div>
        <div v-else>
            <div class="d-none d-md-flex align-items-center py-3">
                <div class="fw-medium fs-14">
                    <router-link :to="{
                        name: 'home'
                    }" class="text-color">
                        EziShop
                    </router-link>
                    <span v-for="(cate, index) in categories">
                        <i class="fs-16 ri-arrow-right-s-line"></i>
                        <router-link :to="{
                            name: 'products',
                            query: index === 0
                                ? { catp: getSlugFromId(cate.id, categories), page: 1 }
                                : {
                                    catp: getSlugFromId(categories[index - 1].id, categories),
                                    catc: getSlugFromId(cate.id, categories),
                                    page: 1
                                }
                        }" class="text-danger">
                            {{ cate.name }}
                        </router-link>
                    </span>
                </div>
            </div>
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
                        <div class="d-flex align-items-center gap-3 mt-4 ms-4">
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
                                <i id="heart-icon"
                                    :class="liked ? 'fa-solid fa-heart text-danger fs-4' : 'fa-regular fa-heart fs-4 text-danger'"
                                    style="cursor: pointer" @click="toggleLike"></i>
                                <span id="like-text" class="fw-semibold">
                                    {{ liked ? 'Đã thích' : 'Thích' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col col-lg-6 col-xl-7 px-4 px-md-5 mt-4">
                        <span class="fs-24 fw-semibold">{{ product?.name }}</span>
                        <div class="d-flex align-items-center mt-2">
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
                            <span class=" fw-semibold">{{ product?.total_sell }}</span>
                        </div>
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
                                <div class="col-5 d-flex align-items-center">
                                    <div class="d-flex border" style="height: 38px; overflow: hidden;">
                                        <!-- Nút giảm -->
                                        <button class="btn p-0 d-flex align-items-center justify-content-center"
                                            type="button" style="width: 38px; height: 38px; border: none;"
                                            @click="decreaseQuantity">
                                            <span style="font-size: 18px;">−</span>
                                        </button>

                                        <!-- Ô số lượng -->
                                        <input type="text" :value="quantity" readonly
                                            style=" width: 50px; height: 38px; border: none; text-align: center; font-size: 16px; line-height: 38px; outline: none; background-color: white;" />

                                        <!-- Nút tăng -->
                                        <button class="btn p-0 d-flex align-items-center justify-content-center"
                                            type="button" style="width: 38px; height: 38px; border: none;"
                                            @click="increaseQuantity">
                                            <span style="font-size: 18px;">+</span>
                                        </button>
                                    </div>
                                </div>
                                <div class="col">
                                    <span class="text-secondary">{{ variantFlag ? variantStock : totalStock }}
                                        sản phẩm có sẵn
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex gap-3 mt-4 ms-3">
                            <button @click="addCart()" type="button" class="sub-btn px-4 py-2 fw-semibold radius-2">
                                Thêm vào giỏ hàng
                            </button>
                            <button @click="buyNow()" class="main-btn px-4 py-2 fw-semibold radius-2">
                                Mua ngay
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-3 bg-white radius-2">
                <div class="d-flex p-3 justify-content-around flex-wrap">
                    <div class="d-flex align-items-center">
                        <!-- Ảnh đại diện -->
                        <img :src="`${shop.image ? `/imgs/shops/${shop.image}` : '/imgs/user-default.jpg'}`"
                            class="rounded-circle me-3" alt="Avatar"
                            style="width: 80px; height: 80px; object-fit: cover" />

                        <!-- Thông tin người bán -->
                        <div class="flex-grow-1">
                            <h6 class="fw-bold m-0">{{ shop.shop_name }}</h6>
                            <!-- Nút hành động -->
                            <div class="mt-2 d-flex gap-2">
                                <button class="sub-btn fs-14 py-1 px-3" style="border-radius: 2px" @click="handle_chat">
                                    Chat Ngay
                                </button>
                                <button class="white-btn fs-14 py-1 px-3" style="border-radius: 2px" @click="goToShop">
                                    Xem Shop
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- Thông tin bên phải -->
                    <div class="text-secondary small ps-lg-5 mt-4 align-self-center d-flex flex-column gap-2">
                        <div class="d-flex flex-wrap gap-4">
                            <div>
                                Đánh giá <span class="text-danger fw-bold">{{ shop.total_rates }}</span>
                            </div>
                            <div>
                                Sản phẩm <span class="text-danger fw-bold">{{ shop.products_count }}</span>
                            </div>
                            <div>
                                Người theo dõi <span class="text-danger fw-bold">{{ shop.followers_count }}</span>
                            </div>
                        </div>
                        <div class="d-none d-md-flex flex-wrap gap-4">
                            <!-- <div>
                                Tỷ lệ phản hồi <span class="text-danger fw-bold">100%</span>
                            </div> -->
                            <div>
                                Thời gian phản hồi
                                <span class="text-danger fw-bold">trong vài phút</span>
                            </div>
                            <div>
                                Tham gia <span class="text-danger fw-bold">{{ formatDate(shop.created_at) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-3 bg-white radius-2">
                <div class="pt-4 mx-2">
                    <div class="fs-20 fw-semibold bg-light p-3">CHI TIẾT SẢN PHẨM</div>
                </div>
                <div class="row mt-3 mx-2">
                    <div class="col-2">
                        <span class="fw-semibold text-secondary me-2">Danh mục</span>
                    </div>
                    <div class="col">
                        <div class="fw-medium fs-14">
                            <router-link :to="{
                                name: 'home'
                            }" class="text-color">
                                EziShop
                            </router-link>
                            <span v-for="(cate, index) in categories">
                                <i class="fs-16 ri-arrow-right-s-line"></i>
                                <router-link :to="{
                                    name: 'products',
                                    query: index === 0
                                        ? { catp: getSlugFromId(cate.id, categories), page: 1 }
                                        : {
                                            catp: getSlugFromId(categories[index - 1].id, categories),
                                            catc: getSlugFromId(cate.id, categories),
                                            page: 1
                                        }
                                }" class="text-danger">
                                    {{ cate.name }}
                                </router-link>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="row mt-3 mx-2">
                    <div class="col-2">
                        <span class="fw-semibold text-secondary me-2">Số sản phẩm còn lại</span>
                    </div>
                    <div v-if="totalStock > 1" class="col fs-14 fw-medium">CÒN HÀNG</div>
                    <div v-else class="col fs-14 fw-medium">HẾT HÀNG</div>
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
                <div class="row mt-3 mx-2">
                    <div class="col-2">
                        <span class="fw-semibold text-secondary me-2">Gửi từ</span>
                    </div>
                    <div class="col fw-semibold">{{ shop.province_name }}</div>
                </div>

                <div class="py-4 mx-2 mt-4">
                    <div class="fs-20 fw-semibold bg-light p-3">MÔ TẢ SẢN PHẨM</div>
                    <div class="fs-14 mx-2 mt-3 px-4" v-html="product?.description"></div>
                </div>
            </div>
            <div class="mt-3 pb-3 bg-white radius-2">
                <!-- Tổng điểm đánh giá -->
                <div class="pt-2 mx-2">
                    <div class="fs-20 fw-semibold p-3">ĐÁNH GIÁ SẢN PHẨM</div>
                </div>
                <div class="py-4 px-5 mx-4 radius-2" style="background-color: #fff2f2">
                    <div class="row">
                        <div class="col-md-3 col-12 text-center d-flex flex-column align-items-center ">
                            <h3 class="text-color fw-semibold fs-20">
                                <span id="rating-text">{{ averageRating }}</span>
                                <span class="fw-normal"> trên 5</span>
                            </h3>
                            <div>
                                <template v-for="i in 5">
                                    <i :key="i" v-if="i <= Math.floor(averageRating)"
                                        class="fas fa-star star text-color fs-20"></i>
                                    <i :key="'half-' + i"
                                        v-else-if="i === Math.ceil(averageRating) && averageRating - Math.floor(averageRating) >= 0.5"
                                        class="fas fa-star-half-alt star text-color fs-20"></i>
                                    <i :key="'empty-' + i" v-else class="fas fa-star star fs-20"></i>
                                </template>
                            </div>
                        </div>
                        <div class="col-md-9 col-12">
                            <!-- Bộ lọc đánh giá -->
                            <div class="mt-3 d-flex flex-wrap gap-2 justify-content-center">
                                <button class="white-btn fs-14 py-1 px-2" :class="{ 'btn-active': filter == null }"
                                    @click="filter = null">
                                    Tất cả ({{ totalRates }})
                                </button>
                                <button class="white-btn fs-14 py-1 px-2" :class="{ 'btn-active': filter == 5 }"
                                    @click="filter = 5">
                                    5 sao ({{ rate5 }})
                                </button>
                                <button class="white-btn fs-14 py-1 px-2" :class="{ 'btn-active': filter == 4 }"
                                    @click="filter = 4">
                                    4 sao ({{ rate4 }})
                                </button>
                                <button class="white-btn fs-14 py-1 px-2" :class="{ 'btn-active': filter == 3 }"
                                    @click="filter = 3">
                                    3 sao ({{ rate3 }})
                                </button>
                                <button class="white-btn fs-14 py-1 px-2" :class="{ 'btn-active': filter == 2 }"
                                    @click="filter = 2">
                                    2 sao ({{ rate2 }})
                                </button>
                                <button class="white-btn fs-14 py-1 px-2" :class="{ 'btn-active': filter == 1 }"
                                    @click="filter = 1">
                                    1 sao ({{ rate1 }})
                                </button>
                                <button class="white-btn fs-14 py-1 px-2" :class="{ 'btn-active': filter == 'img' }"
                                    @click="filter = 'img'">
                                    Có hình ảnh/video ({{ hasImage }})
                                </button>
                                <button class="white-btn fs-14 py-1 px-2" :class="{ 'btn-active': filter == 'text' }"
                                    @click="filter = 'text'">
                                    Có bình luận ({{ hasContent }})
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Đánh giá người dùng -->
                <div v-if="filteredRates.length > 0" v-for="rate in filteredRates" class="mt-4 mx-5">
                    <div class="d-flex align-items-center">
                        <img :src="`${rate.user.image ? `/imgs/users/${rate.user.image}` : '/imgs/user-default.jpg'}`"
                            class="rounded-circle me-3" style="width: 60px; height: 60px" alt="Avatar" />
                        <div>
                            <h6 class="m-0">{{ rate.user.name }}</h6>
                            <div class="text-warning">
                                <template v-for="i in 5">
                                    <i class="fas fa-star star fs-10"
                                        :class="i <= Math.round(rate.rate) ? 'text-color' : ''"></i>
                                </template>
                            </div>
                            <small class="text-muted">{{ formatDate(rate.created_at) }} | Phân loại hàng: {{
                                rate.variantAttributesText }}</small>
                        </div>
                    </div>
                    <p class="mt-2">{{ rate.content }}.</p>
                    <!-- Hình ảnh đính kèm -->
                    <img v-for="img in rate.images" :src="`/imgs/rates/${img}`" class="rounded mt-2 me-2"
                        style="width: 80px; height: 80px; object-fit: cover;" alt="Rate Image" />
                    <hr />
                </div>

                <div v-else class="text-center py-5">
                    <img src="/imgs/noRate.png" alt="">
                    <p class="text-muted">Chưa có đánh giá</p>
                </div>
                <!-- Pagination -->
                <div class="d-flex align-items-center justify-content-end me-3">
                    <pagination :meta="meta" @changePage="onChangePage" />
                </div>

            </div>
            <div v-if="products.length > 0">
                <div class="suggest-product bg-white mt-32 d-block d-lg-block">
                    <div class="fs-18 fw-semibold text-center">Có thể bạn cũng thích</div>
                </div>

                <div class="suggest-product__box mt-10">
                    <div class="row g-2">
                        <div v-for="product in products" class="col-6 col-md-3 col-xl-2">
                            <productItem :product="product" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</template>

<script setup>
import { onMounted, ref, nextTick } from 'vue'
import api from '@/configs/api'
import pagination from '@/components/pagination.vue'
import message from '@/utils/messageState'
import { useRoute } from 'vue-router'
import { formatPrice } from '@/utils/formatPrice'
import { formatDate } from '@/utils/formatDate'
import { computed } from 'vue'
import { watch, inject } from 'vue'
import loading__dot from '@/components/loading/loading__dot.vue'
import no_data from '@/components/no_data.vue'
import { extractIdFromSlug } from '@/utils/slugHelpers'
import { getSlugFromId } from '@/utils/slugHelpers'
import productItem from '@/components/productItem.vue'
import useGoTo from '@/utils/useGoto'
import { useRouter } from 'vue-router'
const router = useRouter()
const { goTo } = useGoTo()
const route = useRoute()
// const productId = route.params.id
const productId = computed(() => {
    const id = route.params.id || extractIdFromSlug(Object.keys(route.params).find(key => key.includes('-')))
    return id
})
const product = ref(null)
const attributes = ref([])
const minPrice = ref(0)
const maxPrice = ref(0)
const medias = ref([])
const mainImage = ref({})
const video = ref(false)
const totalStock = ref(0)
const variantStock = ref(0)
const thumbWrapper = ref(null)
const selectedAttributes = ref({})
const variantFlag = ref(false)
const quantity = ref(1)
const loading = ref(false)
const loading_products = ref(false)
const thumbContainer = ref(null)
const rates = ref([])
const averageRating = ref(0)
const meta = ref({})
const currentPage = ref(1)
const totalRates = ref(0)
const rate1 = ref(0)
const rate2 = ref(0)
const rate3 = ref(0)
const rate4 = ref(0)
const rate5 = ref(0)
const hasContent = ref(0)
const hasImage = ref(0)
const categories = ref([])
const shop = ref([])
const products = ref([])
const chat_product = inject('chat_product')
const chat_shop = ref([])
const product_to_chat = ref(null)
const token = localStorage.getItem('token')
const selectAttribute = (attributeId, valueId) => {
    if (selectedAttributes.value[attributeId] === valueId) {
        // Nếu giá trị đã được chọn, xóa nó
        const { [attributeId]: _, ...rest } = selectedAttributes.value
        selectedAttributes.value = rest
    } else {
        // Nếu chưa được chọn, thêm giá trị
        selectedAttributes.value = {
            ...selectedAttributes.value,
            [attributeId]: valueId,
        }
    }
}

const selectedValues = computed(() => {
    return Object.values(selectedAttributes.value)
})

const increaseQuantity = () => {
    if (variantFlag.value) {
        if (quantity.value < variantStock.value) {
            quantity.value++
        }
    } else {
        if (quantity.value < totalStock.value) {
            quantity.value++
        }
    }
}

const decreaseQuantity = () => {
    if (quantity.value > 1) {
        quantity.value--
    }
}

const changeImage = (img) => {
    if (img.type === 'video') {
        video.value = true
    } else if (img.type === 'image') {
        video.value = false
    }
    mainImage.value = img
    scrollToActiveThumbnail()
}

const scrollToActiveThumbnail = () => {
    nextTick(() => {
        const thumbs = thumbWrapper.value?.children || []
        const index = medias.value.findIndex(m => m.url === mainImage.value.url)
        const activeThumb = thumbs[index]
        if (activeThumb && thumbContainer.value) {
            const container = thumbContainer.value
            const thumbLeft = activeThumb.offsetLeft
            const thumbRight = thumbLeft + activeThumb.offsetWidth

            if (thumbLeft < container.scrollLeft) {
                container.scrollLeft = thumbLeft - 10
            } else if (thumbRight > container.scrollLeft + container.clientWidth) {
                container.scrollLeft = thumbRight - container.clientWidth + 10
            }
        }
    })
}

const scrollThumbnails = (direction) => {
    const container = thumbContainer.value
    const wrapper = thumbWrapper.value
    if (!container || !medias.value.length || !wrapper) return

    const currentIndex = medias.value.findIndex(m => m.url === mainImage.value.url)
    let newIndex = currentIndex + direction

    // Giới hạn index trong phạm vi
    if (newIndex < 0) newIndex = 0
    if (newIndex >= medias.value.length) newIndex = medias.value.length - 1

    const newMedia = medias.value[newIndex]
    if (newMedia) {
        mainImage.value = newMedia
        video.value = newMedia.type === 'video'
    }

    // Scroll nhẹ theo direction
    const scrollAmount = 100 // hoặc 80 tùy thumbnail
    container.scrollLeft += direction * scrollAmount

    // Đảm bảo thumbnail được hiển thị
    nextTick(() => {
        const thumbs = wrapper.children || []
        const thumb = thumbs[newIndex]
        if (thumb && container) {
            const thumbLeft = thumb.offsetLeft
            const thumbRight = thumbLeft + thumb.offsetWidth

            if (thumbLeft < container.scrollLeft) {
                container.scrollLeft = thumbLeft - 10
            } else if (thumbRight > container.scrollLeft + container.clientWidth) {
                container.scrollLeft = thumbRight - container.clientWidth + 10
            }
        }
    })
}

const getProduct = async () => {
    try {
        const response = await api.get(`/products/${productId.value}`)
        product.value = response.data.product
        minPrice.value = formatPrice(response.data.product.min_price)
        maxPrice.value = formatPrice(response.data.product.max_price)
        attributes.value = response.data.attributes
        totalStock.value = response.data.total_stock

        // Tách và sắp xếp medias
        const videos = response.data.medias.filter(media => media.type === "video")
        const mainImages = response.data.medias.filter(media => media.type === "image" && media.is_main == 1)
        const otherImages = response.data.medias.filter(media => media.type === "image" && media.is_main != 1)

        // Kết hợp lại theo thứ tự: video -> is_main == 1 -> ảnh thường
        medias.value = [...videos, ...mainImages, ...otherImages]

        // Ưu tiên video làm mainImage, nếu không có thì chọn ảnh chính
        if (videos.length > 0) {
            video.value = true
            mainImage.value = videos[0]
        } else if (mainImages.length > 0) {
            video.value = false
            mainImage.value = mainImages[0]
        } else if (otherImages.length > 0) {
            video.value = false
            mainImage.value = otherImages[0]
        }
    } catch (error) {
        console.log(error)
    }
}

const getVariant = () => {
    variant_id.value = null
    if (!product.value || !product.value.variants || !selectedValues.value.length) {
        return
    }

    // Sắp xếp selected values để so sánh chính xác
    const selected = [...selectedValues.value].map(String).sort().join('-')

    const matched = product.value.variants.find(variant => {
        const variantValues = variant.variant_attribute.map(v => String(v.attribute_value_id)).sort().join('-')
        return selected === variantValues
    })

    if (matched) {
        // console.log("Biến thể phù hợp:", matched)
        variant_id.value = matched.id
        quantity.value = 1
        variantStock.value = matched.stock
        minPrice.value = formatPrice(matched.price)
        maxPrice.value = formatPrice(matched.price)
        variantFlag.value = true
        const variantImage = medias.value.find(media => media.url === matched.image)
        if (variantImage) {
            mainImage.value = variantImage
            video.value = variantImage.type === 'video'
        }
    } else {
        variantFlag.value = false
        message.emit('show-message', {
            title: 'Thông báo',
            text: 'Không tìm thấy biến thể phù hợp',
            type: 'error',
        })
    }
}

const getCategories = async () => {
    try {
        const res = await api.get(`/products/${productId.value}/categories`)
        categories.value = res.data
    } catch (error) {
        console.error("Lỗi khi lấy danh mục:", error)
        categories.value = []
    }
}

const getShopInfo = async () => {
    try {
        const shop_id = product.value.shop_id
        const res = await api.get(`shops/${shop_id}/shop-info`)
        shop.value = res.data
        chat_shop.value = res.data

        product_to_chat.value = {
            name: product.value.name,
            minPrice: Number(product.value.min_price),
            maxPrice: Number(product.value.max_price),
            image: medias.value.find(m => m.is_main === 1)?.url || medias[0]?.url,
            slug: product.value.slug,
        }
    } catch (e) {
        console.log(e)
    }
}

const handle_chat = () => {
    if (token) {
        chat_product.open_chat_with_product(product_to_chat.value, chat_shop.value.owner_id, 'user')
    } else {
        message.emit('show-message', {
            title: 'Thông báo',
            text: 'Vui lòng đăng nhập để trò chuyện',
            type: 'warning',
        })
    }
}

const goToShop = () => {
    if (shop.value && shop.value.id) {
        // Tạo slug từ tên shop
        const shopSlug = shop.value.shop_name
            .toLowerCase()
            .replace(/\s+/g, '-')
            .replace(/[^a-z0-9-]/g, '')

        goTo({
            name: 'shop-profile',
            params: {
                slug: shopSlug,
                id: shop.value.id
            }
        })
    }
}

const getProducts = async () => {
    try {
        loading_products.value = true
        const res = await api.get('/products', {
            params: {
                category_id: product.value.category_id || null,
            }
        })
        const filteredProducts = res.data.data.filter(p => p.id !== product.value.id)
        products.value = filteredProducts
    } catch (error) {
        console.error("Lỗi khi lấy sản phẩm liên quan:", error)
    } finally {
        loading_products.value = false
    }
}

const getRates = async () => {
    try {
        const res = await api.get(`/rates/${productId.value}`, {
            params: {
                page: currentPage.value,
            }
        })
        meta.value = {
            current_page: res.data.current_page,
            last_page: res.data.last_page,
            total: res.data.total,
        }
        rates.value = res.data.rates.map(rate => {
            // Tìm variant theo variant_id
            const variant = product.value?.variants?.find(v => v.id === rate.variant_id)
            let variantAttributesText = ''
            if (variant && variant.variant_attribute) {
                // Lấy tên giá trị thuộc tính, ví dụ: "Màu đỏ, Size L"
                variantAttributesText = variant.variant_attribute
                    .map(attr => attr.attribute_value.value)
                    .join(', ')
            }
            return {
                ...rate,
                variantAttributesText
            }
        })
        // console.log("Đánh giá:", rates.value)
        if (rates.value.length > 0) {
            const totalRating = rates.value.reduce((sum, rate) => sum + rate.rate, 0)
            averageRating.value = (totalRating / rates.value.length).toFixed(1)
        } else {
            averageRating.value = 0
        }
        totalRates.value = res.data.total
        rate1.value = rates.value.filter(r => r.rate === 1).length
        rate2.value = rates.value.filter(r => r.rate === 2).length
        rate3.value = rates.value.filter(r => r.rate === 3).length
        rate4.value = rates.value.filter(r => r.rate === 4).length
        rate5.value = rates.value.filter(r => r.rate === 5).length
        hasContent.value = rates.value.filter(r => r.content != '').length
        hasImage.value = rates.value.filter(r => r.images != null).length
    } catch (error) {
        console.error("Lỗi khi lấy đánh giá:", error)
    }
}

const filter = ref(null)
const filteredRates = computed(() => {
    if (filter.value === null) {
        return rates.value
    } else if (filter.value === 5) {
        return rates.value.filter(rate => rate.rate === 5)
    } else if (filter.value === 4) {
        return rates.value.filter(rate => rate.rate === 4)
    } else if (filter.value === 3) {
        return rates.value.filter(rate => rate.rate === 3)
    } else if (filter.value === 2) {
        return rates.value.filter(rate => rate.rate === 2)
    } else if (filter.value === 1) {
        return rates.value.filter(rate => rate.rate === 1)
    } else if (filter.value === 'img') {
        return rates.value.filter(rate => rate.images && rate.images.length > 0)
    } else if (filter.value === 'text') {
        return rates.value.filter(rate => rate.content && rate.content.trim() !== '')
    }
    return []
})

const onChangePage = (page) => {
    currentPage.value = page
}

watch(currentPage, () => {
    getRates()
})

const variant_id = ref()
const cartUpdated = inject('cartUpdated')
const addCart = async () => {
    if (!variant_id.value || !quantity.value) {
        message.emit("show-message", { title: "Thông báo", text: 'Chưa có dữ liệu phù hợp để thêm vào giỏ hàng.', type: "warning" })
        return
    }
    const token = localStorage.getItem('token')
    if (!token) {
        message.emit("show-message", { title: "Thông báo", text: 'Vui lòng đăng nhập để sử dụng chức năng này!', type: "warning" })
        return
    }
    try {
        const res = await api.post('/carts', {
            variant_id: variant_id.value,
            quantity: quantity.value
        })
        if (res.status == 201) {
            message.emit("show-message", { title: "Thông báo", text: res.data.message, type: "success" })
            cartUpdated.value++
        }
    } catch (error) {
        if (error.response.status == 400) {
            message.emit("show-message", { title: "Cảnh báo", text: error.response.data.message, type: "warning" })
        }
    }
}

const buyNow = async () => {
    if (!variant_id.value || !quantity.value) {
        message.emit("show-message", { title: "Thông báo", text: 'Chưa có dữ liệu phù hợp để thêm vào giỏ hàng.', type: "warning" })
        return
    }
    const token = localStorage.getItem('token')
    if (!token) {
        message.emit("show-message", { title: "Thông báo", text: 'Vui lòng đăng nhập để sử dụng chức năng này!', type: "warning" })
        return
    }
    try {
        const res = await api.post('/carts', {
            variant_id: variant_id.value,
            quantity: quantity.value
        })
        if (res.status == 201) {
            cartUpdated.value++
            router.push({
                name: 'cart',
                query: {
                    cart_id: res.data.cart_id
                }
            })
        }
    } catch (error) {
        if (error.response.status == 400) {
            message.emit("show-message", { title: "Cảnh báo", text: error.response.data.message, type: "warning" })
        }
    }
}

watch(selectedAttributes, (newVal) => {
    if (Object.keys(newVal).length === Object.keys(attributes.value).length) {
        getVariant(productId.value) // Gọi hàm getVariant khi đủ giá trị
    } else {
        // Khi không đủ giá trị, đặt lại về totalStock
        variantFlag.value = false
        minPrice.value = formatPrice(product.value.min_price)
        maxPrice.value = formatPrice(product.value.max_price)
    }
})
const liked = ref(false) // trạng thái yêu thích

const toggleLike = async () => {
    try {
        if (!liked.value) {
            // Thêm vào yêu thích
            await api.post('/favorites', { product_id: productId.value })
            liked.value = true
        } else {
            // Xóa khỏi yêu thích
            await api.delete(`/favorites/${productId.value}`)
            liked.value = false
        }
    } catch (error) {
        console.error('Lỗi khi cập nhật yêu thích:', error)
    }
}

const checkWishlist = async () => {
    try {
        const res = await api.get('/favorites/checkWishlist', {
            params: { product_id: productId.value }
        })
        liked.value = res.data.is_following
    } catch (error) {
        liked.value = false
    }
}
watch(productId, (newId, oldId) => {
    (async () => {
        loading.value = true
        try {
            const pro = getProduct()
            const cate = getCategories()
            const rate = getRates()
            if (token) {
                const wishlist = checkWishlist()
                await wishlist
            }
            await pro
            await cate
            await rate
            await getShopInfo() //Ràng buộc            
        } finally {
            loading.value = false
            await getProducts()
        }
    })()
}, { immediate: true })

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
    border: var(--main-color) !important;
    background-color: var(--main-color) !important;
    color: #ffffff !important;
}

.skeleton {
    position: relative;
    overflow: hidden;
    background-color: #e0e0e0;
    /* màu nền xám nhạt */
    border-radius: 4px;
}

.skeleton::after {
    content: "";
    position: absolute;
    top: 0;
    left: -150px;
    height: 100%;
    width: 150px;
    background: linear-gradient(90deg,
            transparent,
            rgba(255, 255, 255, 0.4),
            transparent);
    animation: skeleton-loading 1.2s infinite;
}

@keyframes skeleton-loading {
    100% {
        left: 100%;
    }
}
</style>
