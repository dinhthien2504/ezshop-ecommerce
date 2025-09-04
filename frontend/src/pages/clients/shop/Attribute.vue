<template>
    <div class="__main">
        <div class="__title mt-3 bg-white px-4 d-flex align-items-center border-bottom" style="height: 50px;"><span
                class="fs-14 fw-semibold text-grey">Thuộc Tính</span></div>
        <div class="__content bg-white px-4 pt-2 pb-5 align-items-center border-bottom shadow-sm">
            <div class="p-3 bg-white text-grey fs-14 fw-medium d-flex align-items-center justify-content-between">
                <p class="m-0">Danh Sách Thuộc Tính Sàn (<span class="text-color">{{ totalAttribute }}</span>)</p>
                <div class="d-flex align-items-center gap-2">
                    <div class="admin__search">
                        <button type="button" @click="onSearch" class="search__button">
                            <i class="ri-search-2-line"></i>
                        </button>
                        <form @submit.prevent="onSearch">
                            <div class="d-flex align-items-center gap-1 position-relative">
                                <!-- Nút tìm kiếm -->
                                <button type="submit" style="height: 38px" class="main-btn py-1 px-3">
                                    <i class="ri-search-2-line"></i>
                                </button>

                                <!-- Ô nhập tìm kiếm -->
                                <input v-model="searchValue" type="text" placeholder="Tìm thuộc tính..."
                                    class="form-control" />

                                <!-- Nút xóa ô tìm kiếm -->
                                <button v-if="searchValue" type="button" @click="searchValue = ''; onSearch()"
                                    class="btn position-absolute top-50 end-0 translate-middle-y me-2 p-0 border-0 bg-transparent">
                                    <i class="ri-close-line"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                    <button type="button" @click="showAddModal()" class="main-btn px-3" style="height: 38px;">
                        <span class="d-none d-lg-block">Thêm Thuộc Tính</span>
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
                    <div v-if="attributes.length == 0">
                        <no_data />
                    </div>
                    <table v-else class="table border">
                        <thead class="table-active" style="height: 50px">
                            <tr class="lh-lg align-middle">
                                <th scope="col" class="min-w-50">
                                    <div class="checkbox">
                                        <label class="checkbox-label">
                                            <input type="checkbox" name="check-item" v-model="checkAll"
                                                @change="toggleAllSelection" />
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
                                </th>
                                <th scope="col" class="fs-14 fw-semibold min-w-150">Tên Thuộc Tính</th>
                                <th scope="col" class="fs-14 fw-semibold min-w-300">Các Giá Trị</th>
                                <th scope="col" class="fs-14 fw-semibold text-center min-w-100">Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="attr in attributes" :key="attr.id">
                                <td scope="row" class="align-middle py-4">
                                    <div class="checkbox me-2">
                                        <label class="checkbox-label">
                                            <input type="checkbox" name="check-item" :value="attr.id"
                                                v-model="selectedAttributes" />
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

                                <td class="align-middle py-4">{{ attr.name }}</td>
                                <td class="align-middle">{{attr.attribute_values && attr.attribute_values.length > 0
                                    ? attr.attribute_values.map(value => value.value).join(', ')
                                    : 'Chưa có giá trị nào'}}</td>

                                <td class="align-middle py-4 text-center">
                                    <div class="more-wrapper">
                                        <button class="more-btn">
                                            <i class="ri-more-fill"></i>
                                        </button>
                                        <div class="more-popup two-row">
                                            <button @click="showModalEdit(attr.id)" class="bg-warning">Sửa</button>
                                            <button @click="showConfirmModal(attr.id)"
                                                class="text-white bg-danger">Xóa</button>
                                            <button @click="goTo(`/kenh-nguoi-ban/thuoc-tinh/gia-tri/${attr.id}`)"
                                                class="text-white bg-success">Thêm Giá Trị</button>
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
                                                @change="toggleAllSelection" />
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
                                        <span class="fw-medium">All</span>
                                    </div>
                                </th>

                                <th scope="col"></th>
                                <th scope="col"></th>
                                <td class="align-middle text-center">
                                    <div class="more-wrapper">
                                        <button class="more-btn p-0">
                                            <i class="ri-more-fill"></i>
                                        </button>
                                        <div class="more-popup">
                                            <button @click="showConfirmModal()" class="text-white bg-danger px-2 py-0">
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
                    <div class="d-flex align-items-center justify-content-end me-3">
                        <pagination :meta="meta" @changePage="onChangePage" />
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal add -->
        <div v-if="isModalAdd"
            class="modal-background container-fluid p-0 d-flex justify-content-center align-items-center">
            <div class="modal-edit-address modal-ezeshop bg-white shadow-sm px-4 pt-4 pb-2">
                <div class="modal-ezeshop__top d-flex justify-content-between align-items-center">
                    <div class="modal-ezeshop__name fs-18 fw-500">Thêm Thuộc Tính</div>
                    <div @click="isModalAdd = false" class="modal-ezeshop__cancel cursor-pointer"><i
                            class="fa-solid fa-xmark fs-18"></i></div>
                </div>
                <!-- Content -->
                <div class="modal-ezeshop__main w-100 p-1">
                    <form id="form-edit-cate" @submit.prevent="addAttribute" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="name" class="form-label fs-14">Tên Thuộc Tính<span
                                    class="text-color">*</span></label>
                            <input v-model="attribute.name" type="text" class="form-control form-control__input">
                        </div>
                    </form>
                </div>
                <div class="modal-ezeshop__bottom d-flex justify-content-end w-100 align-items-center border-top">
                    <div class="modal-ezeshop__cancel me-4"><button @click="isModalAdd = false"
                            class="white-btn py-2 px-4 fs-14">Huỷ</button></div>
                    <div class="modal-ezeshop__save"><button form="form-edit-cate" class="main-btn py-2 px-4 fs-14"
                            type="submit">
                            <loading__loader_circle size="15px" color="#fff" border="2px" v-if="loading.save_add" />
                            <div v-else>Thêm</div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Modal add -->

        <!-- Modal edit -->
        <div v-if="isModelEdit"
            class="modal-background container-fluid p-0 d-flex justify-content-center align-items-center">
            <div class="modal-edit-address modal-ezeshop bg-white shadow-sm px-4 pt-4 pb-2">
                <div class="modal-ezeshop__top d-flex justify-content-between align-items-center">
                    <div class="modal-ezeshop__name fs-18 fw-500">Sửa Thuộc Tính</div>
                    <div @click="closeModelEdit()" class="modal-ezeshop__cancel cursor-pointer"><i
                            class="fa-solid fa-xmark fs-18"></i></div>
                </div>
                <!-- Content -->
                <div class="modal-ezeshop__main w-100 p-1">
                    <form id="form-edit-cate" @submit.prevent="editAttribute" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="name" class="form-label fs-14">Tên Thuộc Tính<span
                                    class="text-color">*</span></label>
                            <input v-model="attrEdit.name" type="text" class="form-control form-control__input">
                        </div>
                    </form>
                </div>
                <div class="modal-ezeshop__bottom d-flex justify-content-end w-100 align-items-center border-top">
                    <div class="modal-ezeshop__cancel me-4"><button @click="closeModelEdit()"
                            class="white-btn py-2 px-4 fs-14">Huỷ</button></div>
                    <div class="modal-ezeshop__save"><button form="form-edit-cate" class="main-btn py-2 px-4 fs-14"
                            type="submit">
                            <loading__loader_circle size="15px" color="#fff" border="2px" v-if="loading.save_edit" />
                            <span v-else>Cập Nhật</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Modal edit -->

        <!-- Confirm Delete Modal -->
        <div v-if="isDeleteModal"
            class="modal-background container-fluid p-0 d-flex justify-content-center align-items-center">
            <div class="modal-edit-address modal-ezeshop bg-white shadow-sm px-4 pt-4 pb-2" style="max-width: 500px;">
                <div class="modal-ezeshop__top d-flex justify-content-between align-items-center">
                    <div class="modal-ezeshop__name fs-18 fw-500">Xác Nhận Xóa</div>
                    <div @click="isDeleteModal = false" class="modal-ezeshop__cancel cursor-pointer"><i
                            class="fa-solid fa-xmark fs-18"></i></div>
                </div>
                <!-- Content -->
                <div class="modal-ezeshop__main w-100">
                    Bạn có chắc muốn xóa không?
                </div>
                <div class="mt-3 modal-ezeshop__bottom d-flex justify-content-end w-100 align-items-center border-top">
                    <div class="modal-ezeshop__cancel me-4"><button @click="isDeleteModal = false"
                            class="white-btn py-2 px-4 fs-14">Huỷ</button></div>
                    <div class="modal-ezeshop__save"><button
                            @click="isDeletingMultiple ? deleteSelectedAttributes() : deleteAttribute()"
                            class="main-btn py-2 px-4 fs-14" type="submit">
                            <loading__loader_circle size="15px" color="#fff" border="2px" v-if="loading.delete" />
                            <span v-else>Xác Nhận</span>
                        </button></div>
                </div>
            </div>
        </div>
        <!-- End Confirm Delete Modal -->
    </div>

</template>

<script>
import { onMounted, ref, watch } from 'vue';
import api from '@/configs/api';
import pagination from '@/components/pagination.vue';
import message from '@/utils/messageState';
import useGoTo from '@/utils/useGoTo';
import loading__dot from '@/components/loading/loading__dot.vue';
import loading__loader_circle from '@/components/loading/loading__loader-circle.vue';
import no_data from '@/components/no_data.vue';

export default {
    components: {
        pagination,
        no_data,
        loading__dot,
        loading__loader_circle,
    },
    setup() {

        // ===================== REF =====================
        const attributes = ref([]);                   // Danh sách các thuộc tính
        const attribute = ref({ name: '' });          // Thuộc tính mới cần thêm
        const totalAttribute = ref(0);                // Tổng số thuộc tính
        const meta = ref({});                         // Dữ liệu phân trang
        const currentPage = ref(1);                   // Trang hiện tại
        const searchValue = ref('');                  // Giá trị tìm kiếm
        const { goTo } = useGoTo();                   // Hàm điều hướng đến trang thêm giá trị thuộc tính
        const isModalAdd = ref(false);                // Trạng thái hiển thị modal thêm thuộc tính
        const loading = ref({
            save_add: false,
            fetch_list: false,
            save_edit: false,
            delete: false
        });

        const isDeleteModal = ref(false);             // Trạng thái hiển thị modal xác nhận xóa
        const attrIdToDelete = ref(null);             // ID thuộc tính cần xóa
        const isDeletingMultiple = ref(false);        // Đang xóa nhiều thuộc tính?

        const selectedAttributes = ref([]);           // Danh sách ID thuộc tính được chọn
        const checkAll = ref(false);                  // Trạng thái checkbox "Chọn tất cả"

        const isModelEdit = ref(false);               // Trạng thái hiển thị modal chỉnh sửa
        const attrEdit = ref({ id: '', name: '' });   // Thuộc tính đang chỉnh sửa

        // ===================== FETCH DATA =====================
        const getAttributes = async () => {
            try {
                loading.value.fetch_list = true; // Bắt đầu trạng thái loading
                const response = await api.get(`/attributes-with-values-shop`, {
                    params: {
                        shop_id: shop_id.value,
                        page: currentPage.value,
                        search: searchValue.value,
                    }
                });
                // Cập nhật dữ liệu
                attributes.value = response.data.attributes;
                totalAttribute.value = response.data.total;
                meta.value = {
                    current_page: response.data.current_page,
                    last_page: response.data.last_page,
                    total: response.data.total,
                };
            } catch (error) {
                console.error('Lỗi khi tải dữ liệu:', error);
            } finally {
                loading.value.fetch_list = false; // Kết thúc trạng thái loading
            }
        };

        // ===================== VALIDATION =====================
        const validateAttribute = () => {
            if (attribute.value.name === '') {
                message.emit('show-message', {
                    title: 'Thất bại',
                    text: 'Tên thuộc tính không được để trống',
                    type: 'error'
                });
                return false;
            }

            if (attributes.value.some(attr => attr.name === attribute.value.name)) {
                message.emit('show-message', {
                    title: 'Thất bại',
                    text: 'Tên thuộc tính đã tồn tại',
                    type: 'error'
                });
                return false;
            }

            return true;
        };

        const validateEditAttribute = () => {
            if (attrEdit.value.name === '') {
                message.emit('show-message', {
                    title: 'Thất bại',
                    text: 'Tên thuộc tính không được để trống',
                    type: 'error'
                });
                return false;
            }

            if (attributes.value.some(attr => attr.name === attrEdit.value.name && attr.id !== attrEdit.value.id)) {
                message.emit('show-message', {
                    title: 'Thất bại',
                    text: 'Tên thuộc tính đã tồn tại',
                    type: 'error'
                });
                return false;
            }

            return true;
        };

        // ===================== THÊM THUỘC TÍNH =====================
        const showAddModal = () => {
            attribute.value = {};
            isModalAdd.value = true;
        };

        const shop_id = ref(null);
        const addAttribute = async () => {
            if (!validateAttribute()) return;

            try {
                loading.value.save_add = true; // Bắt đầu trạng thái loading
                await api.post('/attributes/shop', { name: attribute.value.name, shop_id: shop_id.value });
                message.emit('show-message', {
                    title: 'Thành công',
                    text: 'Thêm thuộc tính thành công',
                    type: 'success'
                });
                getAttributes();
                attribute.value.name = '';
                isModalAdd.value = false; // Đóng modal sau khi thêm thành công
            } catch (error) {
                message.emit('show-message', {
                    title: 'Thất bại',
                    text: 'Thêm thuộc tính thất bại',
                    type: 'error'
                });
                console.error('Lỗi khi thêm thuộc tính:', error);
                console.log(error.response.data);
                console.log(error.response.status);
                console.log(error.response.headers);
            } finally {
                loading.value.save_add = false; // Kết thúc trạng thái loading
            }
        };

        // ===================== XÓA THUỘC TÍNH =====================
        const showConfirmModal = (id = null) => {
            isDeleteModal.value = true;
            attrIdToDelete.value = id;
            isDeletingMultiple.value = !id;
        };

        const deleteAttribute = async () => {
            try {
                loading.value.delete = true; // Bắt đầu trạng thái loading
                await api.delete(`/attributes/${attrIdToDelete.value}`);
                message.emit('show-message', {
                    title: 'Thành công',
                    text: 'Xóa thuộc tính thành công',
                    type: 'success'
                });
                getAttributes();
            } catch (error) {
                message.emit('show-message', {
                    title: 'Thất bại',
                    text: 'Xóa thuộc tính thất bại',
                    type: 'error'
                });
            } finally {
                isDeleteModal.value = false;
                attrIdToDelete.value = null;
                isDeletingMultiple.value = false;
                loading.value.delete = false; // Kết thúc trạng thái loading
            }
        };

        const deleteSelectedAttributes = async () => {
            if (selectedAttributes.value.length === 0) {
                message.emit('show-message', {
                    title: 'Chú ý',
                    text: 'Chưa chọn thuộc tính nào để xóa',
                    type: 'warning'
                });
                isDeleteModal.value = false;
                checkAll.value = false;
                return;
            }
            try {
                loading.value.delete = true; // Bắt đầu trạng thái loading
                isDeletingMultiple.value = true; // Đang xóa nhiều thuộc tính
                await api.delete('/attributes/delete-multiple', {
                    data: { ids: selectedAttributes.value }
                });

                message.emit('show-message', {
                    title: 'Thành công',
                    text: 'Đã xóa các thuộc tính đã chọn',
                    type: 'success'
                });

                selectedAttributes.value = [];
                checkAll.value = false;
                getAttributes();
            } catch (error) {
                message.emit('show-message', {
                    title: 'Thất bại',
                    text: 'Xóa thuộc tính thất bại',
                    type: 'error'
                });
            } finally {
                isDeleteModal.value = false;
                attrIdToDelete.value = null;
                isDeletingMultiple.value = false;
                loading.value.delete = false; // Kết thúc trạng thái loading
            }
        };

        // ===================== CHỈNH SỬA THUỘC TÍNH =====================
        const showModalEdit = (attrId) => {
            const attr = attributes.value.find(attr => attr.id === attrId);
            if (attr) {
                attrEdit.value = { ...attr };
                isModelEdit.value = true;
            } else {
                message.emit('show-message', {
                    title: 'Thất bại',
                    text: 'Không tìm thấy thuộc tính',
                    type: 'error'
                });
            }
        };

        const closeModelEdit = () => {
            isModelEdit.value = false;
            attrEdit.value = {};
        };

        const editAttribute = async () => {
            if (!validateEditAttribute()) return;

            try {
                loading.value.save_edit = true; // Bắt đầu trạng thái loading
                await api.put(`/attributes/${attrEdit.value.id}`, attrEdit.value);
                message.emit('show-message', {
                    title: 'Thành công',
                    text: 'Cập nhật thuộc tính thành công',
                    type: 'success'
                });
                getAttributes();
                closeModelEdit();
            } catch (error) {
                message.emit('show-message', {
                    title: 'Thất bại',
                    text: 'Cập nhật thuộc tính thất bại',
                    type: 'error'
                });
            } finally {
                loading.value.save_edit = false; // Kết thúc trạng thái loading
            }
        };

        // ===================== PHÂN TRANG VÀ TÌM KIẾM =====================
        const onChangePage = (page) => {
            currentPage.value = page;
        };

        const onSearch = () => {
            currentPage.value = 1;
            getAttributes();
        };

        // ===================== CHỌN/XÓA NHIỀU =====================
        const toggleAllSelection = () => {
            checkAll.value
                ? selectedAttributes.value = attributes.value.map(attr => attr.id)
                : selectedAttributes.value = [];
        };


        // ===================== WATCH =====================
        watch(selectedAttributes, (newSelected) => {
            if (newSelected.length === attributes.value.length && attributes.value.length > 0) {
                checkAll.value = true;
            } else {
                checkAll.value = false;
            }
        });


        watch(currentPage, () => {
            getAttributes();
        });



        // ===================== LIFE CYCLE =====================
        const get_shop = async () => {
            loading.value.fetch_list = true
            const res = await api.get('/get-shop');
            shop_id.value = res.data.shop.id;

            await getAttributes();
        };

        onMounted(async () => {
            const shop_id = localStorage.getItem("shop_id");
            if (shop_id == 0) {
                message.emit("show-message", { title: "Thông báo", text: 'Vui lòng đăng ký shop ký để sử dụng được chức năng này.', type: "error" });
                goTo("/kenh-nguoi-ban/dang-ky");
                return
            }
            await get_shop();
        });

        // ===================== RETURN =====================
        return {
            attributes,
            totalAttribute,
            attribute,
            addAttribute,

            // Tìm kiếm & phân trang
            meta,
            currentPage,
            onChangePage,
            searchValue,
            onSearch,

            // Xóa 1 / nhiều
            deleteAttribute,
            showConfirmModal,
            isDeleteModal,
            isDeletingMultiple,
            attrIdToDelete,
            deleteSelectedAttributes,
            selectedAttributes,
            checkAll,
            toggleAllSelection,
            // Chỉnh sửa
            isModelEdit,
            attrEdit,
            showModalEdit,
            closeModelEdit,
            editAttribute,

            message,
            goTo,
            showAddModal,
            isModalAdd,
            loading,
        };
    }
}
</script>
