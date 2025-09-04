<template>
    <main class="admin__content p-2">
        <!-- The Content -->
        <div class="my-1">
            <h3 class="fs-18">Quản Lý Thuế Nhà Nước</h3>
        </div>
        <div class="row g-2">
            <div class="col-md-4 col-lg-3">
                <form @submit.prevent="addTax">
                    <div class="mb-3">
                        <label class="form-label fs-14">Tên Thuế<span class="text-color">*</span></label>
                        <input v-model="tax.name" type="text" class="form-control form-control__input">
                        <p class="text-danger" v-if="errors.name">{{ Array.isArray(errors.name) ? errors.name[0] :
                            errors.name }}
                        </p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fs-14">Phần Trăm VAT<span class="text-color">*</span></label>
                        <input type="number" v-model="tax.vat_percent" :min="0" :value="0"
                            class="form-control form-control__input">
                        <p class="text-danger" v-if="errors.vat_percent">{{ Array.isArray(errors.vat_percent) ?
                            errors.vat_percent[0] :
                            errors.vat_percent }}
                        </p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fs-14">Phần Trăm TAX<span class="text-color">*</span></label>
                        <input type="number" v-model="tax.tax_percent" :min="0" :value="0"
                            class="form-control form-control__input">
                        <p class="text-danger" v-if="errors.tax_percent">{{ Array.isArray(errors.tax_percent) ?
                            errors.tax_percent[0] :
                            errors.tax_percent }}
                        </p>
                    </div>
                    <div class="mb-3">
                        <label for="comment">Mô Tả</label>
                        <textarea v-model="tax.description" class="form-control form-control__input"
                            rows="2"></textarea>
                    </div>
                    <button type="submit" class="main-btn p-1 px-2">Thêm Thuế</button>
                </form>
            </div>
            <div class="col-md-8 col-lg-9">
                <div v-if="is_loading"
                    class="d-flex align-items-center justify-content-center p-4 text-center flex-column bg-white"
                    style="min-height: 500px">
                    <loading__dot />
                </div>
                <div v-else class="bg-white p-2">
                    <!-- Top Category Show -->
                    <div class="d-flex align-items-center gap-1 justify-content-between my-2">
                        <p class="fs-14 d-flex m-0">
                            <span class="text-color me-1">{{ totalTax }} Thuế</span>
                        </p>
                        <form @submit.prevent="onSearch" class="d-flex align-items-center gap-1">
                            <div class="position-relative">
                                <!-- ô input tìm kiếm -->
                                <input type="text" v-model="searchValue" placeholder="Tìm thuế..."
                                    class="form-control form-control__input" />
                                <!-- Nút xóa (hiện khi có nội dung) -->
                                <button v-if="searchValue" @click="clearSearch" type="button"
                                    class="btn position-absolute top-50 end-0 translate-middle-y me-2 p-0 border-0 bg-transparent">
                                    <i class="ri-close-line"></i>
                                </button>
                            </div>

                            <button type="submit" style="height: 37px" class="main-btn py-1 px-3"><i
                                    class="ri-search-2-line"></i></button>
                        </form>
                    </div>
                    <div class="table-responsive">
                        <table class="table border table__fee">
                            <thead class="table-active">
                                <tr class="lh-lg align-middle">
                                    <th scope="col" class="min-w-50">
                                        <div class="checkbox me-2">
                                            <label class="checkbox-label">
                                                <input type="checkbox" @change="toggleSelectionAll($event)"
                                                    :checked="isAllSelected" />
                                                <div class="checkbox-wrapper">
                                                    <div class="checkbox-bg"></div>
                                                    <svg fill="none" viewBox="0 0 24 24" class="checkbox-icon">
                                                        <path stroke-linejoin="round" stroke-linecap="round"
                                                            stroke-width="3" stroke="currentColor" d="M4 12L10 18L20 6"
                                                            class="check-path"></path>
                                                    </svg>
                                                </div>
                                            </label>
                                        </div>
                                    </th>
                                    <th scope="col" class="fs-14 fw-semibold min-w-150">Tên</th>
                                    <th scope="col" class="fs-14 fw-semibold min-w-100">Phí VAT</th>
                                    <th scope="col" class="fs-14 fw-semibold min-w-150">Phí TAX</th>
                                    <th scope="col" class="fs-14 fw-semibold min-w-200">Mô Tả</th>
                                    <th scope="col" class="fs-14 fw-semibold text-center min-w-100">Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="tax in taxes" :key="tax.id">
                                    <td scope="row" class="align-middle">
                                        <div class="checkbox me-2">
                                            <label class="checkbox-label">
                                                <input type="checkbox" @click="toggleSelection(tax.id)"
                                                    :checked="isAllSelected" />
                                                <div class="checkbox-wrapper">
                                                    <div class="checkbox-bg"></div>
                                                    <svg fill="none" viewBox="0 0 24 24" class="checkbox-icon">
                                                        <path stroke-linejoin="round" stroke-linecap="round"
                                                            stroke-width="3" stroke="currentColor" d="M4 12L10 18L20 6"
                                                            class="check-path">
                                                        </path>
                                                    </svg>
                                                </div>
                                            </label>
                                        </div>
                                    </td>
                                    <td class="align-middle fs-14">{{ tax.name }}</td>
                                    <td class="align-middle fs-14">{{ tax.vat_percent }}</td>
                                    <td class="align-middle fs-14">{{ tax.tax_percent }}</td>
                                    <td class="align-middle fs-14">{{ tax.description ?? ' — ' }}</td>
                                    <td class="align-middle text-center py-3">
                                        <div class="more-wrapper">
                                            <button class="more-btn one-button">
                                                <i class="ri-more-fill"></i>
                                            </button>
                                            <div class="more-popup">
                                                <button @click="showModalEdit(tax.id)" class="bg-warning">
                                                    Sửa
                                                    <i class="ri-edit-line"></i>
                                                </button>
                                                <button @click="confirmDelete(tax.id)" class="text-white bg-danger">
                                                    Xóa
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Pagination -->
                                <div class="d-flex align-items-center justify-content-end me-3">
                                    <pagination :meta="meta" @changePage="onChangePage" />
                                </div>
                            </tbody>
                            <tfoot class="table-active">
                                <tr class="lh-lg">
                                    <th scope="col" class="align-middle">
                                        <div class="checkbox align-items-center d-flex gap-1">
                                            <label class="checkbox-label">
                                                <input type="checkbox" @change="toggleSelectionAll($event)"
                                                    :checked="isAllSelected" />
                                                <div class="checkbox-wrapper">
                                                    <div class="checkbox-bg"></div>
                                                    <svg fill="none" viewBox="0 0 24 24" class="checkbox-icon">
                                                        <path stroke-linejoin="round" stroke-linecap="round"
                                                            stroke-width="3" stroke="currentColor" d="M4 12L10 18L20 6"
                                                            class="check-path"></path>
                                                    </svg>
                                                </div>
                                            </label>
                                            <label class="fs-17 cursor-pointer" for="check_all">All</label>
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
                                                <button @click="confirmDeleteSeleted" type="button"
                                                    class="text-white bg-danger px-2 py-0">
                                                    Xóa Tất Cả
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="isModelEdit"
            class="modal-background container-fluid p-0 d-flex justify-content-center align-items-center">
            <div class="modal-edit-address modal-ezeshop bg-white shadow-sm px-4 pt-4 pb-2">
                <div class="modal-ezeshop__top d-flex justify-content-between align-items-center">
                    <div class="modal-ezeshop__name fs-18 fw-500">Sửa Thuế</div>
                    <div @click="isModelEdit = false" class="modal-ezeshop__cancel cursor-pointer"><i
                            class="fa-solid fa-xmark fs-18"></i></div>
                </div>
                <!-- Content -->
                <div class="modal-ezeshop__main w-100 p-1">
                    <form id="form-edit-tax" @submit.prevent="editTax">
                        <div class="mb-3">
                            <label class="form-label fs-14">Tên Thuế<span class="text-color">*</span></label>
                            <input v-model="taxEdit.name" type="text" class="form-control form-control__input">
                            <p class="text-danger" v-if="errors.name">{{ Array.isArray(errors.name) ? errors.name[0] :
                                errors.name }}
                            </p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fs-14">Phần Trăm VAT<span class="text-color">*</span></label>
                            <input type="number" v-model="taxEdit.vat_percent" :min="0" :value="0"
                                class="form-control form-control__input">
                            <p class="text-danger" v-if="errors.vat_percent">{{ Array.isArray(errors.vat_percent) ?
                                errors.vat_percent[0] :
                                errors.vat_percent }}
                            </p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fs-14">Phần Trăm TAX<span class="text-color">*</span></label>
                            <input type="number" v-model="taxEdit.tax_percent" :min="0" :value="0"
                                class="form-control form-control__input">
                            <p class="text-danger" v-if="errors.tax_percent">{{ Array.isArray(errors.tax_percent) ?
                                errors.tax_percent[0] :
                                errors.tax_percent }}
                            </p>
                        </div>
                        <div class="mb-3">
                            <label for="comment">Mô Tả</label>
                            <textarea v-model="taxEdit.description" class="form-control form-control__input"
                                rows="2"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-ezeshop__bottom d-flex justify-content-end w-100 align-items-center border-top">
                    <div class="modal-ezeshop__cancel me-4"><button @click="isModelEdit = false"
                            class="white-btn py-2 px-4 fs-14">Huỷ</button></div>
                    <div class="modal-ezeshop__save"><button form="form-edit-tax" class="main-btn py-2 px-4 fs-14"
                            type="submit">Cập
                            Nhật</button></div>
                </div>
            </div>
        </div>
        <!-- End Model Edit Tax -->

        <div v-if="isDeleteModal"
            class="modal-background container-fluid p-0 d-flex justify-content-center align-items-center">
            <div class="modal-edit-address modal-ezeshop bg-white shadow-sm px-4 pt-4 pb-2" style="max-width: 500px">
                <div class="modal-ezeshop__top d-flex justify-content-between align-items-center">
                    <div class="modal-ezeshop__name fs-18 fw-500">Xác Nhận Xóa Thuế</div>
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
                        <button type="submit" class="main-btn py-2 px-4 fs-14"
                            @click="taxIdToDelete ? deleteTax() : deleteSelected()">
                            Xác Nhận
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Model Cate Confirm-->
    </main>
</template>
<script setup>
import { onMounted, ref, computed } from 'vue'
import api from '@/configs/api'
import pagination from '@/components/pagination.vue'
import message from '@/utils/messageState'
import loading__dot from '@/components/loading/loading__dot.vue'
/*
    GET
*/
const taxes = ref([])
const totalTax = ref(0)
const getTaxes = async () => {
    try {
        is_loading.value = true
        const res = await api.get(`/taxes`, {
            params: {
                page: currentPage.value,
                search: searchValue.value
            }
        })
        if (res.status == 200) {
            taxes.value = res.data.taxes
            totalTax.value = res.data.total
            meta.value = res.data.meta
            is_loading.value = false
        }
    } catch (error) {
        console.error(error)
    } finally {
        is_loading.value = false
    }
}

/*
    ADD
*/
const tax = ref({
    name: '',
    vat_percent: 0,
    tax_percent: 0,
    description: ''
})
const addTax = async () => {
    if (!validation(tax)) return
    try {
        is_loading.value = true
        const res = await api.post(`/taxes`, tax.value)
        if (res.status == 201) {
            Object.assign(tax.value, { name: '', vat_percent: 0, tax_percent: 0, description: '' })
            //Thông báo
            message.emit('show-message', {
                title: 'Thành công',
                text: 'Thêm thuế thành công.',
                type: 'success'
            })
            //Gọi lại để phân trang.
            getTaxes()
        }
    } catch (error) {
        if (error.response && error.response.status === 422) {
            errors.value = error.response.data.errors
        } else {
            console.warn('Lỗi validation:', error)
        }
    } finally {
        is_loading.value = false
    }
}

/*
   EDIT
*/
const taxEdit = ref({
    id: null,
    name: '',
    vat_percent: 0,
    tax_percent: 0,
    description: ''
})
const isModelEdit = ref(false)
const showModalEdit = (tax_id) => {
    errors.value = {}
    isModelEdit.value = true
    const found = taxes.value.find((tax) => tax.id == tax_id)
    taxEdit.value = { ...found }
}
const editTax = async () => {
    if (!validation(taxEdit)) return
    try {
        is_loading.value = true
        const res = await api.put(`/taxes/${taxEdit.value.id}`, taxEdit.value)
        if (res.status == 200) {
            const index = taxes.value.findIndex(tax => tax.id === taxEdit.value.id)
            if (index !== -1) {
                taxes.value[index] = { ...res.data.tax }
            }
            message.emit('show-message', {
                title: 'Thành công',
                text: res.data.message,
                type: 'success'
            })
            isModelEdit.value = false
            is_loading.value = false
        }
    } catch (error) {
        if (error.response && error.response.status === 422) {
            errors.value = error.response.data.errors
        } else {
            console.warn('Lỗi validation:', error)
        }
    } finally {
        is_loading.value = false
    }
}

/*
    DELETE
*/
const taxIdToDelete = ref(null)
const isDeleteModal = ref(false)
const selectedIds = ref([])
const confirmDelete = (tax_id) => {
    taxIdToDelete.value = tax_id
    isDeleteModal.value = true
}
const closeModelDelete = () => {
    taxIdToDelete.value = null
    isDeleteModal.value = false
}
const deleteTax = async () => {
    if (!taxIdToDelete.value) return
    try {
        is_loading.value = true
        const res = await api.delete(`/taxes/${taxIdToDelete.value}`)
        if (res.status == 200) {
            taxes.value = taxes.value.filter((tax) => tax.id !== taxIdToDelete.value)
            //Thông báo
            message.emit('show-message', {
                title: 'Thành công',
                text: 'Xóa thuế thành công.',
                type: 'success'
            })
            is_loading.value = false
        }
    } catch (error) {
        if (error && error.status == 400 && error.response.data.message) {
            //Thông báo
            message.emit('show-message', {
                title: 'Lỗi',
                text: error.response.data.message,
                type: 'error'
            })
        }
    } finally {
        isDeleteModal.value = false
        taxIdToDelete.value = null
        is_loading.value = false
    }
}
const isAllSelected = computed(() => {
    const allTaxIds = taxes.value.map(tax => tax.id)
    return selectedIds.value.length === allTaxIds.length
})
const toggleSelection = (id) => {
    if (selectedIds.value.includes(id)) {
        selectedIds.value = selectedIds.value.filter(item => item !== id)
    } else {
        selectedIds.value.push(id)
    }
}
const toggleSelectionAll = (e) => {
    const checked = e.target.checked
    const allTaxIds = taxes.value.map(tax => tax.id)
    if (checked) {
        selectedIds.value = allTaxIds
    } else {
        selectedIds.value = []
    }
}
const confirmDeleteSeleted = () => {
    if (selectedIds.value.length === 0) {
        //Thông báo
        message.emit('show-message', {
            title: 'Cảnh báo',
            text: 'Vui lòng chọn thuế để xóa.',
            type: 'warning'
        })
        return
    }
    isDeleteModal.value = true
}
const deleteSelected = async () => {
    try {
        is_loading.value = true
        const res = await api.delete(`/taxes/delete-multiple`, {
            data: { ids: selectedIds.value }
        })
        if (res.status == 200) {
            //Thông báo
            message.emit('show-message', {
                title: 'Thông báo',
                text: `${res.data.notice}: ${res.data.not_deleted_names}`,
                type: 'info'
            })
            selectedIds.value = []
            isDeleteModal.value = false
            currentPage.value = 1
            is_loading.value = false
            getTaxes()
        }
    } catch (error) {
        console.error("Lỗi khi xóa:", error)
    } finally {
        is_loading.value = false
    }
}

/*
    SUPPORT
*/
const is_loading = ref(false)
const errors = ref({})
const validation = (tax) => {
    errors.value = {}

    if (!tax.value.name.trim()) {
        errors.value.name = "Tên thuế không được để trống."
    }

    if (tax.value.vat_percent == null || tax.value.vat_percent === '') {
        errors.value.vat_percent = "Thuế VAT không được để trống."
    } else if (tax.value.vat_percent < 0 || tax.value.vat_percent > 100) {
        errors.value.vat_percent = "Thuế VAT phải từ 0 đến 100."
    }

    if (tax.value.tax_percent == null || tax.value.tax_percent === '') {
        errors.value.tax_percent = "Thuế TAX không được để trống."
    } else if (tax.value.tax_percent < 0 || tax.value.tax_percent > 100) {
        errors.value.tax_percent = "Thuế TAX phải từ không đến 100."
    }

    return Object.keys(errors.value).length === 0
}


/*  
    PAGINATION
*/
const meta = ref({})
const currentPage = ref(1)
const onChangePage = (page) => {
    currentPage.value = page
    getTaxes()
}
/*
    SEARCH 
*/
const searchValue = ref('')
const onSearch = () => {
    currentPage.value = 1
    getTaxes()
}
const clearSearch = () => {
    searchValue.value = ''
    getTaxes()
}

/*
    LOAD
*/
onMounted(async () => {
    await getTaxes()
})
</script>