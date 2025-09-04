<template>
    <main class="admin__content p-2">
        <!-- The Content -->
        <div class="my-1">
            <h3 class="fs-18">Quản Lý Phí Sàn</h3>
        </div>
        <div class="row g-2">
            <div class="col-md-4 col-lg-3">
                <form @submit.prevent="addFee">
                    <div class="mb-3">
                        <label class="form-label fs-14">Tên Phí Sàn <span class="text-color">*</span></label>
                        <input v-model="fee.name" type="text" class="form-control form-control__input">
                        <p class="text-danger" v-if="errors.name">{{ Array.isArray(errors.name) ? errors.name[0] :
                            errors.name }}
                        </p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fs-14">Phần Trăm Phí<span class="text-color">*</span></label>
                        <input type="number" v-model="fee.percentage" :min="0" :value="0"
                            class="form-control form-control__input">
                        <p class="text-danger" v-if="errors.percentage">{{ Array.isArray(errors.percentage) ?
                            errors.percentage[0] :
                            errors.percentage }}
                        </p>
                    </div>
                    <button type="submit" class="main-btn p-1 px-2">Thêm Phí Sàn</button>
                </form>
            </div>
            <div class="col-md-8 col-lg-9">
                <div class="bg-white p-2">
                    <!-- Top Category Show -->
                    <div class="d-flex align-items-center justify-content-between my-2">
                        <p class="fs-14 d-flex">
                            <span class="text-color me-1">{{ totalFee }} Phí Sàn</span>
                        </p>
                        <form @submit.prevent="onSearch" class="d-flex align-items-center gap-1">
                            <!-- ô input tìm kiếm -->
                            <input type="text" v-model="searchValue" placeholder="Tìm phí sàn..."
                                class="form-control form-control__input" />
                            <button type="submit" style="height: 37px;" class="main-btn py-1 px-3"><i
                                    class="ri-search-2-line"></i></button>
                        </form>
                    </div>
                    <div v-if="fees.length > 0" class="border">
                        <div class="bg-light py-3 px-2 sticky-top">
                            <div class="row g-0">
                                <div class="col-1">
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
                                </div>
                                <!-- Desktop Header -->
                                <div class="col-5 d-none d-md-block">
                                    <p class="m-0">Tên Phí Sàn</p>
                                </div>
                                <div class="col-3 d-none d-md-block">
                                    <p class="m-0">Phí</p>
                                </div>
                                <div class="col-3 d-none d-md-block text-center">
                                    <p class="m-0">Thao tác</p>
                                </div>

                                <!-- Mobile Header -->
                                <div class="col-10 d-block d-md-none">
                                    <p class="m-0">Tên Phí Sàn & Phí</p>
                                </div>

                            </div>
                        </div>
                        <!-- Item cate-->
                        <div v-for="fee in fees" :key="fee.id">
                            <div class="row align-items-center my-4 g-0 p-2">
                                <!-- Checkbox Column -->
                                <div class="col-1 ">
                                    <div class="checkbox me-2">
                                        <label class="checkbox-label">
                                            <input type="checkbox" @click="toggleSelection(fee.id)"
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
                                </div>
                                <!-- Checkbox vẫn giữ nguyên col-1 -->

                                <!-- Desktop -->
                                <div class="col-5 d-none d-md-block">
                                    <p class="m-0 fs-14 fw-bold">{{ fee.name }}</p>
                                </div>
                                <div class="col-3 d-none d-md-block">
                                    <p class="m-0 fs-14">{{ fee.percentage }}</p>
                                </div>
                                <div class="col-3 d-none d-md-block">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button @click="showModalEdit(fee.id)" type="button"
                                            class="btn btn-outline-warning btn-sm" data-bs-toggle="tooltip" title="Sửa">
                                            <i class="ri-file-edit-line"></i>
                                        </button>
                                        <button @click="confirmDelete(fee.id)" type="button"
                                            class="btn btn-outline-danger btn-sm" data-bs-toggle="tooltip" title="Xóa">
                                            <i class="ri-delete-bin-7-line"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Mobile -->
                                <div class="col-10 d-block d-md-none">
                                    <p class="m-0 fs-14 fw-bold">{{ fee.name }}</p>
                                    <p class="m-0 fs-14">Phí: {{ fee.percentage }}</p>
                                    <div class="d-flex mt-2 gap-2">
                                        <button @click="showModalEdit(fee.id)" type="button"
                                            class="btn btn-outline-warning btn-sm" data-bs-toggle="tooltip" title="Sửa">
                                            <i class="ri-file-edit-line"></i>
                                        </button>
                                        <button @click="confirmDelete(fee.id)" type="button"
                                            class="btn btn-outline-danger btn-sm" data-bs-toggle="tooltip" title="Xóa">
                                            <i class="ri-delete-bin-7-line"></i>
                                        </button>
                                    </div>
                                </div>

                            </div>
                            <hr>
                        </div>
                        <!-- Pagination -->
                        <div class="d-flex align-items-center justify-content-end me-3">
                            <pagination :meta="meta" @changePage="onChangePage" />
                        </div>
                        <!-- Category Bottom -->
                        <div class="bg-light px-2 py-3 mt-3 sticky-bottom">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="text-center d-flex align-items-center gap-2">
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
                                    <label class="fs-17 cursor-pointer" for="check_all">Tất cả</label>
                                </div>
                                <button @click="confirmDeleteSeleted" type="button" style="width: 50px;"
                                    class="btn btn-outline-danger btn-sm me-5"><i
                                        class="ri-delete-bin-7-line"></i></button>
                            </div>
                        </div>
                    </div>
                    <!-- <div v-else>
                        <div class="alert alert-warning text-center">
                            Không có danh mục nào.
                        </div>
                    </div> -->
                </div>
            </div>
        </div>
        <div v-if="isModelEdit"
            class="modal-background container-fluid p-0 d-flex justify-content-center align-items-center">
            <div class="modal-edit-address modal-ezeshop bg-white shadow-sm px-4 pt-4 pb-2">
                <div class="modal-ezeshop__top d-flex justify-content-between align-items-center">
                    <div class="modal-ezeshop__name fs-18 fw-500">Sửa Phí Sàn</div>
                    <div @click="isModelEdit = false" class="modal-ezeshop__cancel cursor-pointer"><i
                            class="fa-solid fa-xmark fs-18"></i></div>
                </div>
                <!-- Content -->
                <div class="modal-ezeshop__main w-100 p-1">
                    <form id="form-edit-fee" @submit.prevent="editFee">
                        <div class="mb-3">
                            <label class="form-label fs-14">Tên Phí<span class="text-color">*</span></label>
                            <input v-model="feeEdit.name" type="text" class="form-control form-control__input">
                            <p class="text-danger" v-if="errors.name">{{ Array.isArray(errors.name) ? errors.name[0] :
                                errors.name }}
                            </p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fs-14">Phần Trăm Phí<span class="text-color">*</span></label>
                            <input type="number" v-model="feeEdit.percentage" :min="0" :value="0"
                                class="form-control form-control__input">
                            <p class="text-danger" v-if="errors.percentage">{{ Array.isArray(errors.percentage) ?
                                errors.percentage[0] :
                                errors.percentage }}
                            </p>
                        </div>
                    </form>
                </div>
                <div class="modal-ezeshop__bottom d-flex justify-content-end w-100 align-items-center border-top">
                    <div class="modal-ezeshop__cancel me-4"><button @click="isModelEdit = false"
                            class="white-btn py-2 px-4 fs-14">Huỷ</button></div>
                    <div class="modal-ezeshop__save"><button form="form-edit-fee" class="main-btn py-2 px-4 fs-14"
                            type="submit">Cập
                            Nhật</button></div>
                </div>
            </div>
        </div>
        <!-- End Model Edit FeeFee -->

        <div v-if="isDeleteModal"
            class="modal-background container-fluid p-0 d-flex justify-content-center align-items-center">
            <div class="modal-edit-address modal-ezeshop bg-white shadow-sm px-4 pt-4 pb-2" style="max-width: 500px;">
                <div class="modal-ezeshop__top d-flex justify-content-between align-items-center">
                    <div class="modal-ezeshop__name fs-18 fw-500">Xác Nhận Xóa Phí Sàn</div>
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
                            @click="feeIdToDelete ? deleteFee() : deleteSelected()">
                            Xác Nhận
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Model Cate Confirm-->
    </main>
</template>
<script>
import { onMounted, ref, computed } from 'vue';
import api from '@/configs/api';
import pagination from '@/components/pagination.vue';
import message from '@/utils/messageState';
export default {
    components: {
        pagination
    },
    setup() {
        /*
            GET
        */
        const fees = ref([]);
        const totalFee = ref(0);
        const getFees = async () => {
            try {
                is_loading.value = true;
                const res = await api.get(`/fees`, {
                    params: {
                        page: currentPage.value,
                        search: searchValue.value
                    }
                });
                if (res.status == 200) {
                    fees.value = res.data.fees;
                    totalFee.value = res.data.total;
                    meta.value = res.data.meta;
                    is_loading.value = false;
                    // console.log(res);
                }
            } catch (error) {
                console.error(error);
            } finally {
                is_loading.value = false;
            }
        };

        /*
            ADD
        */
        const fee = ref({
            name: '',
            percentage: 0
        });
        const addFee = async () => {
            if (!validation(fee)) return;
            try {
                is_loading.value = true;
                const res = await api.post(`/fees`, fee.value);
                if (res.status == 201) {
                    Object.assign(fee.value, { name: '', percentage: 0 });
                    //Thông báo
                    message.emit('show-message', {
                        title: 'Thành công',
                        text: 'Thêm phí sàn thành công.',
                        type: 'success'
                    });
                    //Gọi lại để phân trang.
                    getFees();
                }
            } catch (error) {
                if (error.response && error.response.status === 422) {
                    errors.value = error.response.data.errors;
                } else {
                    console.warn('Lỗi validation:', error);
                }
            } finally {
                is_loading.value = false;
            }
        }

        /*
           EDIT
       */
        const feeEdit = ref({
            id: null,
            name: '',
            percentage: 0
        });
        const isModelEdit = ref(false);
        const showModalEdit = (fee_id) => {
            errors.value = {};
            isModelEdit.value = true;
            const found = fees.value.find((fee) => fee.id == fee_id);
            feeEdit.value = { ...found };
        }
        const editFee = async () => {
            // console.log('hihi');

            if (!validation(feeEdit)) return;
            try {
                is_loading.value = true;
                const res = await api.put(`/fees/${feeEdit.value.id}`, feeEdit.value);
                // console.log(res);
                if (res.status == 200) {
                    const index = fees.value.findIndex(fee => fee.id === feeEdit.value.id);
                    if (index !== -1) {
                        fees.value[index] = { ...res.data.fee };
                    }
                    message.emit('show-message', {
                        title: 'Thành công',
                        text: res.data.message,
                        type: 'success'
                    });
                    isModelEdit.value = false;
                    is_loading.value = false;
                }
            } catch (error) {
                if (error.response && error.response.status === 422) {
                    errors.value = error.response.data.errors;
                } else {
                    console.warn('Lỗi validation:', error);
                }
            } finally {
                is_loading.value = false;
            }
        }

        /*
            DELETE
        */
        const feeIdToDelete = ref(null)
        const isDeleteModal = ref(false);
        const selectedIds = ref([]);
        const confirmDelete = (fee_id) => {
            feeIdToDelete.value = fee_id;
            isDeleteModal.value = true;
        }
        const closeModelDelete = () => {
            feeIdToDelete.value = null;
            isDeleteModal.value = false;
        }
        const deleteFee = async () => {
            if (!feeIdToDelete.value) return;
            try {
                is_loading.value = true;
                const res = await api.delete(`/fees/${feeIdToDelete.value}`);
                if (res.status == 200) {
                    fees.value = fees.value.filter((fee) => fee.id !== feeIdToDelete.value);
                    //Thông báo
                    message.emit('show-message', {
                        title: 'Thành công',
                        text: 'Xóa phí sàn thành công.',
                        type: 'success'
                    });
                    is_loading.value = false;
                }
            } catch (error) {
                if (error && error.status == 400 && error.response.data.message) {
                    //Thông báo
                    message.emit('show-message', {
                        title: 'Lỗi',
                        text: error.response.data.message,
                        type: 'error'
                    });
                }
            } finally {
                isDeleteModal.value = false;
                feeIdToDelete.value = null;
                is_loading.value = false;
            }
        }
        const isAllSelected = computed(() => {
            const allFeeIds = fees.value.map(fee => fee.id);
            return selectedIds.value.length === allFeeIds.length;
        })
        const toggleSelection = (id) => {
            if (selectedIds.value.includes(id)) {
                selectedIds.value = selectedIds.value.filter(item => item !== id)
            } else {
                selectedIds.value.push(id)
            }
        }
        const toggleSelectionAll = (e) => {
            const checked = e.target.checked;
            const allFeeIds = fees.value.map(fee => fee.id);
            if (checked) {
                selectedIds.value = allFeeIds;
            } else {
                selectedIds.value = [];
            }
        }
        const confirmDeleteSeleted = () => {
            if (selectedIds.value.length === 0) {
                //Thông báo
                message.emit('show-message', {
                    title: 'Cảnh báo',
                    text: 'Vui lòng chọn phí sàn để xóa.',
                    type: 'warning'
                });
                return;
            }
            isDeleteModal.value = true;
        }
        const deleteSelected = async () => {
            try {
                is_loading.value = true;
                const res = await api.delete(`/fees/delete-multiple`, {
                    data: { ids: selectedIds.value }
                });
                if (res.status == 200) {
                    //Thông báo
                    message.emit('show-message', {
                        title: 'Thông báo',
                        text: `${res.data.notice}: ${res.data.not_deleted_names}`,
                        type: 'info'
                    });
                    selectedIds.value = [];
                    isDeleteModal.value = false;
                    currentPage.value = 1;
                    is_loading.value = false;
                    getFees();
                }
            } catch (error) {
                console.error("Lỗi khi xóa:", error)
            } finally {
                is_loading.value = false;
            }
        }

        /*
            SUPPORT
        */
        const is_loading = ref(false)
        const errors = ref({});
        const validation = (fee) => {
            errors.value = {};

            if (!fee.value.name.trim()) {
                errors.value.name = "Tên Phí không được để trống.";
            }

            if (fee.value.percentage == null || fee.value.percentage === '') {
                errors.value.percentage = "Phí không được để trống.";
            } else if (fee.value.percentage < 0 || fee.value.percentage > 100) {
                errors.value.percentage = "Phí phải từ 0 đến 100.";
            }

            return Object.keys(errors.value).length === 0;
        };


        /*  
            PAGINATION
        */
        const meta = ref({});
        const currentPage = ref(1);
        const onChangePage = (page) => {
            currentPage.value = page;
            getFees();
        };
        /*
            SEARCH 
        */
        const searchValue = ref('');
        const onSearch = () => {
            currentPage.value = 1;
            getFees();
        };

        /*
            LOAD
        */
        onMounted(async () => {
            await getFees();
        });

        return {
            /*
                GET
            */
            fees,
            totalFee,

            /*
               ADD
           */
            fee,
            addFee,

            /*
               EDIT
           */
            showModalEdit,
            isModelEdit,
            feeEdit,
            editFee,

            /*
               DELETE
           */
            isDeleteModal,
            confirmDelete,
            closeModelDelete,
            deleteFee,
            toggleSelection,
            confirmDeleteSeleted,
            feeIdToDelete,
            deleteSelected,
            toggleSelectionAll,
            isAllSelected,

            /*
                SUPPORT
            */
            errors,

            /*
               PAGINATION
            */
            meta,
            currentPage,
            onChangePage,

            /* 
                SEARCH
            */
            searchValue,
            onSearch,
        };
    }
};
</script>