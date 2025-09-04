<template>
    <main class="__main">
        <div class="__title mt-3 bg-white px-4 d-flex align-items-center border-bottom" style="height: 50px;"><span
                class="fs-14 fw-semibold text-grey">Giá Trị Thuộc Tính</span></div>
        <div class="__content bg-white px-4 pt-2 pb-5 align-items-center border-bottom shadow-sm">

            <div class="p-3 bg-white text-grey fs-14 fw-medium d-flex align-items-center justify-content-between">
                <p class="m-0">Danh Sách Giá Trị Của "{{ attributeName }}" (<span class="text-color">{{ totalValue
                        }}</span>)</p>
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
                                <input v-model="searchValue" type="text" placeholder="Tìm giá trị..."
                                    class="form-control" />

                                <!-- Nút xóa ô tìm kiếm -->
                                <button v-if="searchValue" type="button" @click="searchValue = ''; onSearch()"
                                    class="btn position-absolute top-50 end-0 translate-middle-y me-2 p-0 border-0 bg-transparent">
                                    <i class="ri-close-line"></i>
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
            <div class="row p-10">
                <div class="col-12 col-md-4 col-lg-3">
                    <form @submit.prevent="addAttributeValue">
                        <div class="mb-3">
                            <label for="name" class="form-label fs-14">
                                Giá Trị
                                <span class="text-color">*</span>
                            </label>
                            <input v-model="attributeValue.value" type="text" class="form-control" />
                        </div>
                        <button type="submit" class="main-btn py-2 px-4 fs-14">
                            <loading__loader_circle size="15px" color="#fff" border="2px" v-if="loading.save_add" />
                            <div v-else>Thêm Giá Trị</div>
                        </button>
                    </form>
                </div>
                <div class="col p-10">
                    <div v-if="loading.fetch_list" style="height: 60vh;"
                        class="d-flex align-items-center justify-content-center">
                        <loading__dot />
                    </div>
                    <div v-else class="table-responsive fade-in">
                        <div v-if="attributeValues.length === 0">
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
                                                            class="check-path"></path>
                                                    </svg>
                                                </div>
                                            </label>
                                        </div>
                                    </th>
                                    <th scope="col" class="fs-14 fw-semibold min-w-150">Tên Giá Trị</th>
                                    <th scope="col" class="fs-14 fw-semibold text-center min-w-100">Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="value in attributeValues" :key="value.id">
                                    <td scope="row" class="align-middle">
                                        <div class="checkbox me-2">
                                            <label class="checkbox-label">
                                                <input type="checkbox" name="check-item" :value="value.id"
                                                    v-model="selectedValues" />
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
                                    </td>

                                    <td class="align-middle">{{ value.value }}</td>

                                    <td class="align-middle text-center">
                                        <div class="more-wrapper">
                                            <button class="more-btn">
                                                <i class="ri-more-fill"></i>
                                            </button>
                                            <div class="more-popup">
                                                <button @click="showModalEdit(value.id)" class="bg-warning">Sửa</button>
                                                <button @click="showConfirmModal(value.id)"
                                                    class="text-white bg-danger">Xóa</button>
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
                                                <input type="checkbox" name="check-item" id="check_all"
                                                    v-model="checkAll" @change="toggleAllSelection" />
                                                <div class="checkbox-wrapper">
                                                    <div class="checkbox-bg"></div>
                                                    <svg fill="none" viewBox="0 0 24 24" class="checkbox-icon">
                                                        <path stroke-linejoin="round" stroke-linecap="round"
                                                            stroke-width="3" stroke="currentColor" d="M4 12L10 18L20 6"
                                                            class="check-path"></path>
                                                    </svg>
                                                </div>
                                            </label>
                                            <span class="fw-medium">All</span>
                                        </div>
                                    </th>

                                    <th scope="col"></th>
                                    <td class="align-middle text-center">
                                        <div class="more-wrapper">
                                            <button class="more-btn p-0">
                                                <i class="ri-more-fill"></i>
                                            </button>
                                            <div class="more-popup">
                                                <button @click="showConfirmModal()"
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
                        <!-- Pagination -->
                        <div class="d-flex align-items-center justify-content-end me-3">
                            <pagination :meta="meta" @changePage="onChangePage" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal edit -->
        <div v-if="isModelEdit"
            class="modal-background container-fluid p-0 d-flex justify-content-center align-items-center">
            <div class="modal-edit-address modal-ezeshop bg-white shadow-sm px-4 pt-4 pb-2">
                <div class="modal-ezeshop__top d-flex justify-content-between align-items-center">
                    <div class="modal-ezeshop__name fs-18 fw-500">Sửa Giá Trị</div>
                    <div @click="closeModelEdit()" class="modal-ezeshop__cancel cursor-pointer"><i
                            class="fa-solid fa-xmark fs-18"></i></div>
                </div>
                <!-- Content -->
                <div class="modal-ezeshop__main w-100 p-1">
                    <form id="form-edit-cate" @submit.prevent="editValue" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="name" class="form-label fs-14">Giá trị<span class="text-color">*</span></label>
                            <input v-model="valueEdit.value" type="text" class="form-control form-control__input">
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
                        </button></div>
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
                            @click="isDeletingMultiple ? deleteSelectedValues() : deleteValue()"
                            class="main-btn py-2 px-4 fs-14" type="submit">
                            <loading__loader_circle size="15px" color="#fff" border="2px" v-if="loading.delete" />
                            <span v-else>Xác Nhận</span>
                        </button></div>
                </div>
            </div>
        </div>
        <!-- End Confirm Delete Modal -->
    </main>
</template>

<script>
import { onMounted, ref, watch } from 'vue';
import api from '@/configs/api';
import pagination from '@/components/pagination.vue';
import message from '@/utils/messageState';
import loading__dot from '@/components/loading/loading__dot.vue';
import loading__loader_circle from '@/components/loading/loading__loader-circle.vue';
import no_data from '@/components/no_data.vue';
import { useRoute } from 'vue-router';
import useGoTo from '@/utils/useGoTo'
const { goTo } = useGoTo()


export default {
    components: {
        pagination,
        loading__dot,
        loading__loader_circle,
        no_data,
    },
    setup() {

        const loading = ref({
            save_add: false,
            fetch_list: false,
            save_edit: false,
            delete: false
        });

        // Khởi tạo router và lấy ID từ URL
        const route = useRoute();
        const attrId = route.params.id;

        // Khai báo các biến reactive
        // ============================
        // Danh sách giá trị thuộc tính
        const attributeValues = ref([]);
        // Giá trị thuộc tính mới
        const attributeValue = ref({ value: '' });
        // Tên thuộc tính
        const attributeName = ref('');
        // Tổng số giá trị
        const totalValue = ref(0);
        // Metadata phân trang
        const meta = ref({});
        // Trang hiện tại
        const currentPage = ref(1);
        // Giá trị tìm kiếm
        const searchValue = ref('');
        // Danh sách các giá trị được chọn
        const selectedValues = ref([]);
        // Trạng thái chọn tất cả
        const checkAll = ref(false);
        // Trạng thái hiển thị modal xóa
        const isDeleteModal = ref(false);
        // ID giá trị cần xóa
        const valueIdToDelete = ref(null);
        // Cờ xác định có đang xóa nhiều không
        const isDeletingMultiple = ref(false);
        // Trạng thái hiển thị modal chỉnh sửa
        const isModelEdit = ref(false);
        // Giá trị đang chỉnh sửa
        const valueEdit = ref({ id: '', value: '' });

        // Các hàm xử lý API
        // =================

        /**
         * Lấy danh sách giá trị thuộc tính từ API
         */
        const getAttributeValues = async () => {
            try {
                loading.value.fetch_list = true;
                const response = await api.get(`/attribute-values/${attrId}`, {
                    params: {
                        page: currentPage.value,
                        search: searchValue.value,
                    },
                });
                attributeValues.value = response.data.attributeValues;
                totalValue.value = response.data.total;
                meta.value = {
                    current_page: response.data.current_page,
                    last_page: response.data.last_page,
                    total: response.data.total,
                };
            } catch (error) {
                console.log(error);
            } finally {
                loading.value.fetch_list = false;
            }
        }

        /**
         * Lấy tên thuộc tính từ API
         */
        const getAttributeName = async () => {
            try {
                loading.value.fetch_list = true;
                const response = await api.get(`/attributes/${attrId}`);
                attributeName.value = response.data.attribute.name;
            } catch (error) {
                console.log(error);
            } finally {
                loading.value.fetch_list = false;
            }
        }

        /**
         * Thêm giá trị thuộc tính mới
         */
        const addAttributeValue = async () => {
            if (!validateAttribute()) return;
            try {
                loading.value.save_add = true;
                const res = await api.post(`/attribute-values/${attrId}`, attributeValue.value);
                message.emit('show-message', {
                    title: 'Thành công',
                    text: 'Thêm giá trị thành công',
                    type: 'success'
                });
                getAttributeValues();
                attributeValue.value.value = '';
            } catch (error) {
                console.error('API Error:', error.response?.data || error.message);
                message.emit('show-message', {
                    title: 'Thất bại',
                    text: 'Thêm giá trị thất bại',
                    type: 'error'
                });
            } finally {
                loading.value.save_add = false;
            }
        };

        /**
         * Xóa một giá trị thuộc tính
         */
        const deleteValue = async () => {
            try {
                loading.value.delete = true;
                await api.delete(`/attribute-values/${valueIdToDelete.value}`);
                message.emit('show-message', {
                    title: 'Thành công',
                    text: 'Xóa giá trị thành công',
                    type: 'success'
                });
                getAttributeValues();
            } catch (error) {
                message.emit('show-message', {
                    title: 'Thất bại',
                    text: 'Xóa giá trị thất bại',
                    type: 'error'
                });
            } finally {
                isDeleteModal.value = false;
                valueIdToDelete.value = null;
                isDeletingMultiple.value = false;
                selectedValues.value = [];
                loading.value.delete = false;
            }
        };

        /**
         * Xóa nhiều giá trị thuộc tính
         */
        const deleteSelectedValues = async () => {
            if (selectedValues.value.length === 0) {
                message.emit('show-message', {
                    title: 'Chú ý',
                    text: 'Chưa chọn giá trị nào để xóa',
                    type: 'warning'
                });
                isDeleteModal.value = false;
                checkAll.value = false;
                return;
            }
            try {
                loading.value.delete = true;
                isDeletingMultiple.value = true;
                await api.delete('/attribute-values/delete-multiple', {
                    data: { ids: selectedValues.value }
                });

                message.emit('show-message', {
                    title: 'Thành công',
                    text: 'Đã xóa các giá trị đã chọn',
                    type: 'success'
                });

                selectedValues.value = [];
                checkAll.value = false;
                getAttributeValues();
            } catch (error) {
                message.emit('show-message', {
                    title: 'Thất bại',
                    text: 'Xóa giá trị thất bại',
                    type: 'error'
                });
            } finally {
                isDeleteModal.value = false;
                isDeletingMultiple.value = false;
                valueIdToDelete.value = null;
                selectedValues.value = [];
                loading.value.delete = false;
            }
        };

        /**
         * Cập nhật giá trị thuộc tính
         */
        const editValue = async () => {
            if (!validateEditValue()) return;

            try {
                loading.value.save_edit = true;
                await api.put(`/attribute-values/${valueEdit.value.id}`, valueEdit.value);
                message.emit('show-message', {
                    title: 'Thành công',
                    text: 'Cập nhật giá trị thành công',
                    type: 'success'
                });
                getAttributeValues();
                closeModelEdit();
            } catch (error) {
                message.emit('show-message', {
                    title: 'Thất bại',
                    text: 'Cập nhật giá trị thất bại',
                    type: 'error'
                });
            } finally {
                loading.value.save_edit = false;
            }
        };

        // Các hàm validate
        // ================

        /**
         * Kiểm tra tính hợp lệ của giá trị thuộc tính mới
         */
        const validateAttribute = () => {
            if (attributeValue.value.value === '') {
                message.emit('show-message', {
                    title: 'Thất bại',
                    text: 'Giá trị không được để trống',
                    type: 'error'
                });
                return false;
            }

            if (attributeValues.value.some(attr => attr.value === attributeValue.value.value)) {
                message.emit('show-message', {
                    title: 'Thất bại',
                    text: 'Giá trị đã tồn tại',
                    type: 'error'
                });
                return false;
            }

            return true;
        };

        /**
         * Kiểm tra tính hợp lệ của giá trị đang chỉnh sửa
         */
        const validateEditValue = () => {
            if (valueEdit.value.value === '') {
                message.emit('show-message', {
                    title: 'Thất bại',
                    text: 'Giá trị không được để trống',
                    type: 'error'
                });
                return false;
            }

            if (attributeValues.value.some(value => value.value === valueEdit.value.value && value.id !== valueEdit.value.id)) {
                message.emit('show-message', {
                    title: 'Thất bại',
                    text: 'Giá trị đã tồn tại',
                    type: 'error'
                });
                return false;
            }

            return true;
        };

        // Các hàm xử lý UI
        // ================

        /**
         * Hiển thị modal xác nhận xóa
         */
        const showConfirmModal = (id = null) => {
            isDeleteModal.value = true;
            valueIdToDelete.value = id;
            isDeletingMultiple.value = !id;
        };

        /**
         * Hiển thị modal chỉnh sửa
         */
        const showModalEdit = (valueId) => {
            const value = attributeValues.value.find(value => value.id === valueId);
            if (value) {
                valueEdit.value = { ...value };
                isModelEdit.value = true;
            } else {
                message.emit('show-message', {
                    title: 'Thất bại',
                    text: 'Không tìm thấy giá trị',
                    type: 'error'
                });
            }
        };

        /**
         * Đóng modal chỉnh sửa
         */
        const closeModelEdit = () => {
            isModelEdit.value = false;
            valueEdit.value = {};
        };

        // Các hàm xử lý phân trang và tìm kiếm
        // ===================================

        /**
         * Thay đổi trang hiện tại
         */
        const onChangePage = (page) => {
            currentPage.value = page;
            getAttributeValues();
        };

        /**
         * Tìm kiếm giá trị thuộc tính
         */
        const onSearch = () => {
            currentPage.value = 1;
            getAttributeValues();
        };

        // Các hàm xử lý chọn giá trị
        // ==========================

        /**
         * Chọn/bỏ chọn tất cả giá trị
         */
        const toggleAllSelection = () => {
            checkAll.value
                ? selectedValues.value = attributeValues.value.map(attr => attr.id)
                : selectedValues.value = [];
        };

        /**
         * Chọn/bỏ chọn một giá trị
         */


        // Watchers
        // ========

        // Theo dõi thay đổi danh sách chọn để cập nhật trạng thái "chọn tất cả"
        watch(selectedValues, (newSelected) => {
            if (newSelected.length === attributeValues.value.length && attributeValues.value.length > 0) {
                checkAll.value = true;
            } else {
                checkAll.value = false;
            }
        });

        // Lifecycle hooks
        // ==============

        // Khi component được mount, tải dữ liệu ban đầu
        onMounted(async () => {
            const shop_id = localStorage.getItem("shop_id");
            if (shop_id == 0) {
                message.emit("show-message", { title: "Thông báo", text: 'Vui lòng đăng ký shop ký để sử dụng được chức năng này.', type: "error" });
                goTo("/kenh-nguoi-ban/dang-ky");
                return
            }
            await getAttributeValues();
            await getAttributeName();
        });

        // Trả về các biến và hàm để template có thể sử dụng
        return {
            attributeValues,
            attributeValue,
            addAttributeValue,
            totalValue,
            attributeName,
            meta,
            currentPage,
            onChangePage,
            searchValue,
            onSearch,
            checkAll,
            toggleAllSelection,
            selectedValues,
            isDeleteModal,
            showConfirmModal,
            deleteValue,
            deleteSelectedValues,
            isDeletingMultiple,
            isModelEdit,
            valueEdit,
            showModalEdit,
            closeModelEdit,
            editValue,
            loading,
        };
    }
}
</script>