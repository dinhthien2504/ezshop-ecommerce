<template>
    <div class="admin__content">
        <div
            class="p-3 bg-white border-bottom text-grey fs-14 fw-medium d-flex align-items-center justify-content-between">
            <p class="m-0">
                Số dư của sàn
            </p>
            <div class="d-flex align-items-center gap-2">
                <div class="dropdown">
                    <button type="button" class="btn btn-light border dropdown-toggle radius-2 fw-medium"
                        data-bs-toggle="dropdown">
                        Loại Giao Dịch
                    </button>
                    <ul class="dropdown-menu radius-2">
                        <li v-for="(label, key) in type" :key="key" @click="setStatus(key)">
                            <a class="dropdown-item fw-medium cursor-pointer" :class="{ active: selectedType === key }">
                                {{ label }}
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="dropdown">
                    <button type="button" class="btn btn-light border dropdown-toggle radius-2 fw-medium"
                        data-bs-toggle="dropdown">
                        Tháng: {{ selectedMonth || 'Tất cả' }}
                    </button>
                    <ul class="dropdown-menu radius-2">
                        <li v-for="month in 12" :key="month" @click="setMonth(month)">
                            <a class="dropdown-item fw-medium cursor-pointer"
                                :class="{ active: selectedMonth === month }">
                                Tháng {{ month }}
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="dropdown">
                    <button type="button" class="btn btn-light border dropdown-toggle radius-2 fw-medium"
                        data-bs-toggle="dropdown">
                        Năm: {{ selectedYear || 'Tất cả' }}
                    </button>
                    <ul class="dropdown-menu radius-2">
                        <li v-for="year in years" :key="year" @click="setYear(year)">
                            <a class="dropdown-item fw-medium cursor-pointer"
                                :class="{ active: selectedYear === year }">
                                {{ year }}
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="dropdown">
                    <button type="button" class="btn btn-light border dropdown-toggle radius-2 fw-medium"
                        data-bs-toggle="dropdown">
                        Hiển Thị
                    </button>
                    <ul class="dropdown-menu radius-2">
                        <li v-for="option in [10, 25, 50, 100, 500]" :key="option" @click="setPerPage(option)">
                            <a :class="['dropdown-item fw-medium cursor-pointer', { active: per_page === option }]">
                                {{ option }}
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="admin__search">
                    <button type="button" class="search__button">
                        <i class="ri-search-2-line"></i>
                    </button>
                    <form @submit.prevent="onSearch">
                        <div class="d-flex align-items-center gap-1 position-relative">
                            <button type="submit" style="height: 38px" class="main-btn py-1 px-3 ">
                                <i class="ri-search-2-line"></i>
                            </button>
                            <input type="text" v-model="searchValue" placeholder="Tìm kiếm"
                                class="form-control radius-2" />
                            <button v-if="searchValue" type="button" @click="clearSearch"
                                class="btn position-absolute top-50 end-0 translate-middle-y me-2 p-0 border-0 bg-transparent">
                                <i class="ri-close-line"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div v-if="loading.fetch" style="height: 60vh;" class="d-flex align-items-center justify-content-center">
            <loading_loader size="40px" border="4px" />
        </div>
        <div v-else class="p-10">
            <p class="fs-16 fw-medium my-3">Số dư của sàn:
                <span class="fs-20 text-color fw-semibold">
                    {{ formatPrice(balance) }}
                </span>
            </p>
            <no_data v-if="wallet_transactions.length == 0" />
            <div v-else class="table-responsive fade-in">
                <table class="table border">
                    <thead class="table-active">
                        <tr class="lh-lg align-middle">
                            <th scope="col" class="fs-14 fw-semibold min-w-100">Mã GD</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-150">Đơn Hàng</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-100">Loại GD</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-200">Số Tiền</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-100">Số Dư</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-100">Ghi Chú</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-200 text-center">Thời Gian</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="w in wallet_transactions" class="align-middle fw-medium">
                            <td>{{ w.id }}</td>
                            <td>{{ w.order_id ?? 'Tổng hợp' }}</td>
                            <td>
                                {{ type[w.type] }}
                            </td>
                            <td>
                                <span>
                                    {{ Number(w.amount).toLocaleString('vi-VN') + ' đ' }}
                                </span>
                            </td>
                            <td><span>
                                    {{ Number(w.balance_after).toLocaleString('vi-VN') + ' đ' }}
                                </span></td>
                            <td>{{ w.note }}</td>
                            <td class="text-center">
                                <p>{{ formatDateTime(w.created_at) }}</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <pagination :meta="meta" @changePage="onChangePage" />
        </div>
    </div>
</template>
<script setup>
import { onMounted, ref } from 'vue'
import api from '@/configs/api'
import message from '@/utils/messageState'
import loading_loader from '@/components/loading/loading__loader-circle.vue'
import pagination from '@/components/pagination.vue'
import no_data from '@/components/no_data.vue'
import { formatDateTime } from '@/utils/formatDate'
import { formatPrice } from '@/utils/formatPrice'

onMounted(() => {
    const now = new Date()
    const month = now.getMonth() + 1
    const year = now.getFullYear()

    const startYear = year - 5
    const endYear = year + 5

    years.value = Array.from({ length: endYear - startYear + 1 }, (_, i) => startYear + i)
    selectedMonth.value = month
    selectedYear.value = year
    fetchPlatformWallet()
    fetchPlatformWalletTransactions()

})
/*
    Search & Pagination & Filter
*/
const meta = ref({})
const currentPage = ref(1)
const searchValue = ref('')
const per_page = ref(10)
const selectedMonth = ref(null)
const selectedYear = ref(null)
const selectedType = ref('order_payment')
const years = ref([])
const onSearch = () => {
    currentPage.value = 1
    fetchPlatformWalletTransactions()
}
const clearSearch = () => {
    searchValue.value = ''
    fetchPlatformWalletTransactions()
}
const setPerPage = (perPage) => {
    per_page.value = perPage
    fetchPlatformWalletTransactions()
}
const setMonth = (month) => {
    selectedMonth.value = month
    fetchPlatformWalletTransactions()
}
const setYear = (year) => {
    selectedYear.value = year
    fetchPlatformWalletTransactions()
}
const setStatus = (status) => {
    selectedType.value = status
    fetchPlatformWalletTransactions()
}
const onChangePage = (page) => {
    currentPage.value = page
    fetchPlatformWalletTransactions()
}
/*
    LOADING
*/
const loading = ref({
    fetch: false,
    handlForm: false
})

/*
    FETCH
*/

const type = ref({
    order_payment: 'Thanh toán từ khách hàng',
    order_release_platform: 'Chi tiền từ sàn',
    order_release_seller: 'Người bán nhận tiền',
    refund: 'Hoàn tiền cho khách',
    withdraw: 'Rút tiền',
    deposit: 'Nạp tiền',
    adjustment: 'Điều chỉnh số dư',
    debit: 'Ghi nợ'
})
const wallet_transactions = ref([])
const fetchPlatformWalletTransactions = async () => {
    try {
        loading.value.fetch = true
        const res = await api.get('wallet-transactions/history', {
            params: {
                page: currentPage.value,
                search: searchValue.value,
                per_page: per_page.value,
                month: selectedMonth.value,
                year: selectedYear.value,
                type: selectedType.value
            }
        })
        if (res.status == 200) {
            wallet_transactions.value = res.data.data
            meta.value = res.data.meta
        }
    } catch (error) {
        message.emit('show-message', { title: "Lỗi", text: "Lấy dữ liệu lịch sử giao dịch thất bại.", type: "error" })
        console.log(error);

    } finally {
        loading.value.fetch = false
    }
}

const balance = ref(0)
const fetchPlatformWallet = async () => {
    try {
        const res = await api.get('wallets')
        if (res.status == 200) {
            balance.value = res.data.balance
        }
    } catch (error) {
        message.emit('show-message', { title: "Lỗi", text: "Lấy số dư sàn thất bại.", type: "error" })
        console.log(error);

    }
}
</script>
<style scoped>
.dropdown .active {
    background-color: var(--text-color);
    color: var(--white-color) !important;
}
</style>