<template>
    <div v-if="loading.fetch_all" class="d-flex align-items-center justify-content-center w-100">
        <loading__dot />
    </div>
    <div v-else class="__main">
        <div class="__overview bg-white mt-3 px-4 py-2 shadow-sm">
            <div class="__title fs-20 fw-semibold">Tổng quan</div>
            <div class="__content border mt-3 d-flex gap-3 p-4 flex-wrap">
                <div class="__left flex-fill d-flex flex-column">
                    <div class="__excess fw-500 fs-14 mb-3">
                        Số dư:
                        <span v-if="loading.fetch_balance">
                            <loading_loader_circle size="20px" border="3px" />
                        </span>
                        <span v-else class="fs-20">
                            {{
                                wallet_shop ? formatPrice(wallet_shop) : 0
                            }}
                        </span>
                    </div>

                    <div class="__payment-request">
                        <button @click="openPayoutForm()" :disabled="wallet_shop == 0"
                            class="main-btn redius-2 d-flex justify-content-center align-items-center"
                            style="width: 200px; height: 32px;">
                            <loading_loader_circle v-if="loading.preview_payout" border="3px" size="20px"
                                color="#fff" />
                            <span v-else>Yêu cầu thanh toán</span>
                        </button>
                    </div>
                </div>
                <div class="__right flex-fill">
                    <div class="__bank-account fw-semibold fs-16 mb-3">
                        Tài khoản ngân hàng
                        <button v-if="Object.keys(bank_info).length != 0" @click="isBankModalVisible = true"
                            class="fs-14 btn p-0 border-0 text-primary ms-4">
                            Cập nhật
                        </button>
                    </div>
                    <div v-if="loading.fetch_bank_info">
                        <loading_loader_circle size="20px" border="3px" />
                    </div>
                    <template v-else>
                        <div v-if="Object.keys(bank_info).length === 0">
                            <p class=" text-danger fs-14 fw-medium">Bạn chưa thiết lập tài khoản để rút tiền.
                                <button @click="isBankModalVisible = true" class="fs-14 btn p-0 border-0 text-primary">
                                    Thiết lập ngay
                                </button>
                            </p>
                        </div>
                        <div v-else class="__bank d-flex align-items-center">
                            <img :src="bank_info.bank_logo" alt="Logo ngân hàng" class="rounded"
                                style="width: 48px; height: 48px; object-fit: contain;" v-if="bank_info.bank_logo" />
                            <div class="__name fs-14 text-primary ps-1 flex-wrap">
                                {{ bank_info.bank_name }}
                                -
                                {{ bank_info.bank_account_name }}
                                -
                                {{ bank_info.bank_account_number }}
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
        <div class="__withdrawal-history bg-white px-4 py-2 shadow-sm mt-3">
            <div class="__title fs-20 fw-500">Lịch sử rút tiền</div>
            <loading_loader_circle v-if="loading.fetch_payout_history" border="3px" size="25px" />
            <div v-else class="table-responsive mt-3 fs-14">
                <div v-if="payouts.length == 0">
                    <p class="fs-16 fw-medium">Bạn chưa có yêu cầu rút tiền.</p>
                </div>
                <table v-else class="table align-middle">
                    <thead>
                        <tr>
                            <td scope="col" style="color: gray; font-weight: 600;">ID</td>
                            <th scope="col" style="color: gray; font-weight: 600;">Ngày Rút</th>
                            <th scope="col" style="color: gray; font-weight: 600;">Số Tiền</th>
                            <th scope="col" style="color: gray; font-weight: 600;">Trạng Thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="p in payouts" class="border-top">
                            <td>{{ p.payout_code }}</td>
                            <td>{{ p.requested_at }}</td>
                            <td>{{ formatPrice(p.net_amount) }}</td>
                            <td>
                                <span class="fw-medium" :class="statusColor(p.payout_status)">
                                    {{ statusLabel(p.payout_status) }}
                                </span>
                                <button @click="retryPayout(p.id)" v-if="p.approval_status == 'rejected'"
                                    class="bg-white border-0 text-primary ms-2">
                                    Tạo lại y/c
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div v-if="isBankModalVisible"
        class="modal-background container-fluid p-0 d-flex justify-content-center align-items-center">
        <div class="modal-edit-address modal-ezeshop bg-white shadow-sm px-4 pt-4 pb-2">
            <div class="modal-ezeshop__top d-flex justify-content-between align-items-center">
                <div class="modal-ezeshop__name fs-18 fw-500">Thiết Lập Tài Khoản Rút Tiền</div>
                <div @click="isBankModalVisible = false" class="modal-ezeshop__cancel cursor-pointer">
                    <i class="fa-solid fa-xmark fs-18"></i>
                </div>
            </div>
            <div class="p-2 bg-white rounded">
                <label class="form-label">Ngân hàng</label>
                <input v-model="bankSearch" @focus="showDropdown = true" @blur="hideDropdown" type="text"
                    class="form-control radius-2 mb-2" placeholder="Nhập tên ngân hàng" />
                <ul v-if="showDropdown && filteredBanks.length"
                    class="list-group position-absolute bg-white radius-2 z-3 w-75"
                    style="max-height: 300px; overflow-y: auto; max-width: 500px;">
                    <li v-for="bank in filteredBanks" :key="bank.code" class="list-group-item list-group-item-action"
                        @mousedown.prevent="selectBank(bank)">
                        <img :src="bank.logo" alt="logo" class="me-2" width="20" height="20"
                            style="object-fit: cover;" />
                        {{ bank.name }}
                    </li>
                </ul>
                <div class="text-danger small" v-if="errors.bank">* {{ errors.bank }}</div>

                <label class="form-label redius-2 mt-3">Số tài khoản</label>
                <input v-model="accountNumber" type="text" class="form-control mb-3" placeholder="Nhập số tài khoản" />
                <div class="text-danger small" v-if="errors.accountNumber">* {{ errors.accountNumber }}</div>
                <label class="form-label redius-2 mt-3">Tên Chủ Tài Khoản</label>

                <input v-model="accountName" type="text" class="form-control mb-3"
                    placeholder="Nhập tên tài khoản của bạn" />
                <div class="text-danger small mb-2" v-if="errors.accountName">* {{ errors.accountName }}</div>

                <button :disabled="loading.save_bank_info"
                    class="main-btn py-1 px-2 d-flex align-items-center justify-content-center"
                    style="width: 150px; height: 40px;" @click="saveBank">
                    <loading_loader_circle v-if="loading.save_bank_info" size="18px" border="3px" color="#fff" />
                    <span v-else>Lưu Thông Tin</span>
                </button>
            </div>
        </div>
    </div>
    <div v-if="isPayoutModalOpen"
        class="modal-background container-fluid p-0 d-flex justify-content-center align-items-center">
        <div class="modal-edit-address modal-ezeshop bg-white shadow-sm px-4 pt-4 pb-2">
            <!-- Header -->
            <div class="modal-ezeshop__top d-flex justify-content-between align-items-center">
                <div class="modal-ezeshop__name fs-18 fw-500">Yêu Cầu Lệnh Rút Tiền</div>
                <div @click="isPayoutModalOpen = false" class="modal-ezeshop__cancel cursor-pointer">
                    <i class="fa-solid fa-xmark fs-18"></i>
                </div>
            </div>

            <!-- Nội dung -->
            <div class="bg-light p-3">
                <!-- Số dư hiện tại -->
                <div class="d-flex justify-content-between mb-3">
                    <span class="fs-14 fw-medium">Số dư hiện tại:</span>
                    <strong class="fs-18">{{ formatPrice(wallet_shop) }}</strong>
                </div>

                <!-- Nhập số tiền muốn rút -->
                <div class="mb-3">
                    <label class="form-label fs-14 fw-medium">Số tiền muốn rút</label>
                    <div class="input-group">
                        <input type="number" class="form-control radius-2" v-model="payout_amount"
                            placeholder="Nhập số tiền..." />
                        <button class="white-btn fs-14 px-3" type="button" @click="payout_amount = wallet_shop">
                            Rút tất cả
                        </button>
                    </div>
                    <small v-if="payout_amount > wallet_shop" class="text-danger">
                        Số tiền rút không được lớn hơn số dư hiện tại.
                    </small>
                </div>
                <!-- Submit -->
                <div class="d-flex justify-content-end mt-3">
                    <button class="main-btn redius-2 d-flex justify-content-center align-items-center"
                        @click="requestPayout()" :disabled="payout_amount <= 0 || payout_amount > wallet_shop"
                        style="width: 200px; height: 32px;">
                        <loading_loader_circle v-if="loading.request_payout" border="3px" size="20px" color="#fff" />
                        <span v-else>Xác Nhận Rút</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import api from '@/configs/api'
import axios from 'axios'
import loading_loader_circle from '@/components/loading/loading__loader-circle.vue'
import loading__dot from '@/components/loading/loading__dot.vue'
import message from '@/utils/messageState'
import { formatPrice } from '@/utils/formatPrice'
import { payoutStatusMap } from '@/utils/payoutStatusMap'

// Trạng thái loading
const loading = ref({
    fetch_all: false,
    fetch_balance: false,
    fetch_bank_info: false,
    save_bank_info: false,
    request_payout: false,
    fetch_payout_history: false,
    preview_payout: false
})

//==================== BALANCE ====================//
const wallet_shop = ref(0)
const getBalance = async () => {
    try {
        loading.value.fetch_balance = true
        const res = await api.get('wallets/user')
        wallet_shop.value = res.data
    } catch (error) {
        console.log(error);
    } finally {
        loading.value.fetch_balance = false
    }
}
//==================== END BALANCE ====================//

//==================== PAYOUTS =================//
const payouts = ref([])
const getPayoutsByShop = async () => {
    try {
        loading.value.fetch_payout_history = true
        const res = await api.get('payouts/shop')
        payouts.value = res.data
    } catch (error) {
        console.log(error)
    } finally {
        loading.value.fetch_payout_history = false
    }
}
const payout_amount = ref(0)
const isPayoutModalOpen = ref(false)
const openPayoutForm = async () => {
    if (wallet_shop.value === 0) {
        message.emit('show-message', {
            title: 'Cảnh báo',
            text: 'Hiện tại số dư của bạn là 0',
            type: 'warning'
        })
        return
    } else {
        isPayoutModalOpen.value = true
    }
}

const requestPayout = async () => {
    try {
        if (wallet_shop.value === 0 || payout_amount.value == 0 || wallet_shop.value < payout_amount.value) {
            message.emit('show-message', {
                title: 'Cảnh báo',
                text: 'Không hợp lệ',
                type: 'warning'
            })
            return
        }

        loading.value.request_payout = true;

        await api.post('/payouts', {
            amount: payout_amount.value
        });
        isPayoutModalOpen.value = false
        // Gọi lại balance để làm mới
        await getBalance()
        await getPayoutsByShop()

    } catch (error) {
        console.log(error);
    } finally {
        loading.value.request_payout = false;
    }
}

const statusLabel = (status) => {
    return payoutStatusMap[status]?.label || 'Không rõ'
}

const statusColor = (status) => {
    return `text-${payoutStatusMap[status]?.color || 'secondary'}`
}

const retryPayout = async (payoutId) => {
    if (!payoutId) {
        message.emit('show-message', {
            title: 'Thông báo',
            text: 'Thông tin tạo lại y/c không hợp lệ.',
            type: 'warning'
        })
        return
    }
    try {
        const res = await api.post(`payouts/${payoutId}/retry`)
        if (res.status == 200) {
            const inx = payouts.value.findIndex(item => item.id == payoutId)
            if (inx >= 0) {
                payouts.value[inx] = res.data
            }
            message.emit('show-message', {
                title: 'Thông báo',
                text: 'Tạo lại y/c rút tiền thành công.',
                type: 'warning'
            })
        }

    } catch (err) {
        console.log(err);
    } finally {

    }
}
//==================== END PAYOUT =================//

//==================== BANK ====================//
// Dữ liệu hiển thị tài khoản ngân hàng
const bank_info = ref({})

// Biến để hiển thị modal
const isBankModalVisible = ref(false)

// Form state
const selectedBank = ref({})
const accountNumber = ref('')
const accountName = ref('')
const bankSearch = ref('')
const errors = ref({})

// Dropdown ngân hàng
const showDropdown = ref(false)
const bankList = ref([])

// Lọc danh sách ngân hàng theo từ khóa
const filteredBanks = computed(() => {
    const keyword = bankSearch.value.toLowerCase()
    return bankList.value.filter(bank =>
        bank.name.toLowerCase().includes(keyword) ||
        bank.shortName?.toLowerCase().includes(keyword)
    )
})

// Lấy thông tin ngân hàng hiện tại của shop
const getBankAccountInfo = async () => {
    try {
        loading.value.fetch_bank_info = true
        const res = await api.get('/shops/bank-account')
        mapApiToForm(res.data)
    } catch (error) {
        console.error(error)
    } finally {
        loading.value.fetch_bank_info = false
    }
}

// Ánh xạ dữ liệu từ API vào form
const mapApiToForm = (data) => {
    bank_info.value = data

    selectedBank.value = {
        name: data.bank_name,
        short_name: data.bank_short_name,
        logo: data.bank_logo,
        bin: data.bin_account,
    }

    accountNumber.value = data.bank_account_number
    accountName.value = data.bank_account_name
    bankSearch.value = data.bank_name
}

// Tạo payload từ form để gửi lên server
const mapFormToPayload = () => ({
    bank_account_name: accountName.value,
    bank_account_number: accountNumber.value,
    bank_name: selectedBank.value.name,
    bank_short_name: selectedBank.value.short_name,
    bank_logo: selectedBank.value.logo,
    bin_account: selectedBank.value.bin
})

// Lấy danh sách ngân hàng từ VietQR
const fetchBanks = async () => {
    try {
        const res = await axios.get('https://api.vietqr.io/v2/banks')
        bankList.value = res.data.data
    } catch (err) {
        console.error('Lỗi khi tải danh sách ngân hàng', err)
    }
}

// Khi chọn ngân hàng trong dropdown
const selectBank = (bank) => {
    selectedBank.value = bank
    bankSearch.value = bank.name
    showDropdown.value = false
}

// Ẩn dropdown ngân hàng
const hideDropdown = () => {
    setTimeout(() => {
        showDropdown.value = false
    }, 200)
}

// Validate dữ liệu form
const validateBankForm = () => {
    errors.value.bank = selectedBank.value?.name ? '' : 'Vui lòng chọn ngân hàng'
    errors.value.accountNumber = accountNumber.value ? '' : 'Vui lòng nhập số tài khoản'
    errors.value.accountName = accountName.value ? '' : 'Vui lòng nhập tên chủ tài khoản'

    return !errors.value.bank && !errors.value.accountNumber && !errors.value.accountName
}

// Lưu thông tin tài khoản ngân hàng
const saveBank = async () => {
    if (!validateBankForm()) return

    const payload = mapFormToPayload()
    try {
        loading.value.save_bank_info = true
        const res = await api.put('shops/bank-account', payload)
        mapApiToForm(res.data)
        isBankModalVisible.value = false
        message.emit('show-message', {
            title: 'Thông báo',
            text: 'Lưu thông tin ngân hàng thành công.',
            type: 'success'
        })
    } catch (error) {
        console.error(error)
        if (error.response?.status === 422) {
            message.emit('show-message', {
                title: 'Lỗi',
                text: error.response.data.message,
                type: 'error'
            })
        }
    } finally {
        loading.value.save_bank_info = false
    }
}

//==================== END BANK ====================//
// On mounted
onMounted(async () => {
    loading.value.fetch_all = true
    await getBankAccountInfo()
    await fetchBanks()
    await getBalance()
    await getPayoutsByShop()
    loading.value.fetch_all = false
})
</script>