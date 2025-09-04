<template>
    <main class="container-fluid">
        <!-- Header Section -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4 mt-4">
            <div>
                <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
                <p class="text-muted mb-0 small">Tổng quan hoạt động hệ thống</p>
            </div>
            <div class="d-flex gap-2">
                <!-- Nút làm mới -->
                <button class="btn btn-outline-primary btn-sm" style="border-radius: 2px;" @click="refreshData" :disabled="loading">
                    <i class="fas fa-sync-alt me-1" :class="{ 'fa-spin': loading }"></i> 
                    {{ loading ? 'Đang tải...' : 'Làm mới' }}
                </button>
                
                <!-- Nút xuất báo cáo -->
                <button class="btn btn-success btn-sm" style="border-radius: 2px;" @click="exportReport">
                    <i class="fas fa-download me-1"></i> Xuất báo cáo
                </button>
            </div>
        </div>
        
        <!-- Stats Cards -->
        <div>
            <div class="row">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow-sm h-100 py-2 stats-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <div class="text-primary text-uppercase mb-1 fs-15 fw-bold">
                                        Tổng người dùng
                                    </div>
                                    <div class="mb-0 h4 fw-bold text-gray-800">
                                        {{ formatNumber(dashboardStats.total_users) }}
                                    </div>
                                    <div class="text-success small mt-1">
                                        <i class="fas fa-users me-1"></i>
                                        Người dùng hệ thống
                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="stats-icon bg-primary">
                                        <i class="fas fa-users"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow-sm h-100 py-2 stats-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <div class="text-success text-uppercase mb-1 fs-15 fw-bold">
                                        Tổng sản phẩm
                                    </div>
                                    <div class="mb-0 h4 fw-bold text-gray-800">
                                        {{ formatNumber(dashboardStats.total_products) }}
                                    </div>
                                    <div class="text-success small mt-1">
                                        <i class="fas fa-arrow-up me-1"></i>
                                        Sản phẩm đang bán
                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="stats-icon bg-success">
                                        <i class="fas fa-box"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info shadow-sm h-100 py-2 stats-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <div class="text-info text-uppercase mb-1 fs-15 fw-bold">
                                        Tổng cửa hàng
                                    </div>
                                    <div class="mb-0 h4 fw-bold text-gray-800">
                                        {{ formatNumber(dashboardStats.total_shops) }}
                                    </div>
                                    <div class="text-info small mt-1">
                                        <i class="fas fa-store me-1"></i>
                                        Cửa hàng hoạt động
                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="stats-icon bg-info">
                                        <i class="fas fa-store"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow-sm h-100 py-2 stats-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <div class="text-warning text-uppercase mb-1 fs-15 fw-bold">
                                        Đơn hàng tháng
                                    </div>
                                    <div class="mb-0 h4 fw-bold text-gray-800">
                                        {{ formatNumber(dashboardStats.monthly_orders) }}
                                    </div>
                                    <div :class="dashboardStats.order_growth >= 0 ? 'text-success' : 'text-danger'" class="small mt-1">
                                        <i :class="dashboardStats.order_growth >= 0 ? 'fas fa-arrow-up' : 'fas fa-arrow-down'" class="me-1"></i>
                                        {{ dashboardStats.order_growth }}% so với tháng trước
                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="stats-icon bg-warning">
                                        <i class="fas fa-shopping-cart"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Revenue & Orders Row -->
            <div class="row">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-danger shadow-sm h-100 py-2 stats-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <div class="text-danger text-uppercase mb-1 fs-15 fw-bold d-flex align-items-center justify-content-between" style="white-space: nowrap;">
                                        <span style="overflow: hidden; text-overflow: ellipsis;">DT {{ getRevenueFilterText() }}</span>
                                        <!-- Mini filter dropdown chỉ cho doanh thu -->
                                        <div class="dropdown ms-2 flex-shrink-0">
                                            <button class="btn btn-sm btn-link text-danger p-0" type="button" data-bs-toggle="dropdown" style="font-size: 12px;">
                                                <i class="fas fa-filter"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><h6 class="dropdown-header" style="font-size: 11px;">Lọc doanh thu</h6></li>
                                                <li><a class="dropdown-item" href="#" @click="applyRevenueFilter('today')" style="font-size: 12px;">
                                                    <i class="fas fa-calendar-day me-2"></i>Hôm nay
                                                </a></li>
                                                <li><a class="dropdown-item" href="#" @click="applyRevenueFilter('week')" style="font-size: 12px;">
                                                    <i class="fas fa-calendar-week me-2"></i>Tuần này
                                                </a></li>
                                                <li><a class="dropdown-item" href="#" @click="applyRevenueFilter('month')" style="font-size: 12px;">
                                                    <i class="fas fa-calendar-alt me-2"></i>Tháng này
                                                </a></li>
                                                <li><a class="dropdown-item" href="#" @click="applyRevenueFilter('quarter')" style="font-size: 12px;">
                                                    <i class="fas fa-calendar me-2"></i>Quý này
                                                </a></li>
                                                <li><a class="dropdown-item" href="#" @click="applyRevenueFilter('year')" style="font-size: 12px;">
                                                    <i class="fas fa-calendar-check me-2"></i>Năm này
                                                </a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="mb-0 h4 fw-bold text-gray-800">
                                        {{ formatPrice(revenueStats.amount) }}
                                    </div>
                                    <div :class="revenueStats.growth >= 0 ? 'text-success' : 'text-danger'" class="small mt-1">
                                        <i :class="revenueStats.growth >= 0 ? 'fas fa-arrow-up' : 'fas fa-arrow-down'" class="me-1"></i>
                                        {{ revenueStats.growth }}% so với {{ getRevenuePreviousPeriodText() }}
                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="stats-icon bg-danger">
                                        <i class="fas fa-dollar-sign"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-secondary shadow-sm h-100 py-2 stats-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <div class="text-secondary text-uppercase mb-1 fs-15 fw-bold">
                                        Đơn chờ duyệt
                                    </div>
                                    <div class="mb-0 h4 fw-bold text-gray-800">
                                        {{ formatNumber(dashboardStats.pending_orders) }}
                                    </div>
                                    <div class="text-warning small mt-1">
                                        <i class="fas fa-clock me-1"></i>
                                        Cần xử lý
                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="stats-icon bg-secondary">
                                        <i class="fas fa-hourglass-half"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow-sm h-100 py-2 stats-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <div class="text-success text-uppercase mb-1 fs-15 fw-bold">
                                        Đơn đã duyệt
                                    </div>
                                    <div class="mb-0 h4 fw-bold text-gray-800">
                                        {{ formatNumber(dashboardStats.confirmed_orders) }}
                                    </div>
                                    <div class="text-success small mt-1">
                                        <i class="fas fa-check me-1"></i>
                                        Đã xác nhận
                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="stats-icon bg-success">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info shadow-sm h-100 py-2 stats-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <div class="text-info text-uppercase mb-1 fs-15 fw-bold">
                                        Tổng doanh thu
                                    </div>
                                    <div class="mb-0 h4 fw-bold text-gray-800">
                                        {{ formatPrice(dashboardStats.total_revenue) }}
                                    </div>
                                    <div class="text-info small mt-1">
                                        <i class="fas fa-chart-line me-1"></i>
                                        Tổng tích lũy
                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="stats-icon bg-info">
                                        <i class="fas fa-chart-line"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="row">
            <!-- Revenue Chart -->
            <div class="col-xl-8 col-lg-7">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 fw-bold text-primary text-danger">
                            <i class="fas fa-chart-area me-2"></i>
                            Biểu đồ doanh thu
                        </h6>
                        <div class="d-flex gap-2">
                            <!-- Bộ lọc khoảng thời gian cho biểu đồ -->
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-danger dropdown-toggle" type="button" data-bs-toggle="dropdown" style="border-radius: 2px;">
                                    <i class="fas fa-chart-line me-1"></i>{{ chartPeriodText }}
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" @click="loadRevenueChart(6)">6 tháng gần đây</a></li>
                                    <li><a class="dropdown-item" href="#" @click="loadRevenueChart(12)">12 tháng gần đây</a></li>
                                    <li><a class="dropdown-item" href="#" @click="loadRevenueChart(24)">24 tháng gần đây</a></li>
                                </ul>
                            </div>
                            
                            <!-- Menu khác -->
                            <div class="dropdown">
                                <button class="btn btn-sm btn-link text-muted dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" @click="exportChartData">
                                        <i class="fas fa-download me-2"></i>Xuất dữ liệu biểu đồ
                                    </a></li>
                                    <li><a class="dropdown-item" href="#" @click="refreshChart">
                                        <i class="fas fa-sync me-2"></i>Làm mới biểu đồ
                                    </a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="chart-area">
                            <div v-if="chartsLoading" class="chart-loading">
                                <div class="spinner-border text-danger" role="status">
                                    <span class="visually-hidden">Đang tải...</span>
                                </div>
                            </div>
                            <canvas id="myAreaChart" width="100%" height="320"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Category Chart -->
            <div class="col-xl-4 col-lg-5">
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 fw-bold text-primary text-danger">
                            <i class="fas fa-chart-pie me-2"></i>
                            Tỉ lệ danh mục
                        </h6>
                        <button class="btn btn-sm btn-link text-muted">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="chart-pie d-flex align-items-center justify-content-center" style="height: 300px;">
                            <div v-if="chartsLoading" class="chart-loading">
                                <div class="spinner-border text-danger" role="status">
                                    <span class="visually-hidden">Đang tải...</span>
                                </div>
                            </div>
                            <canvas id="myPieChart" width="100%" height="100%"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Top Shops -->
        <div class="row mt-4 mt-lg-0">
            <div class="col-xl-12">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="m-0 fw-bold text-primary text-danger">
                            <i class="fas fa-store me-2"></i>
                            Top cửa hàng
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div v-if="topShopsLoading" class="d-flex align-items-center justify-content-center py-5">
                            <div class="spinner-border text-danger" role="status">
                                <span class="visually-hidden">Đang tải...</span>
                            </div>
                        </div>
                        <div v-else-if="topShops.length === 0" class="text-center py-5 text-muted">
                            <i class="fas fa-store fa-3x mb-3"></i>
                            <p>Không có dữ liệu cửa hàng</p>
                        </div>
                        <div v-else>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="border-0 d-flex align-items-center justify-content-center">Hạng</th>
                                            <th class="border-0">Cửa hàng</th>
                                            <th class="border-0">Chủ sở hữu</th>
                                            <th class="border-0">Tổng đơn hàng</th>
                                            <th class="border-0">Doanh thu</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(shop, index) in topShops" :key="shop.id">
                                            <td class="align-middle">
                                                <div class="d-flex align-items-center justify-content-center">
                                                    <span class="badge fs-6 fw-bold" :class="getRankBadgeClass(index + 1)">
                                                        {{ index + 1 }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="align-middle">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm me-3">
                                                        <img v-if="shop.shop_avatar" 
                                                             :src="`imgs/shops/${shop.shop_avatar}`" 
                                                             :alt="shop.shop_name"
                                                             class="rounded-circle"
                                                             style="width: 40px; height: 40px; object-fit: cover;">
                                                        <div v-else 
                                                             class="bg-secondary rounded-circle d-flex align-items-center justify-content-center text-white"
                                                             style="width: 40px; height: 40px;">
                                                            <i class="fas fa-store"></i>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-gray-800">{{ shop.shop_name || 'N/A' }}</div>
                                                        <div class="text-muted small">ID: #{{ shop.id }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle">
                                                <span class="text-gray-800">{{ shop.owner_name || 'N/A' }}</span>
                                            </td>
                                            <td class="align-middle">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-shopping-cart text-warning me-2"></i>
                                                    <span class="fw-bold">{{ formatNumber(shop.total_orders || 0) }}</span>
                                                </div>
                                            </td>
                                            <td class="align-middle">
                                                <div class="d-flex align-items-center">
                                                    <span class="fw-bold text-success">{{ formatPrice(shop.total_revenue || 0) }}</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- Top shops pagination -->
                            <div class="px-3 py-2">
                                <pagination 
                                    :meta="topShopsPagination" 
                                    @changePage="loadTopShops"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Orders -->
        <div class="row mt-4 mt-lg-0">
            <div class="col-xl-12">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="m-0 fw-bold text-primary text-danger">
                            <i class="fas fa-shopping-cart me-2"></i>
                            Đơn hàng gần đây
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div v-if="recentOrdersLoading" class="d-flex align-items-center justify-content-center py-5">
                            <div class="spinner-border text-danger" role="status">
                                <span class="visually-hidden">Đang tải...</span>
                            </div>
                        </div>
                        <div v-else-if="recentOrders.length === 0" class="text-center py-5 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3"></i>
                            <p>Không có đơn hàng nào</p>
                        </div>
                        <div v-else>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="d-flex align-items-center justify-content-center">ID</th>
                                            <th>Khách hàng</th>
                                            <th>Cửa hàng</th>
                                            <th>Số tiền</th>
                                            <th>Trạng thái</th>
                                            <th>Thời gian</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="order in recentOrders" :key="order.id">
                                            <td class="d-flex align-items-center justify-content-center">#{{ order.id }}</td>
                                            <td>{{ order.user?.user_name || order.user?.name || order.customer_name || order.user_name || 'N/A' }}</td>
                                            <td>{{ order.shop?.shop_name || order.shop?.name || order.store_name || order.shop_name || 'N/A' }}</td>
                                            <td class="fw-bold text-success">{{ formatPrice(order.total_amount || order.amount || order.total || 0) }}</td>
                                            <td>
                                                <span class="badge" :class="getOrderStatusClass(order.order_status || order.status)">
                                                    {{ getOrderStatusText(order.order_status || order.status) }}
                                                </span>
                                            </td>
                                            <td class="text-muted">
                                                <i class="fas fa-clock me-1"></i>
                                                {{ formatTimeAgo(order.created_date || order.created_at) }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- Pagination -->
                            <div class="d-flex align-items-center justify-content-end p-3">
                                <pagination :meta="meta" @changePage="onChangePage" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { formatPrice } from '@/utils/formatPrice.js'
import api from '@/configs/api'
import pagination from '@/components/pagination.vue'


// ===========================
// CONSTANTS & CONFIGURATION
// ===========================
// ===========================

// Loading states - Quản lý trạng thái loading cho các phần khác nhau
const loading = ref(false) // Loading chính cho toàn bộ dashboard
const recentOrdersLoading = ref(false) // Loading riêng cho bảng đơn hàng
const topShopsLoading = ref(false) // Loading riêng cho bảng top shops
const chartsLoading = ref(false) // Loading riêng cho biểu đồ
const statsLoading = ref(false) // Loading riêng cho stats cards

// Filter states - Quản lý bộ lọc thời gian cho doanh thu riêng biệt
const revenueFilter = ref('month') // Bộ lọc riêng cho doanh thu: today, week, month, quarter, year

// Chart filter states - Quản lý bộ lọc cho biểu đồ
const chartPeriod = ref(12) // Khoảng thời gian cho biểu đồ (tháng)
const chartPeriodText = ref('12 tháng gần đây') // Text hiển thị cho dropdown

// Dashboard statistics - Dữ liệu thống kê tổng quan
const dashboardStats = ref({
    total_users: 0,
    total_products: 0,
    total_shops: 0,
    total_categories: 0,
    total_orders: 0,
    pending_orders: 0,
    confirmed_orders: 0,
    total_revenue: 0,
    monthly_orders: 0,
    monthly_revenue: 0,
    order_growth: 0,
    revenue_growth: 0
})

// Revenue statistics - Dữ liệu doanh thu riêng biệt có thể lọc
const revenueStats = ref({
    amount: 0,
    growth: 0
})

// Recent orders data - Dữ liệu đơn hàng gần đây
const recentOrders = ref([])

// Top shops data - Dữ liệu top cửa hàng
const topShops = ref([])
const topShopsPagination = ref({
    current_page: 1,
    last_page: 1,
    per_page: 5,
    total: 0
})

// Chart data - Dữ liệu cho các biểu đồ
const categoryStats = ref([]) // Dữ liệu cho biểu đồ tròn danh mục
const revenueChartData = ref([]) // Dữ liệu cho biểu đồ doanh thu

// Pagination data - Dữ liệu phân trang cho đơn hàng gần đây
const meta = ref({})
const currentPage = ref(1)

// ===========================
// API FUNCTIONS
// ===========================

/**
 * Load dashboard statistics (không có filter)
 * Tải thống kê dashboard cố định
 */
const loadDashboardStats = async () => {
    try {
        statsLoading.value = true
        const response = await api.get('/admin/dashboard/stats')
        dashboardStats.value = response.data.overview || response.data
    } catch (error) {
        console.error('Error loading dashboard stats:', error)
        // Không reset loading ở đây để tránh UI nhấp nháy
    } finally {
        statsLoading.value = false
    }
}

/**
 * Load revenue statistics with filter
 * Tải thống kê doanh thu theo bộ lọc
 */
const loadRevenueStats = async () => {
    try {
        // Gọi API thật để lấy doanh thu theo filter
        const response = await api.get('/admin/dashboard/revenue-stats', {
            params: { filter: revenueFilter.value }
        })
        
        if (response.data.success) {
            revenueStats.value = response.data.revenue_stats
        } else {
            // Fallback to dashboard stats if API fails
            const baseMonthlyRevenue = dashboardStats.value.monthly_revenue || 0
            revenueStats.value = {
                amount: baseMonthlyRevenue,
                growth: dashboardStats.value.revenue_growth || 0
            }
        }

    } catch (error) {
        console.error('Error loading revenue stats:', error)
        
        // Fallback to dashboard stats on error
        const baseMonthlyRevenue = dashboardStats.value.monthly_revenue || 0
        revenueStats.value = {
            amount: baseMonthlyRevenue,
            growth: dashboardStats.value.revenue_growth || 0
        }
    }
}

/**
 * Load recent orders with pagination (không có filter)
 * Tải danh sách đơn hàng gần đây có phân trang
 * @param {number} page - Số trang cần tải
 */
const loadRecentOrders = async (page = 1) => {
    try {
        recentOrdersLoading.value = true
        const response = await api.get('/admin/dashboard/recent-orders', {
            params: { page }
        })
        
        recentOrders.value = response.data.recent_orders || response.data.data || []
        
        // Xử lý meta data cho pagination
        if (response.data.meta) {
            meta.value = response.data.meta
        } else if (response.data.current_page) {
            meta.value = {
                current_page: response.data.current_page,
                last_page: response.data.last_page,
                per_page: response.data.per_page,
                total: response.data.total
            }
        }
        currentPage.value = page
    } catch (error) {
        console.error('Error loading recent orders:', error)
    } finally {
        recentOrdersLoading.value = false
    }
}

/**
 * Load top shops with pagination
 * Tải dữ liệu top cửa hàng với phân trang
 */
const loadTopShops = async (page = 1) => {
    try {
        topShopsLoading.value = true

        const response = await api.get('/admin/dashboard/top-shops', {
            params: {
                page: page,
                per_page: topShopsPagination.value.per_page
            }
        })

        topShops.value = response.data.top_shops || []
        topShopsPagination.value = response.data.meta || response.data
    } catch (error) {
        console.error('Error loading top shops:', error)
        topShops.value = []
    } finally {
        topShopsLoading.value = false
    }
}

/**
 * Load category statistics for pie chart (không có filter)
 * Tải thống kê danh mục cho biểu đồ tròn
 */
const loadCategoryStats = async () => {
    try {
        chartsLoading.value = true

        const response = await api.get('/admin/dashboard/category-stats')
        categoryStats.value = response.data.category_stats || []
        renderPieChart()
    } catch (error) {
        console.error('Error loading category stats:', error)
    } finally {
        chartsLoading.value = false
    }
}

/**
 * Load revenue chart data (không có filter)
 * Tải dữ liệu biểu đồ doanh thu
 * @param {number} period - Khoảng thời gian (tháng)
 */
const loadRevenueChart = async (period = 12) => {
    try {
        chartsLoading.value = true
        chartPeriod.value = period
        chartPeriodText.value = `${period} tháng gần đây`

        const response = await api.get('/admin/dashboard/revenue-chart', {
            params: { period }
        })
        
        revenueChartData.value = response.data.chart_data || []
        renderAreaChart()
    } catch (error) {
        console.error('Error loading revenue chart:', error)
    } finally {
        chartsLoading.value = false
    }
}

// ===========================
// FILTER FUNCTIONS
// ===========================

/**
 * Apply revenue filter and refresh revenue data only
 * Áp dụng bộ lọc doanh thu và chỉ làm mới dữ liệu doanh thu
 * @param {string} filter - Loại bộ lọc: today, week, month, quarter, year
 */
const applyRevenueFilter = async (filter) => {
    try {
        revenueFilter.value = filter
        
        // Load lại revenue stats với filter mới
        await loadRevenueStats()
    } catch (error) {
        console.error('Error applying revenue filter:', error)
    }
}

/**
 * Get revenue filter display text
 * Lấy text hiển thị cho bộ lọc doanh thu hiện tại
 * @returns {string} Text hiển thị
 */
const getRevenueFilterText = () => {
    const filterTexts = {
        today: 'hôm nay',
        week: 'tuần này',
        month: 'tháng này', 
        quarter: 'quý này',
        year: 'năm này'
    }
    return filterTexts[revenueFilter.value] || 'tháng này'
}

/**
 * Get previous period text for revenue growth comparison
 * Lấy text kỳ trước để so sánh tăng trưởng doanh thu
 * @returns {string} Text kỳ trước
 */
const getRevenuePreviousPeriodText = () => {
    const periodTexts = {
        today: 'hôm qua',
        week: 'tuần trước',
        month: 'tháng trước',
        quarter: 'quý trước', 
        year: 'năm trước'
    }
    return periodTexts[revenueFilter.value] || 'tháng trước'
}

// ===========================
// UTILITY FUNCTIONS
// ===========================

// ===========================
// UTILITY FUNCTIONS
// ===========================

/**
 * Format month string to Vietnamese format (MM-YYYY)
 * Định dạng chuỗi tháng sang định dạng Việt Nam
 * @param {string} monthString - Chuỗi tháng đầu vào
 * @returns {string} Chuỗi tháng đã định dạng
 */
const formatMonthToVietnamese = (monthString) => {
    try {
        // Handle both "2024-01" and other formats
        if (monthString.includes('-')) {
            const parts = monthString.split('-')
            const year = parts[0]
            const month = parseInt(parts[1])
            return `${month}-${year}`
        }
        
        // If it's already in a different format, try to parse it
        const date = new Date(monthString)
        if (!isNaN(date.getTime())) {
            const month = date.getMonth() + 1
            const year = date.getFullYear()
            return `${month}-${year}`
        }
        
        return monthString
    } catch (error) {
        console.error('Error formatting month:', error)
        return monthString
    }
}

/**
 * Format number with Vietnamese locale
 * Định dạng số theo định dạng Việt Nam
 * @param {number} number - Số cần định dạng
 * @returns {string} Số đã định dạng
 */
const formatNumber = (number) => {
    if (!number) return '0'
    return new Intl.NumberFormat('vi-VN').format(number)
}

/**
 * Format time ago in Vietnamese
 * Định dạng thời gian theo kiểu "15 phút trước"
 * @param {string} dateString - Chuỗi thời gian
 * @returns {string} Text thời gian đã định dạng
 */
const formatTimeAgo = (dateString) => {
    if (!dateString) return 'N/A'
    
    try {
        const now = new Date()
        const date = new Date(dateString)
        const diffInMs = now - date
        const diffInMinutes = Math.floor(diffInMs / (1000 * 60))
        const diffInHours = Math.floor(diffInMs / (1000 * 60 * 60))
        const diffInDays = Math.floor(diffInMs / (1000 * 60 * 60 * 24))
        const diffInMonths = Math.floor(diffInMs / (1000 * 60 * 60 * 24 * 30))
        const diffInYears = Math.floor(diffInMs / (1000 * 60 * 60 * 24 * 365))
        
        if (diffInMinutes < 1) {
            return 'Vừa xong'
        } else if (diffInMinutes < 60) {
            return `${diffInMinutes} phút trước`
        } else if (diffInHours < 24) {
            return `${diffInHours} giờ trước`
        } else if (diffInDays < 30) {
            return `${diffInDays} ngày trước`
        } else if (diffInMonths < 12) {
            return `${diffInMonths} tháng trước`
        } else {
            return `${diffInYears} năm trước`
        }
    } catch (error) {
        console.error('Error formatting time ago:', error)
        return 'N/A'
    }
}

/**
 * Get CSS class for rank badge
 * Lấy class CSS cho badge thứ hạng
 * @param {number} rank - Thứ hạng (1, 2, 3, ...)
 * @returns {string} Class CSS
 */
const getRankBadgeClass = (rank) => {
    const rankClasses = {
        1: 'bg-warning text-dark',    // Vàng cho hạng 1
        2: 'bg-secondary text-white', // Xám cho hạng 2  
        3: 'bg-warning text-white'    // Cam cho hạng 3
    }
    return rankClasses[rank] || 'bg-primary text-white' // Xanh cho các hạng khác
}

/**
 * Get CSS class for order status badge
 * Lấy class CSS cho badge trạng thái đơn hàng
 * @param {number} status - Mã trạng thái đơn hàng
 * @returns {string} Class CSS
 */
const getOrderStatusClass = (status) => {
    const statusClasses = {
        1: 'bg-warning text-dark',  // Chờ duyệt
        2: 'bg-info text-white',    // Đã duyệt
        3: 'bg-primary text-white', // Đang giao
        4: 'bg-secondary text-white', // Đã giao
        5: 'bg-success text-white', // Hoàn thành
        6: 'bg-danger text-white',  // Đã hủy
        7: 'bg-dark text-white'     // Hoàn trả
    }
    return statusClasses[status] || 'bg-secondary text-white'
}

/**
 * Get text for order status
 * Lấy text cho trạng thái đơn hàng
 * @param {number} status - Mã trạng thái đơn hàng
 * @returns {string} Text trạng thái
 */
const getOrderStatusText = (status) => {
    const statusTexts = {
        1: 'Chờ duyệt',
        2: 'Đã duyệt', 
        3: 'Đang giao',
        4: 'Đã giao',
        5: 'Hoàn thành',
        6: 'Đã hủy',
        7: 'Hoàn trả'
    }
    return statusTexts[status] || 'Không xác định'
}
// ===========================
// CHART RENDERING FUNCTIONS
// ===========================

/**
 * Render area chart for revenue
 * Vẽ biểu đồ vùng cho doanh thu
 */
const renderAreaChart = () => {
    const ctx = document.getElementById('myAreaChart')
    if (!ctx) return
    
    // Load Chart.js if not available
    if (typeof Chart === 'undefined') {
        const script = document.createElement('script')
        script.src = 'https://cdn.jsdelivr.net/npm/chart.js'
        script.onload = () => renderAreaChart()
        document.head.appendChild(script)
        return
    }
    
    // Destroy existing chart if it exists
    if (window.myAreaChart && typeof window.myAreaChart.destroy === 'function') {
        window.myAreaChart.destroy()
    }
    
    // Check if we have real data
    let chartData = revenueChartData.value
    if (!chartData || chartData.length === 0) {
        console.log('No revenue chart data available') // Debug log
        // Show empty chart or message
        ctx.getContext('2d').clearRect(0, 0, ctx.width, ctx.height)
        return
    }
    
    const labels = chartData.map(item => formatMonthToVietnamese(item.month))
    const revenueData = chartData.map(item => item.revenue)
    
    window.myAreaChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Doanh thu',
                data: revenueData,
                backgroundColor: 'rgba(231, 74, 59, 0.1)',
                borderColor: 'rgba(231, 74, 59, 1)',
                borderWidth: 2,
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return formatPrice(value)
                        }
                    }
                }
            }
        }
    })
}

/**
 * Render pie chart for categories
 * Vẽ biểu đồ tròn cho danh mục
 */
const renderPieChart = () => {
    const ctx = document.getElementById('myPieChart')
    if (!ctx) return
    
    // Load Chart.js if not available
    if (typeof Chart === 'undefined') {
        const script = document.createElement('script')
        script.src = 'https://cdn.jsdelivr.net/npm/chart.js'
        script.onload = () => renderPieChart()
        document.head.appendChild(script)
        return
    }
    
    // Destroy existing chart if it exists
    if (window.myPieChart && typeof window.myPieChart.destroy === 'function') {
        window.myPieChart.destroy()
    }
    
    // Check if we have real data
    let chartData = categoryStats.value
    if (!chartData || chartData.length === 0) {
        console.log('No category chart data available') // Debug log
        // Show empty chart or message
        ctx.getContext('2d').clearRect(0, 0, ctx.width, ctx.height)
        return
    }
    
    const labels = chartData.map(item => item.category_name)
    const data = chartData.map(item => item.product_count)
    
    const colors = [
        '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
        '#858796', '#5a5c69', '#fd7e14', '#6f42c1', '#20c997'
    ]
    
    window.myPieChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: colors.slice(0, data.length),
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                title: {
                    display: true,
                    text: 'Tỉ lệ danh mục'
                }
            }
        }
    })
}

// ===========================
// PERFORMANCE OPTIMIZATION
// ===========================

/**
 * Debounce function to limit API calls
 * Function debounce để giới hạn API calls
 * @param {Function} func - Function cần debounce
 * @param {number} wait - Thời gian chờ (ms)
 * @returns {Function} Debounced function
 */
const debounce = (func, wait) => {
    let timeout
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout)
            func(...args)
        }
        clearTimeout(timeout)
        timeout = setTimeout(later, wait)
    }
}

/**
 * Check if data is valid and not empty
 * Kiểm tra dữ liệu có hợp lệ và không rỗng
 * @param {any} data - Dữ liệu cần kiểm tra
 * @returns {boolean} True nếu dữ liệu hợp lệ
 */
const isValidData = (data) => {
    return data !== null && data !== undefined && data !== ''
}

/**
 * Optimized refresh with selective loading
 * Refresh tối ưu với loading có chọn lọc
 * @param {string} section - Phần cần refresh: 'all', 'stats', 'charts', 'tables'
 */
const optimizedRefresh = async (section = 'all') => {
    try {
        switch (section) {
            case 'stats':
                await refreshStats()
                break
            case 'charts':
                chartsLoading.value = true
                await Promise.all([
                    loadCategoryStats(),
                    loadRevenueChart(chartPeriod.value)
                ])
                break
            case 'tables':
                await refreshTables()
                break
            case 'all':
            default:
                await refreshData()
                break
        }
    } catch (error) {
        console.error(`Error in optimized refresh (${section}):`, error)
    }
}

// ===========================
// EVENT HANDLERS
// ===========================

/**
 * Handle page change for pagination
 * Xử lý khi thay đổi trang trong pagination
 * @param {number} page - Số trang mới
 */
const onChangePage = (page) => {
    currentPage.value = page
    loadRecentOrders(page)
}

/**
 * Refresh all dashboard data - Optimized version
 * Làm mới tất cả dữ liệu dashboard - Phiên bản tối ưu
 */
const refreshData = async () => {
    try {
        loading.value = true
        
        // Load dashboard stats first để có base data cho revenue stats
        await loadDashboardStats()
        
        // Load revenue stats sau khi có base data
        await loadRevenueStats()
        
        // Load tất cả dữ liệu khác song song để tối ưu thời gian
        const parallelTasks = [
            loadCategoryStats(), // Pie chart
            loadRevenueChart(chartPeriod.value), // Area chart
            loadTopShops(1), // Top shops table
            loadRecentOrders(1) // Recent orders table
        ]
        
        await Promise.all(parallelTasks)
        
    } catch (error) {
        console.error('Error refreshing dashboard data:', error)
    } finally {
        loading.value = false
    }
}

/**
 * Refresh only chart data
 * Làm mới chỉ dữ liệu biểu đồ
 */
const refreshChart = async () => {
    await loadRevenueChart(chartPeriod.value)
}

/**
 * Refresh only stats data
 * Làm mới chỉ dữ liệu thống kê
 */
const refreshStats = async () => {
    await loadDashboardStats()
    await loadRevenueStats()
}

/**
 * Refresh only tables data
 * Làm mới chỉ dữ liệu bảng
 */
const refreshTables = async () => {
    await Promise.all([
        loadTopShops(1),
        loadRecentOrders(1)
    ])
}

/**
 * Export dashboard report
 * Xuất báo cáo dashboard
 */
const exportReport = async () => {
    try {
        // Hiển thị thông báo đang xuất
        const originalText = 'Xuất báo cáo'
        const button = event.target
        button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Đang xuất...'
        button.disabled = true
        
        // Simulate export process (replace with real API call)
        await new Promise(resolve => setTimeout(resolve, 2000))
        
        // Create sample data for download
        const csvContent = `Dashboard Report\n` +
                          `Tổng người dùng,${dashboardStats.value.total_users}\n` +
                          `Tổng sản phẩm,${dashboardStats.value.total_products}\n` +
                          `Tổng cửa hàng,${dashboardStats.value.total_shops}\n` +
                          `Doanh thu ${getRevenueFilterText()},${revenueStats.value.amount}\n` +
                          `Tăng trưởng doanh thu,${revenueStats.value.growth}%\n`
        
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
        const url = window.URL.createObjectURL(blob)
        const a = document.createElement('a')
        a.href = url
        a.download = `dashboard-report-${new Date().toISOString().split('T')[0]}.csv`
        document.body.appendChild(a)
        a.click()
        window.URL.revokeObjectURL(url)
        document.body.removeChild(a)
        
        // Reset button
        button.innerHTML = '<i class="fas fa-check me-1"></i> Đã xuất'
        setTimeout(() => {
            button.innerHTML = `<i class="fas fa-download me-1"></i> ${originalText}`
            button.disabled = false
        }, 2000)
        
    } catch (error) {
        console.error('Error exporting report:', error)
        alert('Có lỗi xảy ra khi xuất báo cáo!')
    }
}

/**
 * Export chart data
 * Xuất dữ liệu biểu đồ
 */
const exportChartData = async () => {
    try {
        // Get current chart data
        const chartData = revenueChartData.value
        
        if (!chartData || chartData.length === 0) {
            alert('Không có dữ liệu biểu đồ để xuất!')
            return
        }
        
        // Create CSV content
        let csvContent = `Biểu đồ doanh thu - ${chartPeriodText.value}\n`
        csvContent += `Tháng,Doanh thu\n`
        
        chartData.forEach(item => {
            csvContent += `${formatMonthToVietnamese(item.month)},${item.revenue}\n`
        })
        
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
        const url = window.URL.createObjectURL(blob)
        const a = document.createElement('a')
        a.href = url
        a.download = `revenue-chart-${chartPeriod.value}months-${new Date().toISOString().split('T')[0]}.csv`
        document.body.appendChild(a)
        a.click()
        window.URL.revokeObjectURL(url)
        document.body.removeChild(a)
        
        console.log('Chart data exported successfully') // Debug log
        
    } catch (error) {
        console.error('Error exporting chart data:', error)
        alert('Có lỗi xảy ra khi xuất dữ liệu biểu đồ!')
    }
}

// ===========================
// LIFECYCLE HOOKS
// ===========================

/**
 * Component mounted hook - Optimized loading
 * Hook khi component được mount - Tối ưu hóa loading
 */
onMounted(async () => {
    // Load stats đầu tiên vì quan trọng nhất
    await loadDashboardStats()
    
    // Load revenue stats ngay sau dashboard stats với filter mặc định
    await loadRevenueStats()
    
    // Sau đó load các phần khác song song
    setTimeout(() => {
        // Load charts sau một chút để UI không bị lag
        Promise.all([
            loadCategoryStats(),
            loadRevenueChart(chartPeriod.value)
        ])
    }, 100)
    
    setTimeout(() => {
        // Load tables cuối cùng
        Promise.all([
            loadTopShops(1),
            loadRecentOrders(1)
        ])
    }, 200)
})
</script>

<style scoped>
.stats-card {
    transition: transform 0.2s, box-shadow 0.2s;
}

.stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 25px rgba(0, 0, 0, 0.15) !important;
    z-index: 900;  /* Đảm bảo card hover có z-index thấp hơn dropdown */
}

.stats-icon {
    width: 55px;
    height: 55px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    color: white;
    font-size: 1.2rem;
}

.border-left-primary {
    border-left: 4px solid #4e73df !important;
}

.border-left-success {
    border-left: 4px solid #1cc88a !important;
}

.border-left-info {
    border-left: 4px solid #36b9cc !important;
}

.border-left-warning {
    border-left: 4px solid #f6c23e !important;
}

.border-left-danger {
    border-left: 4px solid #e74a3b !important;
}

.border-left-secondary {
    border-left: 4px solid #858796 !important;
}

.text-gray-800 {
    color: #5a5c69 !important;
}

.chart-area, .chart-pie {
    position: relative;
    height: 320px;
}

.table th {
    font-weight: 600;
    color: #5a5c69;
    border-bottom: 1px solid #e3e6f0;
}

.table td {
    color: #5a5c69;
    vertical-align: middle;
}

.badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

@media (max-width: 768px) {
    .stats-icon {
        width: 45px;
        height: 45px;
        font-size: 1.1rem;
    }
    
    .h4 {
        font-size: 1.4rem;
    }
}

/* Spinner styles */
.spinner-border {
    width: 2rem;
    height: 2rem;
}

/* Filter button styles */
.btn-outline-secondary.dropdown-toggle {
    min-width: 140px;
    text-align: left;
}

.btn-outline-secondary.dropdown-toggle:focus {
    box-shadow: 0 0 0 0.2rem rgba(108, 117, 125, 0.25);
}

/* Loading overlay for charts */
.chart-loading {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: rgba(255, 255, 255, 0.9);
    z-index: 10;
    border-radius: 0.375rem;
}

/* Smooth loading transitions */
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from, .fade-leave-to {
    opacity: 0;
}

/* Optimized spinner */
.spinner-border {
    width: 2rem;
    height: 2rem;
    border-width: 0.25em;
}

.spinner-border-sm {
    width: 1rem;
    height: 1rem;
    border-width: 0.125em;
}

/* Smooth transitions for stats cards */
.stats-card .h4 {
    transition: all 0.3s ease;
}

/* Chart containers */
.chart-area, .chart-pie {
    position: relative;
}

.chart-area canvas, .chart-pie canvas {
    transition: opacity 0.3s ease;
}

/* Ensure revenue title doesn't wrap */
.stats-card .text-danger.text-uppercase {
    white-space: nowrap !important;
}

.stats-card .text-danger.text-uppercase span {
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: calc(100% - 30px); /* Reserve space for filter button */
}

/* Filter dropdown in revenue card */
.stats-card .dropdown {
    position: relative;
    z-index: 1100;
}

.stats-card .dropdown-menu {
    z-index: 1200 !important;
    position: absolute !important;
}

.stats-card:hover {
    z-index: 900;
}

/* Ensure dropdown is not clipped by card overflow */
.stats-card {
    overflow: visible !important;
}

.stats-card .card-body {
    overflow: visible !important;
}

/* Dropdown menu improvements */
.dropdown-menu {
    border: 1px solid #e3e6f0;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    z-index: 1200 !important;
}

.dropdown-item {
    transition: all 0.15s ease-in-out;
}

.dropdown-item:hover {
    background-color: #f8f9fc;
    color: #5a5c69;
}

.dropdown-header {
    font-weight: 600;
    color: #5a5c69;
}

/* Top shops table styles */
.table th.border-0 {
    border-top: none !important;
    font-weight: 600;
    color: #5a5c69;
    background-color: #f8f9fc;
}

.table tbody tr:hover {
    background-color: #f8f9fc;
}

.avatar-sm img {
    border: 2px solid #e3e6f0;
}

.badge.fs-6 {
    font-size: 0.875rem !important;
    min-width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Rank badge special styling */
.badge.bg-warning.text-dark {
    background: linear-gradient(45deg, #f6c23e, #f4b619) !important;
    box-shadow: 0 2px 4px rgba(246, 194, 62, 0.3);
}

.badge.bg-secondary.text-white {
    background: linear-gradient(45deg, #858796, #6c757d) !important;
    box-shadow: 0 2px 4px rgba(133, 135, 150, 0.3);
}

.text-success.fw-bold {
    font-weight: 600 !important;
}
</style>
