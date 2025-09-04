<template>
    <main class="admin__content">
        <div
            class="p-3 bg-white border-bottom text-grey fs-14 fw-medium d-flex align-items-center justify-content-between">
            <p class="m-0">
                Danh Sách Người Dùng (<span class="text-color">{{ totalUser }}</span>)
            </p>
            <div class="d-flex align-items-center gap-2">
                <div class="admin__search">
                    <button @click="onSearch" type="button" class="search__button">
                        <i class="ri-search-2-line"></i>
                    </button>
                    <form @submit.prevent="onSearch">
                        <div class="d-flex align-items-center gap-1 position-relative">
                            <button type="submit" style="height: 38px" class="main-btn py-1 px-3 ">
                                <i class="ri-search-2-line"></i>
                            </button>
                            <input type="text" v-model="searchValue" placeholder="Tìm người dùng..."
                                class="form-control" />
                            <button v-if="searchValue" type="button" @click="searchValue = ''; onSearch()"
                                class="btn position-absolute top-50 end-0 translate-middle-y me-2 p-0 border-0 bg-transparent">
                                <i class="ri-close-line"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="p-10 admin-user">
            <div v-if="loading.fetch" style="height: 60vh;"
                class="d-flex align-items-center justify-content-center">
                <loading__dot />
            </div>
            <div v-else class="table-responsive fade-in">
                <div v-if="users.length === 0">
                    <no_data />
                </div>
                <table v-else class="table border">
                    <thead class="table-active" style="height: 50px;">
                        <tr class="lh-lg align-middle">
                            <th scope="col" class="min-w-50">
                                <div class="checkbox">
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="check-item" v-model="checkAll"
                                            @change="toggleAllSelection"/>
                                        <div class="checkbox-wrapper">
                                            <div class="checkbox-bg"></div>
                                            <svg fill="none" viewBox="0 0 24 24" class="checkbox-icon">
                                                <path stroke-linejoin="round" stroke-linecap="round" stroke-width="3"
                                                    stroke="currentColor" d="M4 12L10 18L20 6" class="check-path">
                                                </path>
                                            </svg>
                                        </div>
                                    </label>
                                </div>
                            </th>
                            <th scope="col" class="fs-14 fw-semibold min-w-150">Hình
                                Ảnh</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-300">Thông
                                Tin Tài Khoản</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-150">Xếp Hạng</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-150">Trạng
                                Thái</th>
                            <th scope="col" class="fs-14 fw-semibold text-center min-w-100">Thao
                                Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in users">
                            <td scope="row" class="align-middle">
                                <div class="checkbox me-2">
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="check-item" :value="user.id"
                                            v-model="selectedUsers"/>
                                        <div class="checkbox-wrapper">
                                            <div class="checkbox-bg"></div>
                                            <svg fill="none" viewBox="0 0 24 24" class="checkbox-icon">
                                                <path stroke-linejoin="round" stroke-linecap="round" stroke-width="3"
                                                    stroke="currentColor" d="M4 12L10 18L20 6" class="check-path">
                                                </path>
                                            </svg>
                                        </div>
                                    </label>
                                </div>
                            </td>

                            <td class="align-middle">
                                <img v-if="user.avatar" class="avatar" :src="`/imgs/users/${user.avatar}`">
                                <img v-else class="avatar" src="/imgs/user-default.jpg">
                            </td>
                            <td class="align-middle">
                                <p>{{ user.user_name }}</p>
                                <p>{{ user.phone }}</p>
                                <p>{{ user.email }}</p>
                            </td>
                            <td class="align-middle">
                                <span class="badge radius-2 text-bg-danger">{{ user.rank ?? 'Chưa có xếp hạng' }}</span>
                            </td>
                            <td class="align-middle">
                                <span v-if="user.status === 1"
                                    class="badge radius-2 text-bg-success">Hoạt động</span>
                                <span v-else class="badge radius-2 text-bg-warning">Tạm ngừng</span>
                            </td>
                            <td class="align-middle text-center">
                                <div class="more-wrapper">
                                    <button class="more-btn">
                                        <i class="ri-more-fill"></i>
                                    </button>
                                    <div class="more-popup">
                                        <!-- <button class="bg-info">Xem</button> -->
                                        <button v-if="user.status === 0" @click="changeStatus(user.id)"
                                            class="bg-success text-light">Mở</button>
                                        <button @click="changeStatus(user.id)" v-else class="bg-warning">Khóa</button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot class="table-active">
                        <tr class="lh-lg">
                            <th scope="col" class="align-middle">
                                <div class="checkbox align-items-center d-flex gap-1">
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="check-item" id="check_all" v-model="checkAll"
                                            @change="toggleAllSelection"/>
                                        <div class="checkbox-wrapper">
                                            <div class="checkbox-bg"></div>
                                            <svg fill="none" viewBox="0 0 24 24" class="checkbox-icon">
                                                <path stroke-linejoin="round" stroke-linecap="round" stroke-width="3"
                                                    stroke="currentColor" d="M4 12L10 18L20 6" class="check-path">
                                                </path>
                                            </svg>
                                        </div>
                                    </label>
                                    <span class="fw-medium">All</span>
                                </div>
                            </th>
                            <th scope="col"></th>
                            <th scope="col"></th>
                            <th scope="col"></th>
                            <th scope="col"></th>
                            <td class="align-middle text-center">
                                <div class="more-wrapper ">
                                    <button class="more-btn p-0">
                                        <i class="ri-more-fill"></i>
                                    </button>
                                    <div class="more-popup">
                                        <button @click="showUnlockModal" class="text-white bg-success px-2 py-0">
                                            Mở
                                        </button>
                                        <button @click="showLockModal" class="bg-warning px-2 py-0">
                                            Khóa
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
                <!-- Pagination -->
                <div class="d-flex align-items-center justify-content-end me-3">
                    <pagination :meta="meta" @changePage="onChangePage" />
                </div>
            </div>
        </div>

         <!-- Confirm Lock Modal -->
        <div v-if="isLockModal"
            class="modal-background container-fluid p-0 d-flex justify-content-center align-items-center">
            <div class="modal-edit-address modal-ezeshop bg-white shadow-sm px-4 pt-4 pb-2" style="max-width: 500px;">
                <div class="modal-ezeshop__top d-flex justify-content-between align-items-center">
                    <div class="modal-ezeshop__name fs-18 fw-500">Xác Nhận Khóa</div>
                    <div @click="isLockModal = false" class="modal-ezeshop__cancel cursor-pointer"><i
                            class="fa-solid fa-xmark fs-18"></i></div>
                </div>
                <!-- Content -->
                <div class="modal-ezeshop__main w-100">
                    Bạn có chắc muốn khóa tất cả tài khoản đã chọn không?
                </div>
                <div class="mt-3 modal-ezeshop__bottom d-flex justify-content-end w-100 align-items-center border-top">
                    <div class="modal-ezeshop__cancel me-4"><button @click="isLockModal = false"
                            class="white-btn py-2 px-4 fs-14">Huỷ</button></div>
                    <div class="modal-ezeshop__save"><button
                            @click="lockSelected"
                            class="main-btn py-2 px-4 fs-14" type="submit">
                            <loading__loader_circle size="15px" color="#fff" border="2px" v-if="loading.lock" />
                            <div v-else>Xác Nhận</div>
                        </button></div>
                </div>
            </div>
        </div>

        <!-- Confirm Unlock Modal -->
        <div v-if="isUnlockModal"
            class="modal-background container-fluid p-0 d-flex justify-content-center align-items-center">
            <div class="modal-edit-address modal-ezeshop bg-white shadow-sm px-4 pt-4 pb-2" style="max-width: 500px;">
                <div class="modal-ezeshop__top d-flex justify-content-between align-items-center">
                    <div class="modal-ezeshop__name fs-18 fw-500">Xác Nhận Mở Khóa</div>
                    <div @click="isUnlockModal = false" class="modal-ezeshop__cancel cursor-pointer"><i
                            class="fa-solid fa-xmark fs-18"></i></div>
                </div>
                <!-- Content -->
                <div class="modal-ezeshop__main w-100">
                    Bạn có chắc muốn mở khóa tất cả tài khoản đã chọn không?
                </div>
                <div class="mt-3 modal-ezeshop__bottom d-flex justify-content-end w-100 align-items-center border-top">
                    <div class="modal-ezeshop__cancel me-4"><button @click="isUnlockModal = false"
                            class="white-btn py-2 px-4 fs-14">Huỷ</button></div>
                    <div class="modal-ezeshop__save"><button
                            @click="unlockSelected"
                            class="main-btn py-2 px-4 fs-14" type="submit">
                            <loading__loader_circle size="15px" color="#fff" border="2px" v-if="loading.unlock" />
                            <div v-else>Xác Nhận</div>
                        </button></div>
                </div>
            </div>
        </div>
    </main>
</template>
<script setup>
import { ref, onMounted, watch } from 'vue'
import api from '@/configs/api'
import { formatDate } from '@/utils/formatDate'
import { formatPrice } from '@/utils/formatPrice'
import pagination from '@/components/pagination.vue'
import message from '@/utils/messageState'
import loading__dot from '@/components/loading/loading__dot.vue'
import loading__loader_circle from '@/components/loading/loading__loader-circle.vue'
import no_data from '@/components/no_data.vue'

const meta = ref({})
const currentPage = ref(1) // Trang hiện tại
const searchValue = ref('') // Giá trị tìm kiếm

// Danh sách user và tổng số
const users = ref([]) 
const totalUser = ref(0) 

// Trạng thái loading cho các thao tác
const loading = ref({
    fetch: false,
    lock: false,
    unlock: false
})


const getUsers = async () => {
    try {
        loading.value.fetch = true
        const res = await api.get('/user-list', {
            params: {
                page: currentPage.value,
                search: searchValue.value,
            }
        })
        users.value = res.data.users
        // console.log(users.value)
        totalUser.value = res.data.total
        meta.value = {
            current_page: res.data.current_page,
            last_page: res.data.last_page,
            total: res.data.total,
        }
    } catch (error) {
        console.error(error)
    } finally {
        loading.value.fetch = false
    }
}

// Đổi trang
const onChangePage = (page) => {
    currentPage.value = page
}

// Tìm kiếm
const onSearch = () => {
    currentPage.value = 1
    getUsers()
}

const changeStatus = async (id) => {
    try {
        await api.post(`/user/change-status/${id}`)
        getUsers()
        message.emit('show-message', {
            title: 'Thành công',
            text: 'Đổi trạng thái thành công',
            type: 'success'
        })
    } catch (err) {
        console.error(err)
    }
}

const selectedUsers = ref([]) 
const checkAll = ref(false) 

// Chọn hoặc bỏ chọn tất cả vai trò
const toggleAllSelection = () => {
    checkAll.value
        ? selectedUsers.value = users.value.map(user => user.id)
        : selectedUsers.value = []
}

// Theo dõi selectedUsers để cập nhật trạng thái checkAll
watch(selectedUsers, (newSelected) => {
    if (newSelected.length === users.value.length && users.value.length > 0) {
        checkAll.value = true
    } else {
        checkAll.value = false
    }
})

const isLockModal = ref(false)
const showLockModal = () => {
    isLockModal.value = true
}
const lockSelected = async () => {
    if (selectedUsers.value.length === 0) {
        message.emit('show-message', {
            title: 'Chú ý',
            text: 'Chưa chọn người dùng nào để khóa',
            type: 'warning'
        })
        isLockModal.value = false
        checkAll.value = false
        return
    }
    try {
        loading.value.lock = true
        await api.post('/user/lock-multiple', {
             ids: selectedUsers.value 
        })
        message.emit('show-message', {
            title: 'Thành công',
            text: 'Đã khóa các người dùng đã chọn',
            type: 'success'
        })
        selectedUsers.value = []
        checkAll.value = false
        getUsers()
    } catch (error) {
        message.emit('show-message', {
            title: 'Thất bại',
            text: 'Khóa thất bại',
            type: 'error'
        })
    } finally {
        loading.value.lock = false
        isLockModal.value = false
    }
}


const isUnlockModal = ref(false)
const showUnlockModal = () => {
    isUnlockModal.value = true
}
const unlockSelected = async () => {
    if (selectedUsers.value.length === 0) {
        message.emit('show-message', {
            title: 'Chú ý',
            text: 'Chưa chọn người dùng nào để mở',
            type: 'warning'
        })
        isUnlockModal.value = false
        checkAll.value = false
        return
    }
    try {
        loading.value.unlock = true
        await api.post('/user/unlock-multiple', {
             ids: selectedUsers.value 
        })
        message.emit('show-message', {
            title: 'Thành công',
            text: 'Đã mở các người dùng đã chọn',
            type: 'success'
        })
        selectedUsers.value = []
        checkAll.value = false
        getUsers()
    } catch (error) {
        message.emit('show-message', {
            title: 'Thất bại',
            text: 'Mở thất bại',
            type: 'error'
        })
    } finally {
        loading.value.unlock = false
        isUnlockModal.value = false
    }
}

// Theo dõi thay đổi trang để lấy lại dữ liệu
watch(currentPage, () => {
    getUsers()
})

// Khi component mounted, lấy danh sách User
onMounted(async () => {
    await getUsers()
})
</script>