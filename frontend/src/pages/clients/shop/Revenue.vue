<template>
  <div v-if="loading.fetch_all" class="d-flex align-items-center justify-content-center mt-3 w-100"
    style="min-height: 60vh;">
    <loading_dot border="4px" size="30px" />
  </div>
  <div v-else class="__main">
    <div class="__todo-list mt-3 shadow-sm bg-white px-5 py-4">
      <div class="__title fs-20 fw-semibold">Danh sách cần làm</div>
      <div class="__content mt-3">
        <div class="wrapper d-flex flex-wrap w-100 justify-content-between">
          <router-link :to="{
            name: 'order-shop'
          }" class="__item __pending d-flex flex-column align-items-center">
            <div class="__count fw-bold main-color fs-20">{{ orderStats.pending ?? 0 }}</div>
            <div class="__title fs-14 mt-2">Chờ xác nhận</div>
          </router-link>

          <router-link :to="{
            name: 'order-shop'
          }" class="__item __awaiting-pickup d-flex flex-column align-items-center">
            <div class="__count fw-bold main-color fs-20">{{ orderStats.confirmed ?? 0 }}</div>
            <div class="__title fs-14 mt-2">Chờ lấy hàng</div>
          </router-link>

          <router-link :to="{
            name: 'order-shop'
          }" class="__item __confirmed d-flex flex-column align-items-center">
            <div class="__count fw-bold main-color fs-20">{{ orderStats.done ?? 0 }}</div>
            <div class="__title fs-14 mt-2">Đã hoàn thành</div>
          </router-link>

          <router-link :to="{
            name: 'order-shop'
          }" class="__item __refund d-flex flex-column align-items-center">
            <div class="__count fw-bold main-color fs-20">{{ orderStats.return_refund ?? 0 }}</div>
            <div class="__title fs-14 mt-2">Đơn trả hàng/ hoàn tiền</div>
          </router-link>

          <router-link :to="{
            name: 'order-shop'
          }" class="__item __canceled d-flex flex-column align-items-center">
            <div class="__count fw-bold main-color fs-20">{{ orderStats.canceled ?? 0 }}</div>
            <div class="__title fs-14 mt-2">Đã huỷ</div>
          </router-link>

        </div>
      </div>
    </div>
    <!-- Todo-list  -->
    <div class="__sale-analysis mt-2 shadow-sm bg-white px-5 pt-3">
      <div class="__title fs-20 fw-semibold">Phân tích bán hàng</div>
      <div class="__content">
        <div class="__time fs-14 text-grey mt-2">
          <div class="row mb-3">
            <div class="col">
              <label for="start_date" class="form-label">Từ ngày</label>
              <input type="date" id="start_date" v-model="filters.start_date" class="form-control">
            </div>
            <div class="col">
              <label for="end_date" class="form-label">Đến ngày</label>
              <input type="date" id="end_date" v-model="filters.end_date" class="form-control">
            </div>
            <div class="col d-flex align-items-end gap-2">
              <button @click="filtersRun" style="height: 38px;"
                class="main-btn w-100 d-flex align-items-center justify-content-center">
                <loading_loader v-if="loading.fetch_shop_analytics" size="20px" border="3px" color="#fff" />
                <span v-else>Lọc</span>
              </button>
              <button @click="clearFilters()" v-if="filters.start_date || filters.end_date" type="button"
                style="width: 120px; height: 38px;" class="white-btn fs-14">Xóa lọc</button>
            </div>
          </div>

        </div>
        <div class="__overview fs-14 text-grey mt-2">Tổng quan dữ liệu của shop đối với đơn hàng</div>

        <div class="__statistic d-flex flex-wrap mt-2">
          <div class="__item shadow-sm p-3">
            <div class="__title d-flex justify-content-between text-grey">
              <span class="fw-semibold fs-14">Doanh số</span>
              <i class="fa-solid fa-circle-exclamation fs-24"></i>
            </div>
            <div class="__data fw-semibold">{{ formatPrice(analytics.total_revenue) }}</div>
          </div>

          <div class="__item shadow-sm p-3">
            <div class="__title d-flex justify-content-between text-grey">
              <span class="fw-semibold fs-14">Lượt truy cập</span>
              <i class="fa-solid fa-circle-exclamation fs-24"></i>
            </div>
            <div class="__data fw-semibold">{{ analytics.total_visits }}</div>
          </div>

          <div class="__item shadow-sm p-3">
            <div class="__title d-flex justify-content-between text-grey">
              <span class="fw-semibold fs-14">Đơn hàng</span>
              <i class="fa-solid fa-circle-exclamation fs-24"></i>
            </div>
            <div class="__data fw-semibold">{{ analytics.total_orders }}</div>
          </div>

          <div class="__item shadow-sm p-3">
            <div class="__title d-flex justify-content-between text-grey">
              <span class="fw-semibold fs-14">Tỉ lệ chuyển đổi</span>
              <i class="fa-solid fa-circle-exclamation fs-24"></i>
            </div>
            <div class="__data fw-semibold">{{ analytics.conversion_rate }}%</div>
          </div>

        </div>
        <div class="mt-5">
          <ApexChart width="100%" height="300" type="line" ref="chartRef" :options="chartOptions" :series="series" />
        </div>
      </div>
    </div>
    <!-- Sale-analysis  -->

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/configs/api'
import loading_dot from '@/components/loading/loading__dot.vue'
import loading_loader from '@/components/loading/loading__loader-circle.vue'
import { formatPrice } from '@/utils/formatPrice'
import ApexChart from 'vue3-apexcharts'
import message from '@/utils/messageState'

const chartRef = ref(null)

const series = ref([])

const chartOptions = ref({
  chart: {
    type: 'line',
    height: 400,
    toolbar: {
      show: false
    }
  },
  stroke: {
    curve: 'smooth'
  },
  markers: {
    size: 4
  },
  colors: ['#00b894', '#0984e3', '#fd79a8'],
  xaxis: {
    categories: [],
    title: {
      text: 'Ngày'
    }
  },
  yaxis: {
    labels: {
      formatter: (val) => {
        return val > 50 ? val.toLocaleString('vi-VN') + ' ₫' : val
      }
    },
    title: {
      text: 'Giá trị'
    }
  },
  tooltip: {
    y: {
      formatter: function (val) {
        return val > 50 ? val.toLocaleString('vi-VN') + ' ₫' : val
      }
    }
  },
  legend: {
    position: 'top'
  }
})

const updateChart = (data) => {
  chartRef.value?.updateOptions({
    xaxis: {
      categories: data.categories,
      title: { text: 'Ngày' }
    }
  })
}
const loading = ref({
  fetch_all: false,
  order_stats: false,
  fetch_shop_analytics: false
})

const orderStats = ref({
  pending: 0,
  confirmed: 0,
  done: 0,
  canceled: 0,
  return_refund: 0
})

const analytics = ref({
  total_revenue: 0,
  total_orders: 0,
  total_visits: 0,
  conversion_rate: 0
})

const filters = ref({
  start_date: '',
  end_date: ''
})

const filtersRun = () => {
  if (filters.value.start_date || filters.value.end_date) {
    fetchShopAnalytics()
    fetchChartShopAnalytics()
  } else {
    message.emit('show-message', { title: "Thông Báo", text: "Vui lòng chọn ngày bắt đầu hoặc kết thúc để lọc", type: "warning" })
  }
}
const clearFilters = () => {
  filters.value.start_date = ''
  filters.value.end_date = ''
  fetchShopAnalytics()
  fetchChartShopAnalytics()
}
const fetchOrderStats = async () => {
  try {
    loading.value.order_stats = true
    const res = await api.get('shops/statistics/overview')
    orderStats.value = res.data
  } catch (error) {
    console.error('Lỗi khi tải thống kê:', error)
  } finally {
    loading.value.order_stats = false
  }
}

const fetchShopAnalytics = async () => {
  try {
    loading.value.fetch_shop_analytics = true
    const res = await api.get('shops/analytics', {
      params: {
        start_date: filters.value.start_date,
        end_date: filters.value.end_date
      }
    })
    analytics.value = res.data
  } catch (error) {
    console.error('Lỗi khi tải thống kê:', error)
  } finally {
    loading.value.fetch_shop_analytics = false
  }
}

const fetchChartShopAnalytics = async () => {
  try {
    const res = await api.get('shops/chart-analytics', {
      params: {
        start_date: filters.value.start_date,
        end_date: filters.value.end_date
      }
    })
    series.value = res.data.series
    chartOptions.value.xaxis.categories = res.data.categories
    updateChart(res.data)
  } catch (error) {
    console.error('Lỗi khi tải thống kê:', error)
  } finally {
  }
}

onMounted(async () => {
  loading.value.fetch_all = true
  await fetchOrderStats()
  await fetchShopAnalytics()
  await fetchChartShopAnalytics()
  loading.value.fetch_all = false
})

</script>

<style scoped>
.__main>.__todo-list {
  width: 100%;
  min-height: 150px;
}

.__main>.__todo-list>.__content>.wrapper>.__item {
  min-width: 140px;
  height: 80px;
}

.__main>.__sale-analysis {
  width: 100%;
}

.__main>.__sale-analysis>.__content>.__statistic>.__item {
  width: 235px;
  height: 73px;
  border-left: 3px solid;
  border-radius: 5px;
  margin: 10px 20px 0 0;
}

.__main>.__sale-analysis>.__content>.__statistic>.__item:nth-child(1) {
  border-left-color: #15FC93;
}

.__main>.__sale-analysis>.__content>.__statistic>.__item:nth-child(2) {
  border-left-color: #FF6B6B;
}

.__main>.__sale-analysis>.__content>.__statistic>.__item:nth-child(3) {
  border-left-color: #4D96FF;
}

.__main>.__sale-analysis>.__content>.__statistic>.__item:nth-child(4) {
  border-left-color: #FFD93D;
}
</style>