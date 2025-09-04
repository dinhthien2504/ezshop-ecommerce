<template>
    <div class="admin__content">
        <div
            class="p-3 bg-white border-bottom text-grey fs-14 fw-medium d-flex align-items-center justify-content-between">
            <p class="m-0">
                Danh Sách Ngành Hàng
            </p>
            <div class="d-flex align-items-center gap-2">
                <div class="admin__search">
                    <button type="button" @click="onSearch" class="search__button">
                        <i class="ri-search-2-line"></i>
                    </button>
                    <form @submit.prevent="onSearch">
                        <div class="d-flex align-items-center gap-1 position-relative">
                            <button type="submit" style="height: 38px" class="main-btn py-1 px-3 ">
                                <i class="ri-search-2-line"></i>
                            </button>
                            <input type="text" placeholder="Tìm danh mục..." v-model="searchValue"
                                class="form-control" />
                            <button v-if="searchValue" @click="clearSearch" type="button"
                                class="btn position-absolute top-50 end-0 translate-middle-y me-2 p-0 border-0 bg-transparent">
                                <i class="ri-close-line"></i>
                            </button>
                        </div>
                    </form>
                </div>

                <button type="button" @click="isModalAdd = true" class="main-btn px-3" style="height: 38px;">
                    <span class="d-none d-lg-block">Thêm Ngành Hàng</span>
                    <span class="d-block d-lg-none"><i class="ri-add-fill"></i></span>
                </button>
            </div>
        </div>
        <div class="p-10">
            <div v-if="loading.fetch_list" style="height: 60vh;"
                class="d-flex align-items-center justify-content-center">
                <loading__dot />
            </div>
            <div v-else class="table-responsive fade-in">
                <div v-if="categories_tree.length === 0">
                    <no_data />
                </div>
                <table v-else class="table border">
                    <thead class="table-active">
                        <tr class="lh-lg align-middle">
                            <th scope="col" class="min-w-50">
                                <div class="checkbox">
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="check-item" @change="toggleSelectionAll($event)"
                                            :checked="isAllSelected" />
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
                            <th scope="col" class="fs-14 fw-semibold min-w-100">Ảnh</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-150">Tên</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-250">Mô Tả</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-150">Thuế</th>
                            <th scope="col" class="fs-14 fw-semibold min-w-150">Phí</th>
                            <th scope="col" class="fs-14 fw-semibold text-center min-w-100">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <categoryList :categories_tree="categories_tree" @edit-category="showModalEdit"
                            @delete-category="openDeleteModal" @select-category="toggleSelection"
                            :selected="selectedCategoryIds" :taxes="taxes" :fees="fees" />
                    </tbody>
                    <tfoot class="table-active">
                        <tr class="lh-lg">
                            <th scope="col" class="align-middle">
                                <div class="checkbox align-items-center d-flex gap-1">
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="check-item" @change="toggleSelectionAll($event)"
                                            :checked="isAllSelected" />
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
                            <th scope="col"></th>
                            <td class="align-middle text-center">
                                <div class="more-wrapper ">
                                    <button class="more-btn p-0">
                                        <i class="ri-more-fill"></i>
                                    </button>
                                    <div class="more-popup">
                                        <button @click="openDeleteModal()" class="text-white bg-danger px-2 py-0">
                                            Xóa Tất Cả
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
                <!-- Pagination -->
                <div class="d-flex align-items-center justify-content-start me-3">
                    <pagination :meta="meta" @changePage="onChangePage" />
                </div>
            </div>
        </div>
    </div>
    <div v-if="isModalAdd"
        class="modal-background container-fluid p-0 d-flex justify-content-center align-items-center">
        <div class="modal-edit-address modal-ezeshop bg-white shadow-sm px-4 pt-4 pb-2">
            <div class="modal-ezeshop__top d-flex justify-content-between align-items-center">
                <div class="modal-ezeshop__name fs-18 fw-500">Thêm Danh Mục</div>
                <div @click="isModalAdd = false" class="modal-ezeshop__cancel cursor-pointer"><i
                        class="fa-solid fa-xmark fs-18"></i></div>
            </div>
            <!-- Content -->
            <div class="modal-ezeshop__main w-100 p-1">
                <form id="form-add-cate" @submit.prevent="addCategory" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="name" class="form-label fs-14">Tên Danh Mục<span class="text-color">*</span></label>
                        <input v-model="category.name" type="text" class="form-control form-control__input">
                        <p class="text-danger" v-if="errors.name">{{ Array.isArray(errors.name) ? errors.name[0] :
                            errors.name }}
                        </p>
                    </div>
                    <div class="mb-3">
                        <label for="category_parent" class="form-label fs-14">Danh Mục Cha</label>
                        <select v-model="category.parent_id" id="category_parent"
                            class="form-control form-control__input form-select cursor-pointer">
                            <option value="">Không có</option>
                            <categoryOption :categories_tree="categories_full_tree" />
                        </select>
                        <p class="text-danger" v-if="errors.parent_id">{{ Array.isArray(errors.parent_id) ?
                            errors.parent_id[0] :
                            errors.parent_id }}</p>
                    </div>
                    <div class="mb-3">
                        <label for="category_parent" class="form-label fs-14">
                            Thuế<span class="text-color">*</span>
                        </label>
                        <select v-model="category.tax_id" id="category_parent"
                            class="form-control form-control__input form-select cursor-pointer">
                            <option v-for="tax in taxes" :value="tax.id">{{ tax.name }}</option>
                        </select>
                        <p class="text-danger" v-if="errors.tax_id">{{ Array.isArray(errors.tax_id) ?
                            errors.tax_id[0] :
                            errors.tax_id }}</p>
                    </div>
                    <div class="mb-3">
                        <label for="category_parent" class="form-label fs-14">
                            Phí<span class="text-color">*</span>
                        </label>
                        <select v-model="category.fee_id" id="category_parent"
                            class="form-control form-control__input form-select cursor-pointer">
                            <option v-for="fee in fees" :value="fee.id">{{ fee.name }}</option>
                        </select>
                        <p class="text-danger" v-if="errors.fee_id">{{ Array.isArray(errors.fee_id) ?
                            errors.fee_id[0] :
                            errors.fee_id }}</p>
                    </div>
                    <div class="mb-3">
                        <label for="comment">Mô Tả</label>
                        <textarea v-model="category.description" class="form-control form-control__input"
                            rows="2"></textarea>
                    </div>
                    <div class="mb-3 mt-3">
                        <label for="image" class="form-label fs-14">Hình Ảnh<span
                                class="text-color">*</span></label><br>
                        <label class="category__box--image" for="image">
                            <img :src="imageData.imagePreview || imageData.defaultImage" alt="upload icon">
                        </label>
                        <input type="file" hidden id="image" accept="image/*" @change="(e) => uploadImage(e, category)">
                    </div>
                </form>
            </div>
            <div class="modal-ezeshop__bottom d-flex justify-content-end w-100 align-items-center border-top">
                <div class="modal-ezeshop__cancel me-4">
                    <button @click="isModalAdd = false" class="white-btn py-2 px-4 fs-14">Huỷ</button>
                </div>
                <div class="modal-ezeshop__save">
                    <button form="form-add-cate" style="width: 150px; height: 37px;"
                        class="main-btn py-2 fs-14 d-flex align-items-center justify-content-center" type="submit">
                        <loading__loader_circle size="15px" color="#fff" border="2px" v-if="loading.save_add" />
                        <span v-else>Thêm Danh Mục</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div v-if="isModalEdit"
        class="modal-background container-fluid p-0 d-flex justify-content-center align-items-center">
        <div class="modal-edit-address modal-ezeshop bg-white shadow-sm px-4 pt-4 pb-2">
            <div class="modal-ezeshop__top d-flex justify-content-between align-items-center">
                <div class="modal-ezeshop__name fs-18 fw-500">Sửa Danh Mục</div>
                <div @click="closeModelEdit()" class="modal-ezeshop__cancel cursor-pointer"><i
                        class="fa-solid fa-xmark fs-18"></i></div>
            </div>
            <!-- Content -->
            <div class="modal-ezeshop__main w-100 p-1">
                <form id="form-edit-cate" @submit.prevent="editCategory" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="name" class="form-label fs-14">Tên Danh Mục<span class="text-color">*</span></label>
                        <input v-model="categoryEdit.name" type="text" class="form-control form-control__input">
                        <p class="text-danger" v-if="errors.name">{{ Array.isArray(errors.name) ? errors.name[0] :
                            errors.name }}</p>
                    </div>
                    <div class="mb-3">
                        <label for="category_parent" class="form-label fs-14">Danh Mục Cha</label>
                        <select v-model="categoryEdit.parent_id" id="category_parent"
                            class="form-control form-control__input form-select cursor-pointer">
                            <option value="">Không có</option>
                            <categoryOption :categories_tree="categories_full_tree" />
                        </select>
                        <p class="text-danger" v-if="errors.parent_id">{{ Array.isArray(errors.parent_id) ?
                            errors.parent_id[0] :
                            errors.parent_id }}</p>
                    </div>
                    <div class="mb-3">
                        <label for="category_parent" class="form-label fs-14">
                            Thuế
                            <span class="text-color">*</span>
                        </label>
                        <select v-model="categoryEdit.tax_id" id="category_parent"
                            class="form-control form-control__input form-select cursor-pointer">
                            <option v-for="tax in taxes" :value="tax.id">{{ tax.name }}</option>
                        </select>
                        <p class="text-danger" v-if="errors.tax_id">{{ Array.isArray(errors.tax_id) ?
                            errors.tax_id[0] :
                            errors.tax_id }}</p>
                    </div>
                    <div class="mb-3">
                        <label for="category_parent" class="form-label fs-14">
                            Phí<span class="text-color">*</span>
                        </label>
                        <select v-model="categoryEdit.fee_id" id="category_parent"
                            class="form-control form-control__input form-select cursor-pointer">
                            <option v-for="fee in fees" :value="fee.id">{{ fee.name }}</option>
                        </select>
                        <p class="text-danger" v-if="errors.fee_id">{{ Array.isArray(errors.fee_id) ?
                            errors.fee_id[0] :
                            errors.fee_id }}</p>
                    </div>
                    <div class="mb-3">
                        <label for="comment">Mô Tả</label>
                        <textarea v-model="categoryEdit.description" class="form-control form-control__input"
                            rows="2"></textarea>
                    </div>
                    <div class="mb-3 mt-3">
                        <label for="imageEdit" class="form-label fs-14">Hình Ảnh<span
                                class="text-color">*</span></label><br>
                        <label class="category__box--image" for="imageEdit">
                            <img :src="imageData.imagePreview || imageData.defaultImage" alt="upload icon">
                        </label>
                        <input type="file" hidden id="imageEdit" accept="image/*"
                            @change="(e) => uploadImage(e, categoryEdit)">
                    </div>
                </form>
            </div>
            <div class="modal-ezeshop__bottom d-flex justify-content-end w-100 align-items-center border-top">
                <div class="modal-ezeshop__cancel me-4"><button @click="closeModelEdit()"
                        class="white-btn py-2 px-4 fs-14">Huỷ</button></div>
                <div class="modal-ezeshop__save">
                    <button form="form-edit-cate" style="width: 150px; height: 37px;"
                        class="main-btn py-2 fs-14 d-flex align-items-center justify-content-center" type="submit">
                        <loading__loader_circle size="15px" color="#fff" border="2px" v-if="loading.save_edit" />
                        <span v-else>Cập Nhật</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Model Cate -->
    <div v-if="isDeleteModalVisible"
        class="modal-background container-fluid p-0 d-flex justify-content-center align-items-center">
        <div class="modal-edit-address modal-ezeshop bg-white shadow-sm px-4 pt-4 pb-2" style="max-width: 500px">
            <div class="modal-ezeshop__top d-flex justify-content-between align-items-center">
                <div class="modal-ezeshop__name fs-18 fw-500">Xác Nhận Xóa Danh Mục</div>
                <div @click="isDeleteModalVisible = false" class="modal-ezeshop__cancel cursor-pointer"><i
                        class="fa-solid fa-xmark fs-18"></i></div>
            </div>
            <!-- Content -->
            <div class="modal-ezeshop__main w-100">
                Bạn có chắc muốn xóa không?
            </div>
            <div class="modal-ezeshop__bottom d-flex justify-content-end w-100 align-items-center border-top">
                <div class="modal-ezeshop__cancel me-4"><button @click="isDeletisDeleteModalVisibleeModal = false"
                        class="white-btn py-2 px-4 fs-14">Huỷ</button></div>
                <div class="modal-ezeshop__save">
                    <button @click="handleDeleteCatogories()" style="width: 150px; height: 37px;"
                        class="main-btn py-2 px-4 fs-14 d-flex align-items-center justify-content-center" type="button">
                        <loading__loader_circle size="15px" color="#fff" border="2px" v-if="loading.delete" />
                        <span v-else>Xác Nhận</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Model Cate Confirm-->
</template>
<script setup>
import { onMounted, ref, computed } from 'vue'
import api from '@/configs/api'
import categoryList from '@/components/admins/categoryList.vue'
import categoryOption from '@/components/admins/categoryOption.vue'
import pagination from '@/components/pagination.vue'
import loading__dot from '@/components/loading/loading__dot.vue'
import loading__loader_circle from '@/components/loading/loading__loader-circle.vue'
import no_data from '@/components/no_data.vue'
import message from '@/utils/messageState'

/*
    GET
*/
const categories_full_tree = ref([])
const categories_tree = ref([])
const totalCate = ref(0)
const taxes = ref([])
const fees = ref([])
const getCategories = async () => {
    try {
        loading.value.fetch_list = true
        const res = await api.get(`/categories-tree`, {
            params: {
                page: currentPage.value,
                search: searchValue.value
            }
        })
        if (res.status == 200) {
            categories_tree.value = res.data.categories_tree
            categories_full_tree.value = res.data.categories_full_tree
            totalCate.value = res.data.total
            meta.value = {
                current_page: res.data.current_page,
                last_page: res.data.last_page,
                per_page: res.data.per_page
            }
            taxes.value = res.data.taxes
            fees.value = res.data.fees
            loading.value.fetch_list = false
        }
    } catch (error) {
        console.error(error)
    } finally {
        loading.value.fetch_list = false
    }
}

/*
    ADD
*/
const category = ref({
    name: '',
    image: '',
    description: '',
    parent_id: '',
    tax_id: 0,
    fee_id: 0
})
const isModalAdd = ref(false)
const addCategory = async () => {
    if (!validCate(category)) return
    const formData = new FormData()
    for (let key in category.value) formData.append(key, category.value[key])
    try {
        loading.value.save_add = true
        const res = await api.post(`/categories`, formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        })
        if (res.status == 201) {
            getCategories()
            Object.assign(category.value, { name: '', image: '', parent_id: '', description: '', tax_id: 0, fee_id: 0 })
            imageData.value.imagePreview = null
            //Thông báo
            message.emit('show-message', {
                title: 'Thành công',
                text: 'Thêm danh mục thành công.',
                type: 'success'
            })
            isModalAdd.value = false
            loading.value.save_add = false
        }
    } catch (error) {
        if (error.response && error.response.status === 422) {
            errors.value = error.response.data.errors
        } else {
            console.warn('Lỗi validation:', error)
        }
    } finally {
        loading.value.save_add = false
    }
}

/*
   EDIT
*/
const categoryEdit = ref({
    id: '',
    name: '',
    image: '',
    description: '',
    parent_id: '',
    tax_id: 0,
    fee_id: 0
})
const isModalEdit = ref(false)
const showModalEdit = (cate_id) => {
    errors.value = {}
    isModalEdit.value = true
    const category = getCategoryById(cate_id)
    Object.assign(categoryEdit.value, {
        id: category.id,
        name: category.name ?? '',
        description: category.description ?? '',
        parent_id: category.parent_id ?? '',
        tax_id: category.tax_id ?? 0,
        fee_id: category.fee_id ?? 0,
        children: category.children ?? []
    })
    imageData.value.imagePreview = category.image ? `/imgs/categories/${category.image}` : null
}
const closeModelEdit = () => {
    isModalEdit.value = false
    imageData.value.imagePreview = null
}
const getCategoryById = (id, tree = categories_tree.value) => {
    for (let cate of tree) {
        if (cate.id == id) {
            return cate
        }
        if (cate.children && cate.children.length > 0) {
            const found = getCategoryById(id, cate.children)
            if (found) return found
        }
    }
    return null
}
const isDescendantOfSelf = (parentId, childrenList) => {
    if (!childrenList || childrenList.length === 0) return false
    for (const child of childrenList) {
        if (child.id === parentId) return true
        if (isDescendantOfSelf(parentId, child.children)) return true
    }
    return false
}
const editCategory = async () => {
    if (!validCate(categoryEdit, categoryEdit.value.id)) return
    if (isDescendantOfSelf(categoryEdit.value.parent_id, categoryEdit.value.children)) {
        errors.value.parent_id = "Không thể chọn con của chính mình làm cha."
        return
    }
    const formData = new FormData()
    formData.append('_method', 'PUT')
    for (let key in categoryEdit.value) formData.append(key, categoryEdit.value[key])
    try {
        loading.value.save_edit = true
        const res = await api.post(`/categories/${categoryEdit.value.id}`, formData)
        if (res.status == 200) {
            getCategories()
            closeModelEdit()
            //Thông báo
            message.emit('show-message', {
                title: 'Thành công',
                text: 'Cập nhật danh mục thành công.',
                type: 'success'
            })
            loading.value.save_edit = false
        }
    } catch (error) {
        if (error.response && error.response.status === 422) {
            errors.value = error.response.data.errors
        } else {
            console.warn('Lỗi validation:', error)
        }
    } finally {
        loading.value.save_edit = false
    }
}

/*
    DELETE
*/
const isDeleteModalVisible = ref(false)
const selectedCategoryIds = ref([])
const openDeleteModal = (id = null) => {
    isDeleteModalVisible.value = true
    if (id) {
        selectedCategoryIds.value = []
        selectedCategoryIds.value = [id]
    }
    console.log(selectedCategoryIds.value);
    if (selectedCategoryIds.value.length === 0) {
        message.emit('show-message', {
            title: 'Thông báo',
            text: 'Vui lòng chọn danh mục muốn xóa',
            type: 'info'
        })
        isDeleteModalVisible.value = false
        return
    }
}

const handleDeleteCatogories = async () => {
    try {
        const ids = selectedCategoryIds.value

        loading.value.delete = true
        const resDelete = await api.delete('/categories', {
            data: {
                ids: ids
            }
        })
        if (resDelete.status == 200) {
            const mess = resDelete.data.not_deleted_names
                ? `Danh mục ${resDelete.data.not_deleted_names} không thể xóa vì có sản phẩm hoặc có danh mục con.`
                : 'Đã xóa thành công.'
            message.emit("show-message", {
                title: "Thông báo",
                text: mess,
                type: "info"
            })
            getCategories()
        }
    } catch (error) {
        error = error.response
        if (error && error.status === 422) {
            message.emit("show-message", {
                title: "Thông báo",
                text: error.data.errors.ids[0],
                type: "error"
            })
        } else if (error.status == 500) {
            message.emit("show-message", {
                title: "Thông báo",
                text: error.data.message,
                type: "error"
            })
        } else {
            console.warn('Lỗi validation:', error)
        }
    } finally {
        loading.value.delete = false
        isDeleteModalVisible.value = false
        selectedCategoryIds.value = []
    }
}

const getAllCategoryIds = (categories) => {
    let ids = []
    categories.forEach(c => {
        ids.push(c.id)
        if (c.children && c.children.length) {
            ids = ids.concat(getAllCategoryIds(c.children))
        }
    })
    return ids
}

const isAllSelected = computed(() => {
    const allIds = getAllCategoryIds(categories_tree.value)
    return selectedCategoryIds.value.length === allIds.length
})

const toggleSelection = (id) => {
    if (selectedCategoryIds.value.includes(id)) {
        selectedCategoryIds.value = selectedCategoryIds.value.filter(item => item !== id)
    } else {
        selectedCategoryIds.value.push(id)
    }
}

const toggleSelectionAll = (e) => {
    const checked = e.target.checked
    const allIds = getAllCategoryIds(categories_tree.value)
    if (checked) {
        selectedCategoryIds.value = allIds
    } else {
        selectedCategoryIds.value = []
    }
}

/*
    SUPPORT
*/
const loading = ref({
    save_add: false,
    fetch_list: false,
    save_edit: false,
    delete: false
})
const errors = ref({})
const imageData = ref({
    imagePreview: null,
    defaultImage: '/imgs/upload-icon-default.jpg'
})
const validCate = (cate, id = 0) => {
    errors.value = {}
    if (!cate.value.name.trim()) errors.value.name = "Tên danh mục không được để trống."
    if (cate.value.parent_id && id == cate.value.parent_id) errors.value.parent_id = "Danh mục cha không thể là chính nó."
    if (!cate.value.tax_id) errors.value.tax_id = "Vui lòng chọn thuế."
    if (!cate.value.fee_id) errors.value.fee_id = "Vui lòng chọn phí sàn."
    return Object.keys(errors.value).length === 0
}
const uploadImage = (e, categoryRef) => {
    try {
        const file = e.target.files[0]
        if (file) {
            categoryRef.image = file
            imageData.value.imagePreview = URL.createObjectURL(file)
        }
    } catch (err) {
        console.error('Lỗi trong uploadImage:', err)
    }
}

/*
    PAGINATION
*/
const meta = ref({})
const currentPage = ref(1)
const onChangePage = (page) => {
    currentPage.value = page
    getCategories()
}
/*
    SEARCH 
*/
const searchValue = ref('')
const onSearch = () => {
    currentPage.value = 1
    getCategories()
}
const clearSearch = () => {
    searchValue.value = ''
    getCategories()
}

/*
    LOAD
*/
onMounted(async () => {
    await getCategories()
})

</script>