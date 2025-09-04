<template>
  <div class="container position-relative">
    <div v-if="is_loading" class="row g-2">
      <div class="col-lg-8 col-md-7 col-12">
        <div class="bg-white p-2 mb-2 sticky-top">
          <div class="skeleton skeleton-text" style="width: 120px"></div>
        </div>
        <div v-for="n in 5" :key="n" class="cart__item bg-white mb-2 p-2">
          <div class="d-flex align-items-center border-bottom pb-2 mb-2">
            <div class="skeleton skeleton-text" style="width: 100px"></div>
          </div>
          <div class="row g-0">
            <div class="col-4">
              <div class="skeleton skeleton-img"></div>
            </div>
            <div class="col-8 px-2">
              <div class="skeleton skeleton-text" style="width: 80%"></div>
              <div class="skeleton skeleton-text" style="width: 60%"></div>
              <div class="skeleton skeleton-text" style="width: 40%"></div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-4 col-md-5 col-12">
        <div class="bg-white p-3">
          <div class="skeleton skeleton-text" style="width: 50%"></div>
          <div class="skeleton skeleton-text" style="width: 80%"></div>
          <div class="skeleton skeleton-text" style="width: 60%"></div>
          <div class="skeleton skeleton-btn mt-3"></div>
        </div>
      </div>
    </div>
    <template v-else>
      <div class="row g-2" v-if="carts.length > 0">
        <div class="col-lg-8 col-md-7 col-12">
          <div class="d-flex justify-content-between align-items-center bg-white p-2 mb-2 sticky-top">
            <div class="d-flex align-items-center">
              <div class="checkbox">
                <label class="checkbox-label">
                  <input type="checkbox" v-model="isAllSelected" />
                  <div class="checkbox-wrapper">
                    <div class="checkbox-bg"></div>
                    <svg fill="none" viewBox="0 0 24 24" class="checkbox-icon">
                      <path stroke-linejoin="round" stroke-linecap="round" stroke-width="3" stroke="currentColor"
                        d="M4 12L10 18L20 6" class="check-path"></path>
                    </svg>
                  </div>
                </label>
              </div>
              <p class="m-0 fs-12 fw-semibold text-secondary ms-1">CHỌN TẤT CẢ</p>
            </div>
            <div class="d-flex align-items-center">
              <i class="text-secondary fa-solid fa-trash"></i>
              <a class="ms-1 fs-12 fw-semibold text-secondary text-decoration-none cursor-pointer"
                @click="confirmDelete()">Xóa bỏ</a>
            </div>
          </div>
          <!-- cart item -->
          <div v-for="cart in carts" :key="cart.shop_id" class="cart__item bg-white mb-2">
            <div class="d-flex align-items-center bg-white p-2 border-bottom">
              <div class="checkbox">
                <label class="checkbox-label">
                  <input type="checkbox" v-model="shopSelected(cart).value" />
                  <div class="checkbox-wrapper">
                    <div class="checkbox-bg"></div>
                    <svg fill="none" viewBox="0 0 24 24" class="checkbox-icon">
                      <path stroke-linejoin="round" stroke-linecap="round" stroke-width="3" stroke="currentColor"
                        d="M4 12L10 18L20 6" class="check-path"></path>
                    </svg>
                  </div>
                </label>
              </div>
              <div class="d-flex align-items-center ms-2">
                <i class="fs-10 text-secondary fa-solid fa-store"></i>
                <p class="m-0 fs-14 ms-1">{{ cart.shop_name }}</p>
              </div>
            </div>
            <!-- cart item detail -->
            <template v-if="cart.variants.length > 0" v-for="variant in cart.variants">
              <div class="row g-0 px-2 py-3">
                <div class="col-xl-2 col-lg-3 col-md-4 col-sm-3 col-4">
                  <div class="d-flex align-items-center">
                    <div class="checkbox me-2">
                      <label class="checkbox-label">
                        <input type="checkbox" v-model="selectedItems" :value="variant.cart_id" />
                        <div class="checkbox-wrapper">
                          <div class="checkbox-bg"></div>
                          <svg fill="none" viewBox="0 0 24 24" class="checkbox-icon">
                            <path stroke-linejoin="round" stroke-linecap="round" stroke-width="3" stroke="currentColor"
                              d="M4 12L10 18L20 6" class="check-path"></path>
                          </svg>
                        </div>
                      </label>
                    </div>
                    <img :src="`/imgs/products/${variant.image}`" :alt="variant.product_name" class="cart__item-img" />
                  </div>
                </div>
                <div class="col-xl-10 col-lg-9 col-md-8 col-sm-9 col-8">
                  <div class="row g-1">
                    <div class="col-12 col-xl-5">
                      <span class="m-0 fs-14 text-black pe-2 cart__item--variant-name">{{ variant.product_name }}</span>
                      <div class="position-relative">
                        <p @click="!activeVariantId ? getVariantProId(variant.product_id, variant) : activeVariantId = null"
                          :class="activeVariantId && activeVariantId == variant.variant_id ? 'active' : ''"
                          class="cart__item--variant fs-12 text-secondary m-0">
                          {{ variant.full_name_variant }}</p>
                        <div v-if="activeVariantId === variant.variant_id" class="cart__item--variant--option">
                          <div class="mb-3 variant-button" v-for="attr in all_variant_pro">
                            <p>{{ attr.name }}:</p>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                              <button type="button" v-for="value in attr.values"
                                @click="selectedAttribute[attr.name] = value.id" :class="{
                                  'active_variant': selectedAttribute[attr.name] == value.id,
                                  'disabled': isDuplicateVariantInCart({ ...selectedAttribute, [attr.name]: value.id }, variant.product_id)
                                }"
                                :disabled="isDuplicateVariantInCart({ ...selectedAttribute, [attr.name]: value.id }, variant.product_id)">
                                {{ value.value }}
                                <div v-if="selectedAttribute[attr.name] == value.id" class="product-variation__tick">
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
                          <div class="border-top d-flex align-items-center justify-content-end gap-2 p-2">
                            <button type="button" @click="activeVariantId = null" class="white-btn py-1 px-2 fs-14">TRỞ
                              LẠI</button>
                            <button type="button" @click="confirmUpdateCart(variant.cart_id, variant.product_id)"
                              class="main-btn py-1 px-2 fs-14">XÁC
                              NHẬN</button>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-12 col-lg-6 col-xl-4">
                      <div class="d-flex align-items-center gap-1">
                        <p class="m-0 fs-14 text-dark fw-medium">
                          {{ Number(variant.price).toLocaleString('vi-VN') + ' ₫' }}
                        </p>
                        <span>-</span>
                        <p class="m-0 fs-14 text-color fw-medium">
                          {{ Number(variant.total_price).toLocaleString('vi-VN') + ' ₫' }}
                        </p>
                      </div>
                      <div class="d-flex">
                        <a href="#" class="no-underline">
                          <i class="text-secondary fa-regular fa-heart"></i>
                        </a>
                        <a class="no-underline cursor-pointer" @click="confirmDelete(variant.cart_id)">
                          <i class="ms-2 text-secondary fa-solid fa-trash"></i>
                        </a>
                      </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-12 col-lg-6 col-xl-3 d-flex justify-content-end">
                      <div :class="{
                        'cart__item-quanity': true,
                        'disabled': disabled_ref && ref_cart_id == variant.cart_id
                      }">
                        <button type="button" @click="decreaseQuantity(variant)">
                          <i class="fs-12 text-secondary fa-solid fa-minus"></i>
                        </button>
                        <input type="text" name="quantity" readonly :value="variant.total_quantity" />
                        <button type="button" @click="increaseQuantity(variant)">
                          <i class="fs-12 text-secondary fa-solid fa-plus"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </template>
            <div class="border-top p-3">
              <div class="d-flex fs-14 fw-medium gap-2">
                <p class="d-flex gap-2 text-color">
                  <i class="ri-coupon-3-line"></i>
                  Giảm {{ formatToK(calculateDiscountByShop(cart.shop_id, cart.variants)) }}
                </p>
                <div class="position-relative">
                  <p class="cursor-pointer" @click="getVouchersForShop(cart.shop_id)">Xem thêm voucher</p>
                  <div v-if="shopIdForVoucher == cart.shop_id" class="cart__voucher-shop">
                    <div class="modal-ezeshop border bg-white shadow-sm" style="width: 350px;">
                      <div
                        class="modal-ezeshop__top d-flex justify-content-between align-items-center border-bottom p-3">
                        <div class="modal-ezeshop__name fs-18 fw-500">{{ cart.shop_name }} Voucher</div>
                        <div @click="shopIdForVoucher = null" class="modal-ezeshop__cancel cursor-pointer">
                          <i class="fa-solid fa-xmark fs-18"></i>
                        </div>
                      </div>
                      <!-- Content -->
                      <div class="modal-ezeshop__main w-100 px-2 py-0">
                        <form>
                          <div class="position-relative" style="min-height: 200px">
                            <div v-if="vouchers.shop[cart.shop_id].length == 0"
                              class="d-flex align-items-center justify-content-center flex-column mt-5">
                              <img src="/imgs/empty-voucher.png" style="width: 70px; object-fit: cover"
                                alt="Không có voucher">
                              <p class="text-secondary fw-medium fs-16 mt-3 text-center">
                                Hiện tại shop không có voucher nào.
                              </p>
                            </div>
                            <div v-else class="position-absolute bg-white w-100 overflow-hidden px-2">
                              <!-- Voucher Sale-->
                              <div class="mb-3" v-if="vouchers.shop[cart.shop_id]">
                                <!-- Voucher Item -->
                                <div v-for="shop in vouchers.shop[cart.shop_id]" :class="{
                                  'mt-3': true,
                                  'disabled': selectedItems.length == 0
                                    || canSelectShopVoucher(cart.variants, shop.min)
                                    || getTimeRemaining(shop.end_date) == 'Đã hết hạn'
                                }">
                                  <div class="d-flex align-items-center gap-2 border radius-3 position-relative">
                                    <div class="box-voucher bg-main" style="min-width: 70px; min-height: 70px;">
                                      <img src="/imgs/ezshop.png" alt="Hình ảnh">
                                      <span>EZShop</span>
                                    </div>
                                    <div class="d-flex justify-content-center flex-column">
                                      <p class="m-0 fs-12 fw-medium">
                                        Giảm
                                        <span v-if="shop.percent">{{ shop.percent }} %</span>
                                        tối đa
                                        {{ Number(shop.max).toLocaleString('vi-VN') + '₫' }}
                                      </p>
                                      <p class="m-0 fs-12 fw-medium">Đơn Tối Thiểu
                                        {{ Number(shop.min).toLocaleString('vi-VN') + '₫' }}
                                      </p>
                                      <div class="d-flex fs-10 mt-2 gap-1">
                                        <span class="fw-medium">
                                          {{ getTimeRemaining(shop.end_date) }}
                                        </span>
                                      </div>
                                    </div>
                                    <div class="ms-auto">
                                      <div class="radio-container me-3">
                                        <input type="radio" @change="voucher_temp.shop[cart.shop_id] = shop.id"
                                          :value="shop.id" :id="shop.id"
                                          :checked="voucher_selected.shop[cart.shop_id] == shop.id" class="radio-input"
                                          name="discount" />
                                        <label :for="shop.id" class="radio">
                                          <span class="circle"></span>
                                        </label>
                                      </div>
                                    </div>
                                    <div class="voucher-quantity">
                                      <span>x{{ shop.quantity }}</span>
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
                          <button @click="shopIdForVoucher = null" class="white-btn py-2 px-4 fs-14">TRỞ LẠI</button>
                        </div>
                        <div :class="{
                          'modal-ezeshop__save': true,
                          'disabled': selectedItems.length == 0
                        }">
                          <button class="main-btn py-2 px-4 fs-14" @click="selectPlatformVoucher" type="button">Áp
                            Dụng</button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- box buy -->
        <div class="col-lg-4 col-md-5 col-12">
          <div class="cart__buy--voucher">
            <p class="m-0">
              <i class="ri-coupon-3-line"></i>
              EZShop Voucher
            </p>
            <button @click="isModalVoucher = true" type="button">Chọn hoặc nhập mã</button>
          </div>
          <div class="bg-white">
            <div>
              <h3 class="fs-18 my-1 p-2">Tóm tắt đơn hàng</h3>
              <div class="d-flex justify-content-between my-2 px-2">
                <p class="fs-14 m-0 text-secondary">Tổng cộng <span>({{ selectedItems.length }} mục)</span></p>
                <p class="fs-14 m-0 text-secondary">
                  {{ totalSelected ? Number(totalSelected).toLocaleString('vi-VN') : 0 }} ₫
                </p>
              </div>
              <div v-if="voucher_selected.platform || voucher_selected.free_shipping" @click="isModalVoucher = true"
                class="d-flex justify-content-end my-2 px-2 cursor-pointer">
                <div v-if="voucher_selected.platform" class="voucher-badge voucher-red">
                  {{ formatToK(totalSelected - totalFinal) }}
                </div>
                <div v-if="voucher_selected.free_shipping" class="voucher-badge voucher-green">Miễn Phí Vận Chuyển</div>
                <span><i class="ri-arrow-right-wide-line"></i></span>
              </div>

              <div class="d-flex justify-content-between my-2 border-top p-2">
                <p class="fs-14 m-0 text-secondary">Tổng cộng</p>
                <p class="fs-18 m-0 text-color fw-medium">
                  {{ totalFinal ? Number(totalFinal).toLocaleString('vi-VN') : 0 }} ₫
                </p>
              </div>
            </div>
            <button type="button" class="main-btn w-100 py-2" @click="handleCheckout()">
              XÁC NHẬN MUA HÀNG
            </button>
          </div>
        </div>
      </div>
      <div v-else class="d-flex align-items-center justify-content-center flex-column" style="height: 60vh">
        <img style="width: 120px" src="/imgs/empty-cart.png" alt="Giỏ Hàng Trống">
        <p class="my-2 fs-14 fw-bold text-secondary">Giỏ hàng của bạn còn trống</p>
        <router-link :to="{
          name: 'home'
        }" class="fs-18 text-white btn bg-main radius-2 px-5 py-2">MUA NGAY</router-link>
      </div>
    </template>
    <div class="my-3">
      <h3 v-if="products_related.length > 0" class="fs-16 text-secondary">CÓ THỂ BẠN CŨNG THÍCH</h3>
      <!-- product -->
      <skeleton-product v-if="loading_product_related" :isLoading="loading_product_related" />
      <div v-else class="row g-2">
        <div v-for="p in products_related" class="col-6 col-md-3 col-xl-2">
          <productItem :product="p" />
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
      <div class="modal-ezeshop__main w-100 p-3">
        <form>
          <div class="position-relative" style="min-height: 250px">
            <div v-if="vouchers.freeShipping.length == 0 && vouchers.platform.length == 0"
              class="d-flex align-items-center flex-column jutify-content-center mt-5">
              <img src="/imgs/empty-voucher.png" style="width: 70px; object-fit: cover" alt="Không có voucher">
              <p class="text-secondary fw-medium fs-16 mt-3 text-center">Hiện tại bạn không có voucher nào.</p>
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
                  'disabled': selectedItems.length == 0 || freeShipping.min > totalSelected
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
                        <span class="fw-medium">
                          {{ getTimeRemaining(freeShipping.end_date) }}
                        </span>
                      </div>
                    </div>
                    <div class="ms-auto">
                      <div class="radio-container me-3">
                        <input type="radio" v-model="voucher_temp.free_shipping" :value="freeShipping.id"
                          :id="freeShipping.id" :checked="voucher_selected.free_shipping == freeShipping.id"
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
                  'disabled': selectedItems.length == 0
                    || platform.min > totalSelected
                    || getTimeRemaining(platform.end_date) == 'Đã hết hạn'
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
                        <!--  -->
                      </p>
                      <p class="m-0 fs-14 fw-medium">Đơn Tối Thiểu
                        {{ Number(platform.min).toLocaleString('vi-VN') + '₫' }}
                      </p>
                      <div class="d-flex fs-10 mt-2 gap-1">
                        <span class="fw-medium">
                          {{ getTimeRemaining(platform.end_date) }}
                        </span>
                      </div>
                    </div>
                    <div class="ms-auto">
                      <div class="radio-container me-3">
                        <input type="radio" v-model="voucher_temp.platform" :value="platform.id" :id="platform.id"
                          :checked="voucher_selected.platform == platform.id" name="platform" class="radio-input" />
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
        <div :class="{
          'modal-ezeshop__save': true,
          'disabled': selectedItems.length == 0
        }">
          <button class="main-btn py-2 px-4 fs-14" @click="selectPlatformVoucher" type="button">Áp Dụng</button>
        </div>
      </div>
    </div>
  </div>
  <!-- End Model Voucher -->
  <div v-if="isModalDelete"
    class="modal-background container-fluid p-0 d-flex justify-content-center align-items-center">
    <div class=" modal-ezeshop bg-white shadow-sm px-4 pt-4 pb-2" style="max-width: 500px">
      <div class="modal-ezeshop__top d-flex justify-content-between align-items-center">
        <div class="modal-ezeshop__name fs-18 fw-500">Xác Nhận Xóa Sản Phẩm Khỏi Giỏ Hàng</div>
        <div @click="closeModelDelete" class="modal-ezeshop__cancel cursor-pointer"><i
            class="fa-solid fa-xmark fs-18"></i></div>
      </div>
      <!-- Content -->
      <div class="modal-ezeshop__main w-100">
        Bạn có chắc muốn xóa không?
      </div>
      <div class="modal-ezeshop__bottom d-flex justify-content-end w-100 align-items-center border-top">
        <div class="modal-ezeshop__cancel me-4"><button @click="closeModelDelete"
            class="white-btn py-2 px-4 fs-14">Huỷ</button></div>
        <div class="modal-ezeshop__save">
          <button type="button" class="main-btn py-2 px-4 fs-14" @click="deleteCart()">
            Xác Nhận
          </button>
        </div>
      </div>
    </div>
  </div>
  <!-- End Model Confirm Delete-->
</template>
<script setup>
import { computed, onMounted, ref, inject, reactive } from 'vue'
import api from '@/configs/api'
import message from '@/utils/messageState'
import { useCheckoutStore } from '@/stores/checkoutStore'
import { useRouter, useRoute } from 'vue-router'
import { formatToK } from '@/utils/formatPrice'
import productItem from '@/components/productItem.vue'
import skeletonProduct from '@/components/skeletonProduct.vue'
import useGoTo from '@/utils/useGoto'
const { goTo } = useGoTo()
onMounted(async () => {
  const token = localStorage.getItem('token')
  if (!token) {
    message.emit("show-message", { title: "Thông báo", text: 'Vui lòng đăng nhập để sử dụng chức năng này!', type: "warning" })
    goTo('/dang-nhap')
    return
  }
  await getCarts()
  getVouchers()
})
/*
  SUPPORT
*/
const router = useRouter()
const route = useRoute()
const is_loading = ref(false)
const loading_product_related = ref(false)
const isModalVoucher = ref(false)
const variant_attribute_group = ref([])
const all_variant = ref([])
const all_variant_pro = ref([])
const activeVariantId = ref(null)
const selectedAttribute = ref({})
const disabled_ref = ref(false)
const cartUpdated = inject('cartUpdated')
const products_related = ref([])
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
  vouchers.platform = vouchers.all.filter(voucher => voucher.shop_id === null && voucher.type != "free_shipping")
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
};

const totalApplyVoucherShop = () => {
  let totalDiscount = 0

  carts.value.forEach(cart => {
    const shopId = cart.shop_id

    const isShopSelected = cart.variants.some(variant =>
      selectedItems.value.includes(variant.cart_id)
    )

    if (isShopSelected) {
      const discount = calculateDiscountByShop(shopId, cart.variants)
      totalDiscount += discount
    }
  })

  return totalDiscount
}

const totalFinal = computed(() => {
  let total = totalSelected.value
  const totalShopDiscount = totalApplyVoucherShop()

  let totalAfterShopDiscount = total - totalShopDiscount

  const selectedId = voucher_selected.value.platform
  const voucher = vouchers.platform.find(v => v.id === selectedId)

  if (!voucher || totalAfterShopDiscount < voucher.min) {
    return totalAfterShopDiscount
  }

  if (voucher.type === "fixed_amount") {
    return Math.max(totalAfterShopDiscount - voucher.max, 0)
  } else {
    const discount = totalAfterShopDiscount * (voucher.percent / 100)
    return Math.max(totalAfterShopDiscount - Math.min(discount, voucher.max), 0)
  }
})

const canSelectShopVoucher = (variants, min_voucher) => {
  if (!variants || variants.length === 0) return true;
  const cartIds = variants.map(c => c.cart_id);
  const cartSelected = selectedItems.value;

  const hasSelectedItem = cartIds.some(id => cartSelected.includes(id));
  if (!hasSelectedItem) {
    return true;
  }

  const total = variants.reduce((sum, item) => {
    if (cartSelected.includes(item.cart_id)) {
      return sum + item.total_price;
    }
    return sum;
  }, 0);

  return total < min_voucher;
};

const calculateDiscountByShop = (shopId, variants) => {
  const voucherId = voucher_selected.value.shop?.[shopId];

  if (!voucherId) return 0

  const voucher = vouchers.shop[shopId].find(v => v.id == voucherId)

  if (!voucher) return 0

  const cartSelected = selectedItems.value;
  const total = variants.reduce((sum, item) => {
    if (cartSelected.includes(item.cart_id)) {
      return sum + item.total_price;
    }
    return sum;
  }, 0);

  if (total < voucher.min) return 0
  if (voucher.type == 'percentage_discount') {
    return Math.min((voucher.percent / 100) * total, voucher.max);
  }
  if (voucher.type == 'fixed_amount') {
    return Math.min(voucher.max, total);
  }
  return 0
}

/*
  GET
*/
const carts = ref([])
const getCarts = async () => {
  try {
    is_loading.value = true
    const res = await api.get(`/carts/user`)
    const passedCartId = route.query.cart_id
    if (res.status == 200) {
      carts.value = res.data.carts
      variant_attribute_group.value = res.data.variantAttributeGrouped
      all_variant.value = res.data.all_variant
      if (passedCartId) {
        selectedItems.value = [...selectedItems.value, Number(passedCartId)]
      }
      router.replace({
        query: {
          ...route.query,
          cart_id: undefined
        }
      })
      is_loading.value = false

      fetchRelatedProducts()
    }
  } catch (error) {
    message.emit('show-message', {
      title: 'Lỗi tải giỏ hàng',
      text: error?.response?.data?.message || 'Không thể tải giỏ hàng. Vui lòng thử lại.',
      type: 'error'
    })
  } finally {
    is_loading.value = false
  }
}

const getVariantProId = (pro_id, variant) => {
  selectedAttribute.value = {}
  activeVariantId.value = variant.variant_id
  all_variant_pro.value = variant_attribute_group.value[pro_id]

  selectedAttribute.value = variant.selected_attribute
}

const fetchRelatedProducts = async () => {
  const categoryIds = new Set()
  const productIds = new Set()

  carts.value.forEach(shop => {
    shop.variants.forEach(item => {
      categoryIds.add(item.category_id)
      productIds.add(item.product_id)
    })
  })
  if (categoryIds.size === 0 || productIds.size === 0) {
    return
  }
  try {
    loading_product_related.value = true
    const res = await api.get(`products/related`, {
      params: {
        category_ids: Array.from(categoryIds),
        product_ids: Array.from(productIds)
      }
    })
    products_related.value = res.data
  } catch (error) {
    console.log(error);
  } finally {
    loading_product_related.value = false
  }
}

/*
  EDIT
*/
const ref_cart_id = ref(null)
const updateCart = async (id, data) => {
  disabled_ref.value = true
  try {
    const res = await api.put(`/carts/${id}`, data)
    if (res.status == 200) {
      disabled_ref.value = false
      cartUpdated.value++
    }
  } catch (error) {
    message.emit('show-message', {
      title: 'Lỗi cập nhật',
      text: error?.response?.data?.message || 'Không thể cập nhật giỏ hàng.',
      type: 'error'
    })
  }
}
const confirmUpdateCart = (cart_id, product_id) => {
  activeVariantId.value = null
  const allVariants = carts.value.flatMap(shop => shop.variants)
  const cartUpdate = allVariants.find(v => v.cart_id === cart_id)

  if (!cartUpdate) return console.warn("Không tìm thấy cart cần sửa")

  const variantsByProduct = all_variant.value[product_id]
  const selectedAttrs = selectedAttribute.value
  const matchedVariant = variantsByProduct.find(v => {
    return Object.entries(selectedAttrs).every(([key, val]) => v.selected_attribute[key] == val)
  })

  if (matchedVariant) {
    for (const key in matchedVariant) {
      if (key != 'selected_attribute') {
        cartUpdate[key] = matchedVariant[key]
      }
    }
    const variant_id = matchedVariant['variant_id']
    updateCart(cart_id, {
      'variant_id': variant_id
    })
  }
}
const isDuplicateVariantInCart = (fakeSelectedAttrs, product_id) => {
  const allVariantsInCart = carts.value.flatMap(shop => shop.variants);
  const otherVariants = allVariantsInCart.filter(v => v.variant_id !== activeVariantId.value && v.product_id == product_id);
  if (!otherVariants) return true
  return otherVariants.some(v => {
    return Object.entries(fakeSelectedAttrs).every(([key, val]) => v.selected_attribute[key] == val);
  });
};

const increaseQuantity = async (variant) => {
  ref_cart_id.value = variant.cart_id
  disabled_ref.value = true
  if (variant.total_quantity < variant.stock) {
    variant.total_quantity++
    variant.total_price = variant.total_quantity * Number(variant.price);
    await updateCart(variant.cart_id, {
      quantity: variant.total_quantity
    })
  } else {
    message.emit('show-message', {
      title: 'Cảnh báo',
      text: 'Lượng hàng trong kho không đủ',
      type: 'warning'
    })
  }
  disabled_ref.value = false
  ref_cart_id.value = null
}

const decreaseQuantity = async (variant) => {
  ref_cart_id.value = variant.cart_id
  disabled_ref.value = true
  if (variant.total_quantity > 1) {
    variant.total_quantity--
    variant.total_price = variant.total_quantity * Number(variant.price);
    await updateCart(variant.cart_id, {
      quantity: variant.total_quantity
    })

  } else {
    message.emit('show-message', {
      title: 'Cảnh báo',
      text: 'Số lượng phải lớn hơn 1.',
      type: 'warning'
    })
  }
  disabled_ref.value = false
  ref_cart_id.value = null
}

/* 
  DELETE  
*/
const isModalDelete = ref(false)
const idCartToDelete = ref(null)
const confirmDelete = (cart_id = null) => {
  isModalDelete.value = true
  idCartToDelete.value = cart_id
}
const closeModelDelete = () => {
  isModalDelete.value = false
  idCartToDelete.value = null
}
const deleteCart = async () => {
  isModalDelete.value = false
  const payload = idCartToDelete.value ? [idCartToDelete.value] : selectedItems.value
  if (Array.isArray(payload) && payload.length > 0) {

    carts.value = carts.value.map(shop => {
      return {
        ...shop,
        variants: shop.variants.filter(variant => !payload.includes(variant.cart_id))
      }
    }).filter(shop => shop.variants.length > 0)

    try {
      const res = await api.delete(`/carts`, {
        params: {
          cartIds: payload
        }
      })
      if (res.status == 200) {
        cartUpdated.value++
      }
    } catch (error) {
      console.log(error)
    } finally {
      idCartToDelete.value = null
    }
  } else {
    message.emit('show-message', {
      title: 'Cảnh báo',
      text: 'Không có sản phẩm nào để xóa.',
      type: 'warning'
    })

  }
}

/*
  SEND BUY
*/
const selectedItems = ref([])
const isAllSelected = computed({
  get() {
    const allIds = carts.value.flatMap(shop => shop.variants.map(v => v.cart_id))
    return allIds.length > 0 && allIds.every(id => selectedItems.value.includes(id))
  },
  set(value) {
    if (value) {
      selectedItems.value = carts.value.flatMap(shop => shop.variants.map(v => v.cart_id))
    } else {
      selectedItems.value = []
    }
  }
})
const shopSelected = (shop) => computed({
  get() {
    const shopIds = shop.variants.map(v => v.cart_id)
    return shopIds.every(id => selectedItems.value.includes(id))
  },
  set(value) {
    const shopIds = shop.variants.map(v => v.cart_id)
    if (value) {
      selectedItems.value = [...new Set([...selectedItems.value, ...shopIds])]
    } else {
      selectedItems.value = selectedItems.value.filter(id => !shopIds.includes(id))
    }
  }
})
const totalSelected = computed(() => {
  let total = 0
  for (const cart of carts.value) {
    for (const variant of cart.variants) {
      if (selectedItems.value.includes(variant.cart_id)) {
        total += variant.total_price
      }
    }
  }
  return total
})

const store = useCheckoutStore()
const handleCheckout = () => {
  if (selectedItems.value.length == 0) {
    message.emit('show-message', {
      title: 'Cảnh báo',
      text: 'Vui lòng chọn sản phẩm để thanh toán.',
      type: 'warning'
    })
    return
  }
  const itemsToCheckout = carts.value
    .map(shop => {
      const selectedVariants = shop.variants.filter(variant => selectedItems.value.includes(variant.cart_id))

      if (selectedVariants.length > 0) {
        return {
          ...shop,
          variants: selectedVariants,
        }
      }
      return null
    })
    .filter(shop => shop !== null)
  store.checkoutItems = {
    items: itemsToCheckout,
    total_checkout: totalSelected.value,
    voucher_selected: voucher_selected.value,
    cart_ids: selectedItems.value
  }
  router.push('/thanh-toan')
}
</script>
<style scoped>
.skeleton {
  background-color: #e2e5e7;
  background-image: linear-gradient(90deg, #e2e5e7 0px, #f8f8f8 40px, #e2e5e7 80px);
  background-size: 600px;
  animation: shimmer 1.2s infinite linear;
  border-radius: 4px;
}

@keyframes shimmer {
  0% {
    background-position: -600px 0;
  }

  100% {
    background-position: 600px 0;
  }
}

.skeleton-text {
  height: 16px;
  margin-bottom: 8px;
}

.skeleton-img {
  width: 100%;
  height: 80px;
}

.skeleton-btn {
  height: 36px;
  width: 100%;
}
</style>