<template>
    <div class="__menu-sidebar d-none d-md-flex bg-white position-relative min-vh-100 border-end align-items-center flex-column px-3 py-3 translate-hidden" id="sidebar">
        <div class="__item mb-3">
        <div class="fw-bold fs-14">Quản lý đơn hàng</div>
            <div class="__list">
                <router-link to="/kenh-nguoi-ban/don-hang">Tất cả</router-link>    
            </div>
        </div>

        <div class="__item mb-3">
            <div class="fw-bold fs-14">Quản lý sản phẩm</div>
            <div class="__list">
                <router-link to="/kenh-nguoi-ban/san-pham">Tất cả sản phẩm</router-link>    
                <router-link to="/kenh-nguoi-ban/them-san-pham">Thêm sản phẩm</router-link>    
            </div>
        </div>

        <div class="__item mb-3">
            <div class="fw-bold fs-14">Quản lý Thuộc tính</div>
            <div class="__list">
                <router-link to="/kenh-nguoi-ban/thuoc-tinh">Tất cả thuộc tính</router-link>       
            </div>
        </div>
        

        <div class="__item mb-3">
        <div class="fw-bold fs-14">Khuyến mãi</div>
        <div class="__list">
            <router-link to="/kenh-nguoi-ban/khuyen-mai">Tất cả khuyến mãi</router-link>   
        </div>
        </div>

        <div class="__item mb-3">
        <div class="fw-bold fs-14">Quản lý tài chính</div>
        <div class="__list">
            <router-link to="/kenh-nguoi-ban/doanh-thu">Doanh thu</router-link>    
            <router-link to="/kenh-nguoi-ban/tai-chinh">Số dư tài khoản</router-link>    
        </div>
        </div>

        <div class="__item mb-3">
        <div class="fw-bold fs-14">Cài đặt</div>
        <div class="__list">
            <router-link to="/kenh-nguoi-ban/ho-so">Thông tin shop</router-link>    
        </div>
        </div>

    </div>
</template>