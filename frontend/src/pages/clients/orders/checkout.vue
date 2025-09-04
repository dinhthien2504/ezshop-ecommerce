<template>
    <div class="container position-relative">
        <div v-if="loading.fetch" class="row">
            <div class="col-lg-8 col-12">
                <div class="skeleton-box mb-3" style="height: 100px;"></div>
                <div v-for="n in 3" :key="n" class="bg-white rounded-2 p-3 mb-3">
                    <div class="d-flex mb-3">
                        <div class="skeleton-box" style="width: 80px; height: 80px;"></div>
                        <div class="ms-3" style="flex: 1;">
                            <div class="skeleton-box mb-2" style="width: 60%; height: 14px;"></div>
                            <div class="skeleton-box mb-1" style="width: 40%; height: 12px;"></div>
                            <div class="skeleton-box mt-2" style="width: 80px; height: 14px;"></div>
                        </div>
                    </div>
                </div>
                <div class="skeleton-box" style="height: 50px; width: 100%;"></div>
            </div>
            <div class="col-lg-4 col-12">
                <div class="skeleton-box mb-3" style="height: 180px;"></div>
                <div class="skeleton-box mb-3" style="height: 60px;"></div>
                <div class="skeleton-box" style="height: 160px;"></div>
            </div>
        </div>
        <div v-else class="row g-2">
            <div class="col-lg-8 col-md-7 col-12">
                <div class="checkout-top"></div>
                <div class="d-flex justify-content-between align-items-center p-2 sticky-top bg-white">
                    <div class="d-flex align-items-center">
                        <p class="m-0 fs-16 fw-semibold text-color ms-1">
                            Địa chỉ nhận hàng
                        </p>
                    </div>
                    <div class="d-flex align-items-center">
                        <a @click="display_address_pickup.address_id ? is_modal_active_addresses = true : open_modal_address(null)"
                            class="ms-1 fs-14 fw-semibold text-secondary text-decoration-none cursor-pointer">
                            {{ display_address_pickup.address_id ? 'Thay đổi' : 'Chọn' }}
                        </a>
                    </div>
                </div>
                <div v-if="display_address_pickup && display_address_pickup.address_id" class="bg-white px-2 py-3 mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <p class="fs-14 fw-semibold m-0">
                            {{ display_address_pickup.name }}
                        </p>
                        |
                        <p class="fs-14 fw-semibold m-0">
                            {{ display_address_pickup.phone }}
                        </p>
                    </div>
                    <div class="d-flex align-items-center gap-1 fs-14 fw-medium">
                        <i class="ri-map-pin-line"></i>
                        <p class="m-0">
                            {{ display_address_pickup.address_detail }},
                            {{ display_address_pickup.ward }},
                            {{ display_address_pickup.district }},
                            {{ display_address_pickup.province }}
                        </p>
                    </div>
                </div>
                <div v-else>
                    <p class="m-0 text-danger">Vui lòng chọn địa chỉ nhận hàng.</p>
                </div>
                <!-- item buy -->
                <div v-for="(shop, index) in checkoutItems.items" class="cart__item bg-white mb-2">
                    <div class="d-flex align-items-center justify-content-between bg-white p-2 border-bottom">
                        <p class="m-0 fs-14 ms-1 text-nowrap">Bưu kiện {{ index + 1 }} /{{ checkoutItems.items.length }}
                        </p>
                        <p class="m-0 fs-14 ms-1 text-nowrap">
                            Vận chuyển bởi
                            <span class="fw-bold">{{ shop.shop_name }}</span>
                        </p>
                    </div>
                    <!--  item buy detail -->
                    <template v-for="variant in shop.variants">
                        <div class="row g-0 px-2 py-3">
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-3 col-4">
                                <div class="d-flex align-items-center">
                                    <img :src="`/imgs/products/${variant.image}`" class="cart__item-img" />
                                </div>
                            </div>
                            <div class="col-xl-10 col-lg-9 col-md-8 col-sm-9 col-8">
                                <div class="row g-1">
                                    <div class="col-12 col-xl-6">
                                        <p class="m-0 fs-14 text-black pe-2 cart__item--variant-name">
                                            {{ variant.product_name }}
                                        </p>
                                        <div class="position-relative">
                                            <p class="cart__item--variant fs-12 text-secondary m-0">
                                                {{ variant.full_name_variant }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-7 col-md-12 col-lg-7 col-xl-4">
                                        <div class="d-flex align-items-center gap-2">
                                            <p class="m-0 fs-16 text-dark fw-medium">
                                                {{ Number(variant.price).toLocaleString('vi-VN') + ' ₫' }}
                                            </p>
                                            <span>-</span>
                                            <p class="m-0 fs-16 text-color fw-medium">
                                                {{ Number(variant.total_price).toLocaleString('vi-VN') + ' ₫' }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-5 col-md-12 col-lg-5 col-xl-2 d-flex justify-content-end">
                                        <p class="fs-16 m-0">Số lượng: {{ variant.total_quantity }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                    <div class="border-top p-2 d-flex align-items-center justify-content-between">
                        <div class="d-flex fs-14 fw-medium gap-2">
                            <p class="d-flex gap-2 text-color cursor-pointer" @click="getVouchersForShop(shop.shop_id)">
                                <i class="ri-coupon-3-line"></i>
                                Giảm {{ formatToK(calculateDiscountByShop(shop.shop_id, shop.variants)) }}
                            </p>
                            <div class="position-relative">
                                <div v-if="shopIdForVoucher == shop.shop_id" class="cart__voucher-shop"
                                    style="right: -240px;">
                                    <div class="modal-ezeshop border bg-white shadow-sm" style="width: 350px;">
                                        <div
                                            class="modal-ezeshop__top d-flex justify-content-between align-items-center border-bottom p-3">
                                            <div class="modal-ezeshop__name fs-18 fw-500">{{ shop.shop_name }} Voucher
                                            </div>
                                            <div @click="shopIdForVoucher = null"
                                                class="modal-ezeshop__cancel cursor-pointer">
                                                <i class="fa-solid fa-xmark fs-18"></i>
                                            </div>
                                        </div>
                                        <!-- Content -->
                                        <div class="modal-ezeshop__main w-100 px-2 py-0">
                                            <form>
                                                <div class="position-relative" style="min-height: 200px">
                                                    <div v-if="vouchers.shop[shop.shop_id].length == 0"
                                                        class="d-flex align-items-center flex-column mt-5">
                                                        <img src="/imgs/empty-voucher.png"
                                                            style="width: 70px; object-fit: cover"
                                                            alt="Không có voucher">
                                                        <p class="text-secondary fw-medium fs-16 mt-3 text-center">
                                                            Hiện tại shop không có voucher nào.
                                                        </p>
                                                    </div>
                                                    <div v-else
                                                        class="position-absolute bg-white w-100 overflow-hidden px-2">
                                                        <!-- Voucher Sale-->
                                                        <div class="mb-3" v-if="vouchers.shop[shop.shop_id]">
                                                            <!-- Voucher Item -->
                                                            <div v-for="voucherShop in vouchers.shop[shop.shop_id]"
                                                                :class="{
                                                                    'mt-3': true,
                                                                    'disabled': canSelectShopVoucher(shop.variants, voucherShop.min)
                                                                        || getTimeRemaining(voucherShop.end_date) == 'Đã hết hạn'
                                                                }">
                                                                <div
                                                                    class="d-flex align-items-center gap-2 border radius-3 position-relative">
                                                                    <div class="box-voucher bg-main"
                                                                        style="min-width: 70px; min-height: 70px;">
                                                                        <img src="/imgs/ezshop.png" alt="Hình ảnh">
                                                                        <span>EZShop</span>
                                                                    </div>
                                                                    <div
                                                                        class="d-flex justify-content-center flex-column">
                                                                        <p class="m-0 fs-12 fw-medium">
                                                                            Giảm
                                                                            <span v-if="voucherShop.percent">{{
                                                                                voucherShop.percent }}
                                                                                %</span>
                                                                            tối đa
                                                                            {{
                                                                                Number(voucherShop.max).toLocaleString('vi-VN')
                                                                                + '₫' }}
                                                                        </p>
                                                                        <p class="m-0 fs-12 fw-medium">Đơn Tối Thiểu
                                                                            {{
                                                                                Number(voucherShop.min).toLocaleString('vi-VN')
                                                                                + '₫' }}
                                                                        </p>
                                                                        <div class="d-flex fs-10 mt-2 gap-1">
                                                                            <span class="fw-medium">
                                                                                {{
                                                                                    getTimeRemaining(voucherShop.end_date)
                                                                                }}
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="ms-auto">
                                                                        <div class="radio-container me-3">
                                                                            <input type="radio"
                                                                                @change="voucher_temp.shop[shop.shop_id] = voucherShop.id"
                                                                                :value="voucherShop.id"
                                                                                :id="voucherShop.id"
                                                                                :checked="voucher_selected.shop[shop.shop_id] == voucherShop.id"
                                                                                class="radio-input" name="discount" />
                                                                            <label :for="voucherShop.id" class="radio">
                                                                                <span class="circle"></span>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="voucher-quantity">
                                                                        <span>x{{ voucherShop.quantity }}</span>
                                                                    </div>
                                                                </div>
                                                                <div class="box-voucher__bottom">
                                                                    <div class="bg-main" style="width: 60px;"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <div
                                            class="modal-ezeshop__bottom d-flex justify-content-end w-100 align-items-center border-top p-3">
                                            <div class="modal-ezeshop__cancel me-4">
                                                <button @click="shopIdForVoucher = null"
                                                    class="white-btn py-2 px-4 fs-14">TRỞ
                                                    LẠI</button>
                                            </div>
                                            <div :class="{
                                                'modal-ezeshop__save': true
                                            }">
                                                <button class="main-btn py-2 px-4 fs-14" @click="selectPlatformVoucher"
                                                    type="button">Áp
                                                    Dụng</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="p-2 d-flex justify-content-end align-items-center gap-2">
                            <p class="fs-14 fw-medium m-0">Phí vận chuyển: </p>
                            <p class="text-end m-0 fs-14 fw-medium">
                                <span v-if="shopTotalMap.map[shop.shop_id].shippingDiscount > 0">
                                    <span class="text-secondary text-decoration-line-through">
                                        {{ (shopTotalMap.map[shop.shop_id].shippingFee || 0).toLocaleString('vi-VN')
                                            + ' ₫'
                                        }}
                                    </span>
                                    {{ ((shopTotalMap.map[shop.shop_id].shippingFee || 0) -
                                        (shopTotalMap.map[shop.shop_id].shippingDiscount ||
                                            0)).toLocaleString('vi-VN') + ' ₫' }}
                                </span>
                                <span v-else>
                                    {{ (shopTotalMap.map[shop.shop_id].shippingFee || 0).toLocaleString('vi-VN') +
                                        '₫' }}
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="border-top p-2 d-flex justify-content-end align-items-center gap-2">
                        <p class="fs-14 text-grey fw-medium m-0">Tổng số tiền: </p>
                        <p class="text-end m-0 fs-16 fw-medium text-color">{{
                            shopTotalMap.map[shop.shop_id].total.toLocaleString('vi-VN') + ' ₫' }}
                        </p>
                    </div>
                </div>
            </div>
            <!-- box buy -->
            <div class="col-lg-4 col-md-5 col-12">
                <div class="p-2 bg-white mb-2">
                    <div class="d-flex align-items-center justify-content-between ">
                        <p class="m-0 fs-16 fw-semibold">
                            Chọn phương thức thanh toán
                        </p>
                    </div>
                    <div class="d-flex flex-column gap-2 mt-2">
                        <div v-if="payment_methods.length > 0" v-for="p in payment_methods"
                            class="border p-2 radius-3 ">
                            <div class="d-flex justify-content-between">
                                <div class="d-flex justify-content-between gap-1">
                                    <img :src="`/imgs/payments/${p.url}`" :alt="p.title">
                                    <p class="m-0 fs-14 fw-medium">{{ p.title }}</p>
                                </div>
                                <div class="radio-container">
                                    <input type="radio" :checked="payment_selected == p.id" v-model="payment_selected"
                                        :value="p.id" :id="`radio-${p.id}`" name="toggle" class="radio-input" />
                                    <label :for="`radio-${p.id}`" class="radio">
                                        <span class="circle"></span>
                                    </label>
                                </div>
                            </div>
                            <p class="fs-12 fw-medium text-secondary m-0 mt-2">{{ p.description }}</p>
                        </div>
                    </div>
                </div>

                <div class="cart__buy--voucher">
                    <p class="m-0">
                        <i class="ri-coupon-3-line"></i>
                        EZShop Voucher
                    </p>
                    <button class="fw-medium" @click="isModalVoucher = true" type="button">Chọn hoặc nhập mã</button>
                </div>
                <div class="bg-white">
                    <div>
                        <h3 class="fs-18 my-1 p-2">Tóm tắt đơn hàng</h3>
                        <div class="d-flex justify-content-between my-2 px-2">
                            <p class="fs-14 m-0 text-secondary">Tổng cộng</p>
                            <p class="fs-14 m-0 text-secondary">
                                {{ checkoutItems.total_checkout
                                    ? Number(checkoutItems.total_checkout).toLocaleString('vi-VN')
                                    : 0
                                }} ₫
                            </p>
                        </div>
                        <div class="d-flex justify-content-between my-2 px-2">
                            <p class="fs-14 m-0 text-secondary">Phí vận chuyển</p>
                            <p class="fs-14 m-0 text-secondary">{{ total_shipping_fee.toLocaleString('vi-VN') }} đ</p>
                        </div>
                        <div v-if="shopTotalMap.totalShippingDiscount > 0"
                            class="d-flex justify-content-between my-2 px-2">
                            <p class="fs-14 m-0 text-secondary">Giảm phí vận chuyển</p>
                            <p class="fs-14 m-0 text-secondary">-{{
                                shopTotalMap.totalShippingDiscount.toLocaleString('vi-VN') }} đ
                            </p>
                        </div>
                        <div class="d-flex justify-content-between my-2 px-2">
                            <p class="fs-14 m-0 text-secondary">Giảm giá</p>
                            <p class="fs-14 m-0 text-secondary">
                                {{ voucher_selected.platform
                                    ? '-' + (checkoutItems.total_checkout - subTotal).toLocaleString('vi-VN')
                                    : 0 }}
                                đ</p>
                        </div>
                        <div v-if="voucher_selected.platform || voucher_selected.free_shipping"
                            @click="isModalVoucher = true" class="d-flex justify-content-end my-2 px-2 cursor-pointer">
                            <div v-if="voucher_selected.platform" class="voucher-badge voucher-red">
                                {{ formatToK(checkoutItems.total_checkout - subTotal) }}
                            </div>
                            <div v-if="voucher_selected.free_shipping" class="voucher-badge voucher-green">Miễn Phí Vận
                                Chuyển</div>
                            <span><i class="ri-arrow-right-wide-line"></i></span>
                        </div>
                        <div class="d-flex justify-content-between my-2 border-top p-2">
                            <p class="fs-14 m-0 text-secondary">Tổng cộng</p>
                            <p class="fs-18 m-0 text-color fw-medium">
                                {{ finalTotal.toLocaleString('vi-VN') }} ₫
                            </p>
                        </div>
                    </div>
                    <button :disabled="loading.placeOrder" @click="placeOrder()" type="button"
                        class="main-btn w-100 py-2 d-flex justify-content-center align-items-center gap-2">
                        <loading__circle color="#fff" border="3px" size="20px" v-if="loading.placeOrder" />
                        XÁC NHẬN ĐẶT HÀNG
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div v-if="isModalVoucher"
        class="modal-background container-fluid p-0 d-flex justify-content-center align-items-center"
        style="touch-action: none">
        <div class="modal-ezeshop bg-white shadow-sm" style="max-width: 500px">
            <div class="modal-ezeshop__top d-flex justify-content-between align-items-center border-bottom p-3">
                <div class="modal-ezeshop__name fs-18 fw-500">Chọn EZShop Voucher</div>
                <div class="modal-ezeshop__cancel cursor-pointer" @click="isModalVoucher = false"><i
                        class="fa-solid fa-xmark fs-18"></i></div>
            </div>
            <!-- Content -->
            <div class="modal-ezeshop__main w-100 p-2 py-3">
                <form>
                    <div class="position-relative" style="min-height: 250px">
                        <div v-if="vouchers.freeShipping.length == 0 && vouchers.platform.length == 0"
                            class="d-flex align-items-center flex-column jutify-content-center mt-5">
                            <img src="/imgs/empty-voucher.png" style="width: 70px; object-fit: cover"
                                alt="Không có voucher">
                            <p class="text-secondary fw-medium fs-16 mt-3 text-center">Hiện tại bạn không có voucher
                                nào.
                            </p>
                        </div>
                        <div v-else class="position-absolute bg-white w-100 overflow-hidden px-2">
                            <!-- Voucher Free Ship-->
                            <div class="mb-3" v-if="vouchers.freeShipping.length > 0">
                                <div class="d-flex flex-column">
                                    <p class="fs-16 m-0 fw-semibold">Mã Miễn Phí Vận Chuyển</p>
                                    <span class="fs-14">Có thể chọn 1 voucher</span>
                                </div>
                                <!-- Voucher Item -->
                                <div v-for="freeShipping in vouchers.freeShipping" :class="{
                                    'mt-3': true,
                                    'disabled': freeShipping.min > checkoutItems.total_checkout
                                }">
                                    <div class="d-flex align-items-center gap-2 border radius-3 position-relative">
                                        <div class="box-voucher">
                                            <img src="/imgs/free-ship.png" alt="Hình ảnh">
                                            <span>Mã vận chuyển</span>
                                        </div>
                                        <div class="d-flex justify-content-center flex-column">
                                            <p class="m-0 fs-14 fw-medium">
                                                Giảm tối đa
                                                {{ Number(freeShipping.max).toLocaleString('vi-VN') + '₫' }}
                                            </p>
                                            <p class="m-0 fs-14 fw-medium">
                                                Đơn Tối Thiểu
                                                {{ Number(freeShipping.min).toLocaleString('vi-VN') + '₫' }}
                                            </p>
                                            <div class="d-flex fs-10 mt-2 gap-1">
                                                <span class="fw-medium">Sắp hết hạn:</span>
                                                <span class="fw-medium">
                                                    {{ getTimeRemaining(freeShipping.end_date) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ms-auto">
                                            <div class="radio-container me-3">
                                                <input type="radio" v-model="voucher_temp.free_shipping"
                                                    :value="freeShipping.id" :id="freeShipping.id"
                                                    :checked="voucher_selected.free_shipping == freeShipping.id"
                                                    name="freeShipping" class="radio-input" />
                                                <label :for="freeShipping.id" class="radio">
                                                    <span class="circle"></span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="voucher-quantity">
                                            <span>x{{ freeShipping.quantity }}</span>
                                        </div>
                                    </div>
                                    <div class="box-voucher__bottom">
                                        <div></div>
                                    </div>
                                </div>
                            </div>
                            <!-- Voucher Sale-->
                            <div class="mb-3" v-if="vouchers.platform.length > 0">
                                <div class="d-flex flex-column">
                                    <p class="fs-16 m-0 fw-semibold">Mã Giảm Giá</p>
                                    <span class="fs-14">Có thể chọn 1 voucher</span>
                                </div>
                                <!-- Voucher Item -->
                                <div v-for="platform in vouchers.platform" :class="{
                                    'mt-3': true,
                                    'disabled': platform.min > checkoutItems.total_checkout
                                }">
                                    <div class="d-flex align-items-center gap-2 border radius-3 position-relative">
                                        <div class="box-voucher bg-main">
                                            <img src="/imgs/ezshop.png" alt="Hình ảnh">
                                            <span>EZShop</span>
                                        </div>
                                        <div class="d-flex justify-content-center flex-column">
                                            <p class="m-0 fs-14 fw-medium">
                                                Giảm <span v-if="platform.percent">{{ platform.percent }} %</span>
                                                tối đa {{ Number(platform.max).toLocaleString('vi-VN') + '₫' }}
                                            </p>
                                            <p class="m-0 fs-14 fw-medium">Đơn Tối Thiểu
                                                {{ Number(platform.min).toLocaleString('vi-VN') + '₫' }}
                                            </p>
                                            <div class="d-flex fs-10 mt-2 gap-1">
                                                <span class="fw-medium">Sắp hết hạn:</span>
                                                <span class="fw-medium">
                                                    {{ getTimeRemaining(platform.end_time) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ms-auto">
                                            <div class="radio-container me-3">
                                                <input type="radio" v-model="voucher_temp.platform" :value="platform.id"
                                                    :id="platform.id"
                                                    :checked="voucher_selected.platform == platform.id" name="platform"
                                                    class="radio-input" />
                                                <label :for="platform.id" class="radio">
                                                    <span class="circle"></span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="voucher-quantity">
                                            <span>x{{ platform.quantity }}</span>
                                        </div>
                                    </div>
                                    <div class="box-voucher__bottom">
                                        <div class="bg-main"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-ezeshop__bottom d-flex justify-content-end w-100 align-items-center border-top p-3">
                <div class="modal-ezeshop__cancel me-4">
                    <button @click="isModalVoucher = false" class="white-btn py-2 px-4 fs-14">TRỞ LẠI</button>
                </div>
                <div class="modal-ezeshop__save">
                    <button class="main-btn py-2 px-4 fs-14" @click="confirmVoucher" type="button">Áp Dụng</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Model Voucher -->
    <div class="modal-background container-fluid d-flex justify-content-center align-items-center"
        v-if="is_modal_active">
        <div v-if="loading.editAddress" class="d-flex align-items-center justify-content-center">
            <loading__dot />
        </div>
        <div v-else class="modal-edit-address modal-ezeshop bg-white shadow-sm px-4 pt-4 pb-2">
            <div class="modal-ezeshop__top d-flex justify-content-between align-items-center">
                <div class="modal-ezeshop__name fs-18 fw-500">
                    {{ is_editing_address ? 'Cập nhật địa chỉ' : 'Thêm địa chỉ' }}
                </div>
                <div class="modal-ezeshop__cancel cursor-pointer" @click="close_modal_address"><i
                        class="fa-solid fa-xmark fs-18"></i></div>
            </div>
            <div class="scroll-wrapper">
                <div class="modal-ezeshop__main w-100">
                    <form @submit.prevent="save_address_pickup" id="form-address">
                        <div class="row g-0">
                            <div class="col-6 pe-1">
                                <span class="fs-14">Họ & tên</span>
                                <input type="text" name="" id=""
                                    class="fs-12 radius-3 w-100 outline-none border px-2 padding-t-4 padding-b-4 mt-2"
                                    v-model="pickup.name">
                                <p v-if="errors.name" class="text-danger small">
                                    {{ Array.isArray(errors.name) ? errors.name[0] : errors.name }}
                                </p>
                            </div>
                            <div class="col-6 ps-1">
                                <span class="fs-14">Số điện thoại</span>
                                <input type="text"
                                    class="fs-12 radius-3 w-100 outline-none border px-2 padding-t-4 padding-b-4 mt-2"
                                    v-model="pickup.phone">
                                <p v-if="errors.phone" class="text-danger small">
                                    {{ Array.isArray(errors.phone) ? errors.phone[0] : errors.phone }}
                                </p>
                            </div>
                        </div>
                        <div class="col-12 my-3">
                            <!-- <p class="mb-4 fs-18 fw-500">Địa chỉ</p> -->
                            <span class="fs-14">Tỉnh/Thành phố/Quận/Huyện/Phường/Xã</span>
                            <div
                                class="col-12 border radius-3 w-100 outline-none border px-2 padding-t-2 padding-b-2 d-flex justify-content-between mt-2 position-relative cursor-pointer">
                                <div class="province-district-ward d-flex fs-14 align-items-center"
                                    @click="open_address">
                                    <div class="__province">{{address.selected_province ?
                                        address.province_list.find(p =>
                                            p.ProvinceID == address.selected_province)?.ProvinceName : "Tỉnh"}}</div>
                                    <span>/</span>
                                    <div class="__district">{{address.selected_district ?
                                        address.district_list.find(d =>
                                            d.DistrictID == address.selected_district)?.DistrictName : "Quận"}}</div>
                                    <span>/</span>
                                    <div class="__ward">{{address.selected_ward ?
                                        address.ward_list.find(w => w.WardCode == address.selected_ward)?.WardName :
                                        "Huyện"}}
                                    </div>
                                </div>

                                <div class="drop-icon"><i class="fa-solid fa-chevron-down fs-12 text-grey"></i></div>

                                <div class="province-district-ward__select position-absolute bg-white w-100 shadow-sm overflow-hidden p-2"
                                    v-show="select_address">
                                    <div class="select__tab d-flex gap-1 justify-content-between">
                                        <div class="tab__item" :class="{ active: address.step === 'province' }"
                                            @click="step_address('province')">Tỉnh/Thành phố</div>
                                        <div class="tab__item" :class="{ active: address.step === 'district' }"
                                            :style="address.selected_province == null ? 'cursor: not-allowed' : 'cursor: pointer'"
                                            @click="address.selected_province !== null && step_address('district')">
                                            Quận/Huyện</div>
                                        <div class="tab__item" :class="{ active: address.step === 'ward' }"
                                            :style="address.selected_district == null ? 'cursor: not-allowed' : 'cursor: pointer'"
                                            @click="address.selected_district !== null && step_address('ward')">
                                            Phường/Xã
                                        </div>
                                    </div>
                                    <div class="select__main mt-2 d-flex flex-wrap gap-2 shadow-sm"
                                        v-if="address.step === 'province'">
                                        <div class="select__item px-3 py-2 fs-12 cursor-pointer"
                                            :class="{ active: province.ProvinceID == address.selected_province }"
                                            v-for="province in address.province_list" :key="province.ProvinceID"
                                            @click="select_province(province.ProvinceID)">{{ province.ProvinceName }}
                                        </div>
                                    </div>

                                    <div class="select__main mt-2 d-flex flex-wrap gap-2 shadow-sm"
                                        v-else-if="address.step === 'district'">
                                        <div class="select__item px-3 py-2 fs-12 cursor-pointer"
                                            :class="{ active: district.DistrictID == address.selected_district }"
                                            v-for="district in address.district_list" :key="district.DistrictID"
                                            @click="select_district(district.DistrictID)">{{ district.DistrictName }}
                                        </div>
                                    </div>
                                    <div class="select__main mt-2 d-flex flex-wrap gap-2 shadow-sm"
                                        v-else-if="address.step === 'ward'">
                                        <div class="select__item px-3 py-2 fs-12 cursor-pointer"
                                            :class="{ active: ward.WardCode == address.selected_ward }"
                                            v-for="ward in address.ward_list" :key="ward.WardCode"
                                            @click="select_ward(ward.WardCode)">
                                            {{ ward.WardName }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p v-if="errors.province" class="text-danger small">
                            {{ Array.isArray(errors.province) ? errors.province[0] : errors.province }}
                        </p>
                        <p v-if="errors.district" class="text-danger small">
                            {{ Array.isArray(errors.district) ? errors.district[0] : errors.district }}
                        </p>
                        <p v-if="errors.ward" class="text-danger small">
                            {{ Array.isArray(errors.ward) ? errors.ward[0] : errors.ward }}
                        </p>


                        <div class="col-12 mb-4">
                            <span class="fs-14">Địa chỉ chi tiết</span>
                            <textarea rows="4" class="fs-12 radius-3 w-100 outline-none border p-1 mt-2"
                                v-model="pickup.address_detail"></textarea>
                            <p v-if="errors.address_detail" class="text-danger small">
                                {{ Array.isArray(errors.address_detail) ? errors.address_detail[0] :
                                    errors.address_detail
                                }}
                            </p>
                        </div>
                    </form>
                </div>
            </div>

            <div class="modal-ezeshop__bottom d-flex justify-content-end w-100 align-items-center border-top">
                <div class="modal-ezeshop__cancel me-4">
                    <button @click="close_modal_address" class="white-btn py-2 px-4 fs-14">Huỷ</button>
                </div>
                <div class="modal-ezeshop__save">
                    <button class="main-btn py-2 px-4 fs-14" form="form-address" type="submit">{{ is_editing_address ?
                        'Lưu' : 'Thêm' }}</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal address -->
    <div v-if="is_modal_active_addresses"
        class="modal-background container-fluid d-flex justify-content-center align-items-center">

        <div class="modal-edit-address modal-ezeshop bg-white shadow-sm">
            <div class="modal-ezeshop__top d-flex justify-content-between align-items-center p-3 border-bottom">
                <div class="modal-ezeshop__name fs-18 fw-500">Địa chỉ của tôi</div>
                <div class="modal-ezeshop__cancel cursor-pointer" @click="is_modal_active_addresses = false"><i
                        class="fa-solid fa-xmark fs-18"></i></div>
            </div>
            <div class="scroll-wrapper">
                <!-- <div class="scroll-wrapper" style="max-height: calc(90vh - 120px) overflow-y: auto padding: 16px"> -->
                <div v-if="loading.editAddress" class="d-flex align-items-center justify-content-center"
                    style="min-height: 400px">
                    <loading__dot />
                </div>
                <div v-else class="modal-ezeshop__main w-100 position-relative" style="min-height: 400px">
                    <form @submit.prevent="changeAddress" class="px-3 py-2 position-absolute w-100"
                        id="form-change-address">
                        <!-- address item -->
                        <div v-for="address in addresses" class="d-flex gap-2 border-bottom py-2">
                            <div class="mt-2">
                                <div class="radio-container text-start">
                                    <input type="radio" v-model="address_order"
                                        :checked="address.address_id == display_address_pickup.address_id"
                                        :id="address.address_id" :value="address.address_id" name="toggle"
                                        class="radio-input" />
                                    <label :for="address.address_id" class="radio">
                                        <span class="circle"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="d-flex flex-column w-100">
                                <div class="d-flex justify-content-between">
                                    <div class="d-flex align-items-center gap-2">
                                        <p class="m-0 fs-16 text-black fw-medium">{{ address.name }}</p>
                                        |
                                        <span class="fs-14">{{ address.phone }}</span>
                                    </div>
                                    <p @click="open_modal_address(address.address_id)"
                                        class="text-color fw-semibold fs-14 cursor-pointer">Cập nhật</p>
                                </div>
                                <p class="fs-14 m-0 fw-medium text-secondary mt-1">{{ address.address_detail }}</p>
                                <p class="fs-14 m-0 fw-medium text-secondary">
                                    {{ address.ward }},
                                    {{ address.district }},
                                    {{ address.province }}
                                </p>
                                <div v-if="address.is_default == 1" class="text-color fs-12 mt-1">Mặc định</div>
                            </div>
                        </div>
                        <button @click="open_modal_address(null)" type="button"
                            class="btn-sm mt-2 btn btn-outline-secondary radius-2">
                            <i class="ri-add-line"></i>
                            Thêm địa chỉ mới
                        </button>
                    </form>
                </div>
            </div>

            <div class="modal-ezeshop__bottom d-flex justify-content-end w-100 align-items-center border-top p-3">
                <div class="modal-ezeshop__cancel me-4">
                    <button @click="is_modal_active_addresses = false" class="white-btn py-2 px-4 fs-14">Huỷ</button>
                </div>
                <div class="modal-ezeshop__save">
                    <button class="main-btn py-2 px-4 fs-14" form="form-change-address" type="submit">
                        Xác nhận
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal show addresses-->
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useCheckoutStore } from '@/stores/checkoutStore'
import api from '@/configs/api'
import message from '@/utils/messageState'
import loading__dot from '@/components/loading/loading__dot.vue'
import loading__circle from '@/components/loading/loading__loader-circle.vue'
import { formatToK } from '@/utils/formatPrice'
/*
    ADDRESS
*/
const select_address = ref(false)
const is_modal_active = ref(false)
const is_modal_active_addresses = ref(false)
const addresses = ref([])
const address_order = ref(null)
const is_editing_address = ref(false)
const editing_address_id = ref(null)
const address = reactive({
    province_list: [],
    district_list: [],
    ward_list: [],

    selected_province: null,
    selected_district: null,
    selected_ward: null,

    step: 'province'
})
const pickup = reactive({
    name: '',
    phone: '',
    address_detail: '',
})
const display_address_pickup = ref({
    address_id: '',
    name: '',
    phone: '',
    address_detail: '',
    province: '',
    district: '',
    ward: '',
    district_id: null,
    ward_code: null
})
const open_modal_address = async (edit_id = null) => {
    is_modal_active_addresses.value = false
    is_modal_active.value = true
    if (edit_id != null) {
        // === MODE EDIT ===
        is_editing_address.value = true
        editing_address_id.value = edit_id
        loading.value.editAddress = true

        const addressToEdit = addresses.value.find((address) => address.address_id == edit_id)

        pickup.name = addressToEdit.name
        pickup.phone = addressToEdit.phone
        pickup.address_detail = addressToEdit.address_detail

        const province = select_province(addressToEdit.province_id)
        const district = select_district(addressToEdit.district_id)
        const ward = select_ward(addressToEdit.ward_code)
        await province
        await district
        await ward
        loading.value.editAddress = false
    } else {
        // === MODE ADD ===
        is_editing_address.value = false
        editing_address_id.value = null

        // Reset form pickup
        pickup.name = ''
        pickup.phone = ''
        pickup.address_detail = ''

        // Reset địa chỉ chọn
        address.selected_province = null
        address.selected_district = null
        address.selected_ward = null

        address.district_list = []
        address.ward_list = []
        address.step = 'province'
    }


}

const close_modal_address = () => {
    is_modal_active.value = false
    is_modal_active_addresses.value = true
}
const open_address = () => select_address.value = !select_address.value
const step_address = (step) => address.step = step

const select_province = async (province) => {
    address.selected_province = province
    address.selected_district = null
    address.selected_ward = null

    const res = await api.get(`/districts/${province}`)
    address.district_list = res.data.districts

    address.step = 'district'

}

const select_district = async (district) => {
    address.selected_district = district
    address.selected_ward = null

    const res = await api.get(`/wards/${district}`)
    address.ward_list = res.data.wards

    address.step = 'ward'
}

const select_ward = (ward) => {
    address.selected_ward = ward
    select_address.value = false
}

const validate_pickup = () => {
    let is_error = true
    errors.value = {}
    if (!pickup.name.trim()) {
        errors.value.name = "Vui lòng nhập tên người gửi!"
        is_error = false
    }

    if (!pickup.phone.trim()) {
        errors.value.phone = "Vui lòng nhập số điện thoại!"
        is_error = false
    } else if (!/^0\d{9}$/.test(pickup.phone.trim())) {
        errors.value.phone = "Số điện thoại không hợp lệ!"
        is_error = false
    }

    if (!address.selected_province) {
        errors.value.province = "Vui lòng chọn tỉnh/thành!"
        is_error = false
    }

    if (!address.selected_district) {
        errors.value.district = "Vui lòng chọn quận/huyện!"
        is_error = false
    }

    if (!address.selected_ward) {
        errors.value.ward = "Vui lòng chọn phường/xã!"
        is_error = false
    }

    if (!pickup.address_detail.trim()) {
        errors.value.address_detail = "Vui lòng nhập địa chỉ chi tiết!"
        is_error = false
    } else if (pickup.address_detail.trim().length < 5) {
        errors.value.address_detail = "Địa chỉ chi tiết quá ngắn!"
        is_error = false
    }
    return is_error
}

const save_address_pickup = async () => {
    const is_error = validate_pickup()
    if (!is_error) return

    errors.value = {}

    const selectedProvince = address.province_list.find(p => p.ProvinceID == address.selected_province)
    const selectedDistrict = address.district_list.find(d => d.DistrictID == address.selected_district)
    const selectedWard = address.ward_list.find(w => w.WardCode == address.selected_ward)

    if (!selectedProvince || !selectedDistrict || !selectedWard) {
        message.emit("show-message", {
            title: "Lỗi dữ liệu",
            text: "Không thể xác định địa chỉ. Vui lòng thử lại.",
            type: "error"
        })
        return
    }
    close_modal_address()
    is_modal_active.value = false
    try {
        if (is_editing_address.value) {
            const dataEdit = {
                name: pickup.name,
                phone: pickup.phone,
                address_detail: pickup.address_detail,
                province: selectedProvince.ProvinceName,
                district: selectedDistrict.DistrictName,
                district_id: selectedDistrict.DistrictID,
                ward: selectedWard.WardName,
                ward_code: selectedWard.WardCode
            }
            const inx = addresses.value.findIndex(a => a.address_id == editing_address_id.value);

            if (inx != -1) {
                addresses.value[inx] = {
                    ...addresses.value[inx],
                    ...dataEdit
                }
            }
            await api.put(`/address/${editing_address_id.value}`, dataEdit)
        } else {
            const dataAdd = {
                name: pickup.name,
                phone: pickup.phone,
                address_detail: pickup.address_detail,
                province: selectedProvince.ProvinceName,
                province_id: selectedProvince.ProvinceID,
                district: selectedDistrict.DistrictName,
                district_id: selectedDistrict.DistrictID,
                ward: selectedWard.WardName,
                ward_code: selectedWard.WardCode
            }
            const res = await api.post('/address', dataAdd)
            if (res.status == 201) {
                dataAdd.address_id = res.data.address.id
                dataAdd.is_default = res.data.address.is_default
                addresses.value.push(dataAdd)
            }
        }
    } catch (error) {
        if (error.response?.status === 422) {
            errors.value = error.response.data.errors
        } else {
            console.error(error)
        }
    } finally {
        editing_address_id.value = null
    }
}

const checkAddress = async (check = false) => {
    const res = await api.get("/addresses")
    addresses.value = res.data.addresses || []
    if (res.status !== 200 || addresses.value.length === 0) {
        open_modal_address()
        message.emit("show-message", {
            title: "Thông báo",
            text: 'Vui lòng nhập địa chỉ để thanh toán.',
            type: "warning"
        })
        return
    }
    if (!check) {
        const selected = addresses.value.find(a => a.is_default === 1) || addresses.value[0]
        display_address_pickup.value = {
            address_id: selected.address_id,
            name: selected.name,
            phone: selected.phone,
            address_detail: selected.address_detail,
            province: selected.province,
            district: selected.district,
            district_id: selected.district_id,
            ward: selected.ward,
            ward_code: selected.ward_code,
            is_default: selected.is_default
        }
    }
}

const changeAddress = async () => {
    loading.value.fetch = true
    if (address_order.value != null) {
        display_address_pickup.value = addresses.value.find((address) => address.address_id == address_order.value)
    }
    is_modal_active_addresses.value = false
    await calculateShippingFeeAll()
    loading.value.fetch = false
}
/*
    CALCULATE SHIPPING_FEE
*/
const shipping_fees = ref([])
const calculateShippingFeeAll = async () => {
    const payload = {
        to_district_id: display_address_pickup.value.district_id,
        to_ward_code: display_address_pickup.value.ward_code,
        carts: checkoutItems.value.items
    };
    const res = await api.post('/calculate-shipping', payload);
    if (res.status == 200) {
        total_shipping_fee.value = res.data.total_fee
        shipping_fees.value = res.data.shipping_fees
    }
}

const shopTotalMap = computed(() => {
    const map = {}
    let totalShippingDiscount = 0
    let totalFinal = 0
    const voucher = vouchers.freeShipping.find(v => v.id == voucher_selected.value.free_shipping)

    if (checkoutItems.value.items && checkoutItems.value.items.length > 0) {
        for (const shop of checkoutItems.value.items) {
            const productTotal = shop.variants.reduce((sum, variant) => {
                return sum + variant.price * variant.total_quantity
            }, 0)

            const shippingFee = shipping_fees.value.find(f => f.shop_id === shop.shop_id)?.fee || 0

            // --- Áp dụng voucher freeship ---
            let shippingDiscount = 0
            if (voucher && checkoutItems.value.total_checkout >= voucher.min && total_shipping_fee.value > 0) {
                if (voucher.percent) {
                    shippingDiscount = Math.floor((voucher.percent / 100) * shippingFee)
                    shippingDiscount = Math.min(shippingDiscount, voucher.max ?? shippingDiscount)
                } else {
                    shippingDiscount = Math.floor(voucher.max)
                }
                shippingDiscount = Math.min(shippingDiscount, shippingFee)
            }

            const finalShippingFee = shippingFee - shippingDiscount
            totalShippingDiscount += shippingDiscount

            // --- Áp dụng voucher shop ---
            const shopDiscount = calculateDiscountByShop(shop.shop_id, shop.variants || [])

            const total = productTotal + finalShippingFee
            const totalAfterDiscount = total - shopDiscount

            totalFinal += totalAfterDiscount

            map[shop.shop_id] = {
                productTotal,
                shippingFee,
                shippingDiscount,
                finalShippingFee,
                shopDiscount,
                totalBeforeDiscount: total,
                total: totalAfterDiscount
            }
        }
    }

    return {
        map,
        totalShippingDiscount,
        totalFinal
    }
})

/*
  GET VOUCHER
*/
const vouchers = reactive({
    all: [],
    freeShipping: [],
    platform: [],
    shop: {}
})

const getVouchers = async () => {
    try {
        const res = await api.get(`/vouchers/active`)
        if (res.status == 200) {
            vouchers.all = res.data.vouchers
            filterVoucher()
        }
    } catch (error) {
        console.log(error)
    }
}

const filterVoucher = () => {
    vouchers.freeShipping = vouchers.all.filter(voucher => voucher.type === "free_shipping")
    vouchers.platform = vouchers.all.filter(voucher => voucher.shop_id === null && voucher.type !== "free_shipping")

    vouchers.shop = {}
    vouchers.all.forEach(voucher => {
        if (voucher.shop_id && voucher.type !== "free_shipping") {
            if (!vouchers.shop[voucher.shop_id]) {
                vouchers.shop[voucher.shop_id] = []
            }
            vouchers.shop[voucher.shop_id].push(voucher)
        }
    })
}


const getTimeRemaining = (endDate) => {
    const start = new Date()
    const end = new Date(endDate)
    const diffMs = end - start

    if (diffMs <= 0) return 'Đã hết hạn'

    const diffSec = Math.floor(diffMs / 1000)
    const days = Math.floor(diffSec / (60 * 60 * 24))
    const hours = Math.floor((diffSec % (60 * 60 * 24)) / (60 * 60))
    const minutes = Math.floor((diffSec % (60 * 60)) / 60)

    const timeParts = []
    if (days > 0) timeParts.push(`${days} ngày`)
    if (hours > 0) timeParts.push(`${hours} giờ`)
    if (minutes > 0) timeParts.push(`${minutes} phút`)

    const timeText = timeParts.join(' ')

    if (days < 1) {
        return `Sắp hết hạn: còn ${timeText}`
    }

    return `Còn ${timeText}`
}
const shopIdForVoucher = ref(null)
const getVouchersForShop = (shop_id) => {
    shopIdForVoucher.value = shop_id
    vouchers.shop[shop_id] = vouchers.all.filter(v => v.shop_id == shop_id)
    voucher_temp.value = JSON.parse(JSON.stringify(voucher_selected.value));
}

const voucher_selected = ref({
    free_shipping: null,
    platform: null,
    shop: {}
})

const voucher_temp = ref({
    free_shipping: null,
    platform: null,
    shop: {}
})

const selectPlatformVoucher = () => {
    voucher_selected.value = { ...voucher_temp.value }
    isModalVoucher.value = false;
    shopIdForVoucher.value = null;
}

const totalApplyVoucherShop = () => {
    let totalDiscount = 0
    const itemsCheckout = checkoutItems.value?.items
    if (!Array.isArray(itemsCheckout)) return 0
    itemsCheckout.forEach(item => {
        const shopId = item.shop_id
        const discount = calculateDiscountByShop(shopId, item.variants)
        totalDiscount += discount
    })
    return totalDiscount
}

const canSelectShopVoucher = (variants, min_voucher) => {
    const total = variants.reduce((sum, item) => {
        return sum + item.total_price;
    }, 0);

    return total < min_voucher;
};

const calculateDiscountByShop = (shopId, variants) => {
    const voucherId = voucher_selected.value.shop?.[shopId]
    if (!voucherId) return 0

    const shopVouchers = vouchers.shop?.[shopId]
    if (!Array.isArray(shopVouchers)) return 0

    const voucher = shopVouchers.find(v => v.id == voucherId)
    if (!voucher) return 0

    const total = variants.reduce((sum, item) => {
        return sum + item.total_price
    }, 0)

    if (total < voucher.min) return 0

    if (voucher.type == 'percentage_discount') {
        return Math.min((voucher.percent / 100) * total, voucher.max)
    }

    if (voucher.type == 'fixed_amount') {
        return Math.min(voucher.max, total)
    }

    return 0
}

const total_shipping_fee = ref(0)

const confirmVoucher = () => {
    voucher_selected.value = { ...voucher_temp.value }
    isModalVoucher.value = false
}
const subTotal = computed(() => {
    const total = checkoutItems.value.total_checkout

    const totalShopDiscount = totalApplyVoucherShop()

    const afterShopDiscount = total - totalShopDiscount;

    const selectedId = voucher_selected.value.platform;
    const platformVoucher = vouchers.platform.find(v => v.id === selectedId);

    if (!platformVoucher || afterShopDiscount < platformVoucher.min) {
        return afterShopDiscount;
    }

    if (platformVoucher.type === "fixed_amount") {
        return Math.max(afterShopDiscount - platformVoucher.max, 0);
    } else {
        const discount = afterShopDiscount * (platformVoucher.percent / 100);
        return Math.max(afterShopDiscount - Math.min(discount, platformVoucher.max), 0);
    }
});


const finalTotal = computed(() => {
    return subTotal.value + (total_shipping_fee.value - shopTotalMap.value.totalShippingDiscount)
})
/*
    GET
*/
const router = useRouter()
const checkoutItems = ref([])
onMounted(async () => {
    try {
        loading.value.fetch = true
        const store = await useCheckoutStore()
        checkoutItems.value = await store.checkoutItems
        voucher_selected.value = checkoutItems.value.voucher_selected
        if (checkoutItems.value.length === 0) {
            router.push('/gio-hang')
            message.emit("show-message", {
                title: "Thông báo",
                text: 'Không có sản phẩm nào để thanh toán.',
                type: "warning"
            })
            return
        }
        const res_payment_method = await api.get("/payment-methods")
        payment_methods.value = res_payment_method.data.payment_methods
        await checkAddress()
        calculateShippingFeeAll()
        await getVouchers()
        loading.value.fetch = false
        const res_province = await api.get("/provinces")
        address.province_list = res_province.data.provinces
    } catch (error) {
        console.error(error)
    }
})
/*
    SUPPORT
*/
const loading = ref({
    fetch: false,
    placeOrder: false,
    editAddress: false
})
const isModalVoucher = ref(false)
const errors = ref({})

/*
    CHECKOUT
*/
const prepareCheckoutData = (data) => {
    const shopMap = shopTotalMap.value.map || {}
    //Lấy ra tổng tiền giảm của voucehr shop
    const voucherShopTotal = data.reduce((sum, shop) => {
        return sum + shopMap[shop.shop_id].shopDiscount
    }, 0)
    const totalProduct = (checkoutItems.value.total_checkout) - voucherShopTotal
    const totalDiscount = totalProduct - subTotal.value
    return data.map(shop => {
        const shopInfo = shopMap[shop.shop_id]
        const productTotal = shopInfo.productTotal - shopInfo.shopDiscount
        const ratio = productTotal / totalProduct
        let platformVoucherDiscount = Math.floor(ratio * totalDiscount)
        const totalWithShipping = shopMap[shop.shop_id]?.total ?? productTotal
        return {
            shop_id: shop.shop_id,
            variants: shop.variants.map(variant => ({
                variant_id: variant.variant_id,
                quantity: variant.total_quantity
            })),
            total_amount: totalWithShipping - platformVoucherDiscount,
            amount_to_seller: productTotal,
            shipping_fee: shopMap[shop.shop_id].shippingFee
        }
    })
}

const prepareVoucherData = (data) => {
    const voucherSelected = voucher_selected.value
    const voucherData = []
    const shopMap = shopTotalMap.value.map || {}
    //Tính shipping fee
    if (voucherSelected.free_shipping) {
        data.forEach(shop => {
            const shopInfo = shopMap[shop.shop_id]
            if (voucherSelected.free_shipping && shopInfo.shippingDiscount > 0) {
                voucherData.push({
                    id: voucherSelected.free_shipping,
                    type: 'shipping',
                    shop_id: shop.shop_id,
                    discount: shopInfo.shippingDiscount
                })
            }
        })
    }
    //Tính platform voucher
    if (voucherSelected.platform) {
        const voucherShopTotal = data.reduce((sum, shop) => {
            return sum + shopMap[shop.shop_id].shopDiscount
        }, 0)
        const totalProduct = (checkoutItems.value.total_checkout) - voucherShopTotal
        const totalDiscount = totalProduct - subTotal.value

        data.forEach(shop => {
            const shopInfo = shopMap[shop.shop_id]
            const productTotal = shopInfo.productTotal - shopInfo.shopDiscount
            const ratio = productTotal / totalProduct
            const platformVoucherDiscount = Math.floor(ratio * totalDiscount)

            voucherData.push({
                id: voucherSelected.platform,
                type: 'platform',
                shop_id: shop.shop_id,
                discount: platformVoucherDiscount
            })
        })
    }
    //Tính shop voucher
    if (voucherSelected.shop) {
        Object.entries(voucherSelected.shop).forEach(([shopId, voucherId]) => {
            const shopInfo = shopMap[shopId]
            if (!shopInfo) return
            voucherData.push({
                id: voucherId,
                type: 'shop',
                shop_id: parseInt(shopId),
                discount: shopInfo.shopDiscount
            })
        })
    }
    return voucherData
}

const placeOrder = async () => {
    //Kiểm tra có địa chỉ
    if (!display_address_pickup.value.address_id) {
        message.emit("show-message", { title: "Thông báo", text: 'Chưa có địa chỉ nhận hàng.', type: "warning" })
        return
    }

    //Kiểm tra có dữ liệu để thanh toán Không
    if (checkoutItems.value.length == 0) {
        router.push('/gio-hang')
        message.emit("show-message", { title: "Thông báo", text: 'Không có sản phẩm nào để thanh toán.', type: "warning" })
        return
    }
    //Tiến hành gửi api qua backend
    try {
        loading.value.placeOrder = true
        //flag
        localStorage.setItem('order_completed', true)
        const payload = {
            orders: prepareCheckoutData(checkoutItems.value.items),
            address_id: display_address_pickup.value.address_id,
            cart_ids: checkoutItems.value.cart_ids,
            payment_method: payment_selected.value,
            vouchers: prepareVoucherData(checkoutItems.value.items)
        }
        const res = await api.post('/payment/init', payload)
        if (res.data.payment_url) {
            window.location.href = res.data.payment_url
        } else {
            await api.post("/rank/update-user-rank");
            router.push(`/hoan-tat-dat-hang?success=${res.data.success}`)
        }
    } catch (error) {
        console.log(error)
    } finally {
        loading.value.placeOrder = false
    }
}

/*
    PAYMENT METHOD
*/
const payment_methods = ref([])
const payment_selected = ref(1)
</script>
<style scoped>
.skeleton-box {
    background-color: #e0e0e0;
    border-radius: 4px;
    animation: pulse 1.5s infinite ease-in-out;
}

@keyframes pulse {
    0% {
        background-color: #e0e0e0;
    }

    50% {
        background-color: #f0f0f0;
    }

    100% {
        background-color: #e0e0e0;
    }
}
</style>