<template>
    <footer class="container-fluid p-0 d-none d-lg-block">
        <div class="footer">
            <div class="container pt-3 mt-5">
                <div class="row">
                    <div class="footer__information col-xl-3 col-lg-4 col-sm-6 fs-14">
                        <a href="" class="logo">
                            <img :src="`/imgs/${logo}`" alt="" style="width: 200px; height: 70; object-fit: cover;" />
                        </a>

                        <p class="information__number mt-2">
                            Tổng đài CSKH: <span>{{ phone }}</span>
                        </p>

                        <p class="information__email mt-2">Email: {{ email }}</p>

                        <div class="footer__list-social mt-2">
                            <p class="m-0 fs-5 fw-semibold">Kết nối với chúng tôi</p>
                            <div class="d-flex">
                                <a :href="`${facebook}`"><i class="fa-brands fa-square-facebook fs-3 me-3"></i></a>
                                <a :href="`${twitter}`"><i class="fa-brands fa-twitter fs-3 me-3"></i></a>
                                <a :href="`${tiktok}`"><i class="fa-brands fa-tiktok fs-3 me-3"></i></a>
                                <a :href="`${youtube}`"><i class="fa-brands fa-youtube fs-3 me-3"></i></a>
                                <a :href="`${instagram}`"><i class="fa-brands fa-instagram fs-3"></i></a>
                            </div>
                        </div>
                    </div>
                    <!-- End information -->

                    <div class="footer__support-customers col-xl-3 col-lg-4 col-sm-6 fs-14">
                        <p class="fs-5 fw-semibold mt-3">Hỗ trợ khách hàng</p>
                        <a href="">
                            <p>Chính sách đổi hàng và bảo hành</p>
                        </a>
                        <a href="">
                            <p>Chính sách Membership</p>
                        </a>
                        <a href="">
                            <p>Chính sách ưu đãi sinh nhật</p>
                        </a>
                        <a href="">
                            <p>Chính sách bảo mật</p>
                        </a>
                        <a href="">
                            <p>Chính sách giao hàng</p>
                        </a>
                    </div>
                    <!-- End support-customers -->

                    <div class="footer__payment-shipping-methods col-xl-3 col-lg-4 col-sm-6 fs-14">
                        <p class="fs-5 fw-semibold mt-3">Thanh toán</p>
                        <div class="payments">
                            <div class="d-flex mb-10 gap-2">
                                <img src="/imgs/payments/pay-code.png" alt="Thanh toán CODE" />
                                <img src="/imgs/payments/pay-momo.png" alt="Thanh toán MOMO" />
                                <img src="/imgs/payments/pay-vnpay.png" alt="Thanh toán VNPAY" />
                            </div>
                        </div>

                        <p class="fs-5 fw-semibold mt-3">Đơn vị vận chuyển</p>

                        <div class="shippings">
                            <div class="d-flex mb-10">
                                <img src="/imgs/ghn.jpg" alt="Giao hàng GHN" width="25" />
                            </div>

                        </div>
                    </div>

                    <!-- End store's system -->

                    <div class="footer__about-us col-xl-3 col-lg-4 col-sm-6 fs-14">
                        <p class="fs-5 fw-semibold mt-3">Ezshop Việt Nam</p>
                        <a href="#">
                            <p>Về Ezshop</p>
                        </a>
                        <a href="#">
                            <p>Tuyển dụng</p>
                        </a>
                        <router-link :to="{
                            name: 'register-shop'
                        }">
                            <p>Kênh người bán</p>
                        </router-link>
                    </div>
                    <!-- End about-us -->
                </div>
            </div>
        </div>

        <div class="sub-footer">
            <div class="container d-flex justify-content-center align-items-center">
                <p class="text-dark mb-2 mt-3 py-0 fw-medium">
                    © Bản quyền thuộc về <span class="brand">EZSHOP</span>
                </p>
            </div>
        </div>
    </footer>
    <!-- End footer  -->
</template>

<script setup>
import { useConfig } from '@/composables/useConfig'

const {
    logoFooter: logo,
    phone,
    email,
    twitter,
    facebook,
    tiktok,
    youtube,
    instagram,
    initConfig
} = useConfig()

// Khởi tạo config nếu chưa có
initConfig()
</script>