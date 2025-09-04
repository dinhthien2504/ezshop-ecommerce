<template>
    <div class="profile__right overflow-hidden">
        <div v-if="loading.fetch" class="d-flex align-items-center justify-content-center p-4 text-center flex-column"
            style="min-height: 500px;">
            <loading__dot />
            <p class="mt-2">Đang tải dữ liệu địa chỉ...</p>
        </div>
        <div v-else class="border py-3 px-5 bg-white shadow-sm mx-1 mt-lg-3 mb-2" style="min-height: 70vh;">
            <div class="border-bottom pb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5>Địa Chỉ Của Tôi</h5>
                        <p class="text-muted">Quản lý danh sách địa chỉ</p>
                    </div>
                    <button @click="open_modal_address(null)"
                        class="btn btn-danger px-3 py-2 fw-semibold radius-2 ms-3">
                        Thêm địa chỉ</button>
                </div>
            </div>
            <div v-if="addresses.length === 0" class="d-flex align-items-center justify-content-center flex-column"
                style="min-height: 400px;">
                <svg style="width: 150px;" fill="none" viewBox="0 0 121 120" class="VMnhZp"><path d="M16 79.5h19.5M43 57.5l-2 19" stroke="#BDBDBD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path><path d="M56.995 78.791v-.001L41.2 38.195c-2.305-5.916-2.371-12.709.44-18.236 1.576-3.095 4.06-6.058 7.977-8 5.061-2.5 11.038-2.58 16.272-.393 3.356 1.41 7 3.92 9.433 8.43v.002c2.837 5.248 2.755 11.853.602 17.603L60.503 78.766v.001c-.617 1.636-2.88 1.643-3.508.024Z" fill="#fff" stroke="#BDBDBD" stroke-width="2"></path><path d="m75.5 58.5 7 52.5M13 93h95.5M40.5 82.5 30.5 93 28 110.5" stroke="#BDBDBD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path><path d="M44.5 79.5c0 .55-.318 1.151-1.038 1.656-.717.502-1.761.844-2.962.844-1.2 0-2.245-.342-2.962-.844-.72-.505-1.038-1.105-1.038-1.656 0-.55.318-1.151 1.038-1.656.717-.502 1.761-.844 2.962-.844 1.2 0 2.245.342 2.962.844.72.505 1.038 1.105 1.038 1.656Z" stroke="#BDBDBD" stroke-width="2"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M48.333 68H18.5a1 1 0 1 0 0 2h30.667l-.834-2Zm20.5 2H102a1 1 0 0 0 0-2H69.667l-.834 2Z" fill="#BDBDBD"></path><path d="M82 73h20l3 16H84.5L82 73ZM34.5 97H76l1.5 13H33l1.5-13ZM20.5 58h18l-1 7h-18l1-7Z" fill="#E8E8E8"></path><path clip-rule="evenodd" d="M19.5 41a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM102.5 60a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" stroke="#E8E8E8" stroke-width="2"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M93.5 22a1 1 0 0 0-1 1v3h-3a1 1 0 1 0 0 2h3v3a1 1 0 1 0 2 0v-3h3a1 1 0 1 0 0-2h-3v-3a1 1 0 0 0-1-1Z" fill="#E8E8E8"></path><circle cx="58.5" cy="27" r="7" stroke="#BDBDBD" stroke-width="2"></circle></svg>
                <p class="text-muted">Bạn chưa có địa chỉ nào</p>
            </div>
            <div v-else class="">
                <div v-for="address in addresses" class="d-flex gap-2 border-bottom py-2">
                    <div class="d-flex flex-column w-100">
                        <div class="d-flex justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <p class="m-0 fs-16 text-black fw-medium">{{ address.name }}</p>
                                |
                                <span class="fs-14">{{ address.phone }}</span>
                            </div>
                            <div class="ms-5">
                                <span @click="open_modal_address(address.address_id)"
                                    class="text-color fw-semibold fs-14 cursor-pointer">Cập nhật</span>
                                <!-- <span v-if="address.is_default != 1"
                                    class="text-color fw-semibold fs-14 cursor-pointer ms-3">Xóa</span> -->

                            </div>
                        </div>
                        <p class="fs-14 m-0 fw-medium text-secondary mt-1">{{ address.address_detail }}</p>
                        <div class="d-flex justify-content-between">
                            <p class="fs-14 m-0 fw-medium text-secondary">
                                {{ address.ward }},
                                {{ address.district }},
                                {{ address.province }}
                            </p>
                            <button v-if="address.is_default != 1" @click="setDefaultAddress(address.address_id)"
                                class="btn btn-outline-secondary fw-semibold fs-12 p-1 ms-5"
                                style="border-radius: 2px; min-width: 130px; height: 30px;">Thiết lập mặc
                                định</button>
                        </div>

                        <div v-if="address.is_default == 1" class="text-color fs-12 mt-1">Mặc định</div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Modal for adding/editing address -->
        <div class="modal-background container-fluid d-flex justify-content-center align-items-center"
            v-if="is_modal_active">
            <div v-if="loading.editAddress" class="d-flex align-items-center justify-content-center">
                <loading__dot />
            </div>
            <div v-else class="modal-edit-address modal-ezeshop bg-white shadow-sm px-4 pt-4 pb-2">
                <div class="modal-ezeshop__top d-flex justify-content-between align-items-center">
                    <div class="modal-ezeshop__name fs-18 fw-500">
                        {{ is_editing_address ? 'Cập nhật địa chỉ' : 'Thêm địa chỉ' }}
                    </div>
                    <div class="modal-ezeshop__cancel cursor-pointer" @click="close_modal_address"><i
                            class="fa-solid fa-xmark fs-18"></i></div>
                </div>
                <div class="scroll-wrapper">
                    <div class="modal-ezeshop__main w-100">
                        <form @submit.prevent="save_address_pickup" id="form-address">
                            <div class="row g-0">
                                <div class="col-6 pe-1">
                                    <span class="fs-14">Họ & tên</span>
                                    <input type="text" name="" id=""
                                        class="fs-12 radius-3 w-100 outline-none border px-2 padding-t-4 padding-b-4 mt-2"
                                        v-model="pickup.name">
                                    <p v-if="errors.name" class="text-danger small">
                                        {{ Array.isArray(errors.name) ? errors.name[0] : errors.name }}
                                    </p>
                                </div>
                                <div class="col-6 ps-1">
                                    <span class="fs-14">Số điện thoại</span>
                                    <input type="text"
                                        class="fs-12 radius-3 w-100 outline-none border px-2 padding-t-4 padding-b-4 mt-2"
                                        v-model="pickup.phone">
                                    <p v-if="errors.phone" class="text-danger small">
                                        {{ Array.isArray(errors.phone) ? errors.phone[0] : errors.phone }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-12 my-3">
                                <!-- <p class="mb-4 fs-18 fw-500">Địa chỉ</p> -->
                                <span class="fs-14">Tỉnh/Thành phố/Quận/Huyện/Phường/Xã</span>
                                <div
                                    class="col-12 border radius-3 w-100 outline-none border px-2 padding-t-2 padding-b-2 d-flex justify-content-between mt-2 position-relative cursor-pointer">
                                    <div class="province-district-ward d-flex fs-14 align-items-center"
                                        @click="open_address">
                                        <div class="__province">{{address.selected_province ?
                                            address.province_list.find(p =>
                                                p.ProvinceID == address.selected_province)?.ProvinceName : "Tỉnh"}}
                                        </div>
                                        <span>/</span>
                                        <div class="__district">{{address.selected_district ?
                                            address.district_list.find(d =>
                                                d.DistrictID == address.selected_district)?.DistrictName : "Quận"}}
                                        </div>
                                        <span>/</span>
                                        <div class="__ward">{{address.selected_ward ?
                                            address.ward_list.find(w => w.WardCode ==
                                                address.selected_ward)?.WardName :
                                            "Huyện"}}
                                        </div>
                                    </div>

                                    <div class="drop-icon"><i class="fa-solid fa-chevron-down fs-12 text-grey"></i>
                                    </div>

                                    <div class="province-district-ward__select position-absolute bg-white w-100 shadow-sm overflow-hidden p-2"
                                        v-show="select_address">
                                        <div class="select__tab d-flex gap-1 justify-content-between">
                                            <div class="tab__item" :class="{ active: address.step === 'province' }"
                                                @click="step_address('province')">Tỉnh/Thành phố</div>
                                            <div class="tab__item" :class="{ active: address.step === 'district' }"
                                                :style="address.selected_province == null ? 'cursor: not-allowed' : 'cursor: pointer'"
                                                @click="address.selected_province !== null && step_address('district')">
                                                Quận/Huyện</div>
                                            <div class="tab__item" :class="{ active: address.step === 'ward' }"
                                                :style="address.selected_district == null ? 'cursor: not-allowed' : 'cursor: pointer'"
                                                @click="address.selected_district !== null && step_address('ward')">
                                                Phường/Xã
                                            </div>
                                        </div>
                                        <div class="select__main mt-2 d-flex flex-wrap gap-2 shadow-sm"
                                            v-if="address.step === 'province'">
                                            <div class="select__item px-3 py-2 fs-12 cursor-pointer"
                                                :class="{ active: province.ProvinceID == address.selected_province }"
                                                v-for="province in address.province_list" :key="province.ProvinceID"
                                                @click="select_province(province.ProvinceID)">{{
                                                    province.ProvinceName
                                                }}
                                            </div>
                                        </div>

                                        <div class="select__main mt-2 d-flex flex-wrap gap-2 shadow-sm"
                                            v-else-if="address.step === 'district'">
                                            <div class="select__item px-3 py-2 fs-12 cursor-pointer"
                                                :class="{ active: district.DistrictID == address.selected_district }"
                                                v-for="district in address.district_list" :key="district.DistrictID"
                                                @click="select_district(district.DistrictID)">{{
                                                    district.DistrictName
                                                }}
                                            </div>
                                        </div>
                                        <div class="select__main mt-2 d-flex flex-wrap gap-2 shadow-sm"
                                            v-else-if="address.step === 'ward'">
                                            <div class="select__item px-3 py-2 fs-12 cursor-pointer"
                                                :class="{ active: ward.WardCode == address.selected_ward }"
                                                v-for="ward in address.ward_list" :key="ward.WardCode"
                                                @click="select_ward(ward.WardCode)">
                                                {{ ward.WardName }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p v-if="errors.province" class="text-danger small">
                                {{ Array.isArray(errors.province) ? errors.province[0] : errors.province }}
                            </p>
                            <p v-if="errors.district" class="text-danger small">
                                {{ Array.isArray(errors.district) ? errors.district[0] : errors.district }}
                            </p>
                            <p v-if="errors.ward" class="text-danger small">
                                {{ Array.isArray(errors.ward) ? errors.ward[0] : errors.ward }}
                            </p>


                            <div class="col-12 mb-4">
                                <span class="fs-14">Địa chỉ chi tiết</span>
                                <textarea rows="4" class="fs-12 radius-3 w-100 outline-none border p-1 mt-2"
                                    v-model="pickup.address_detail"></textarea>
                                <p v-if="errors.address_detail" class="text-danger small">
                                    {{ Array.isArray(errors.address_detail) ? errors.address_detail[0] :
                                        errors.address_detail
                                    }}
                                </p>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="modal-ezeshop__bottom d-flex justify-content-end w-100 align-items-center border-top">
                    <div class="modal-ezeshop__cancel me-4">
                        <button @click="close_modal_address" class="white-btn py-2 px-4 fs-14">Huỷ</button>
                    </div>
                    <div class="modal-ezeshop__save">
                        <button class="main-btn py-2 px-4 fs-14" form="form-address" type="submit">{{
                            is_editing_address
                                ?
                                'Lưu' : 'Thêm' }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import api from '@/configs/api'
import message from '@/utils/messageState'
import loading__dot from '@/components/loading/loading__dot.vue'
import loading__circle from '@/components/loading/loading__loader-circle.vue'
/*
    ADDRESS
*/
const select_address = ref(false)
const is_modal_active = ref(false)
const is_modal_active_addresses = ref(false)
const addresses = ref([])
const is_editing_address = ref(false)
const editing_address_id = ref(null)
const errors = ref({})
const address = reactive({
    province_list: [],
    district_list: [],
    ward_list: [],

    selected_province: null,
    selected_district: null,
    selected_ward: null,

    step: 'province'
})
const pickup = reactive({
    name: '',
    phone: '',
    address_detail: '',
})
const display_address_pickup = ref({
    address_id: '',
    name: '',
    phone: '',
    address_detail: '',
    province: '',
    district: '',
    ward: '',
    district_id: null,
    ward_code: null
})
const loading = ref({
    fetch: false,
    editAddress: false
})
const open_modal_address = async (edit_id = null) => {
    is_modal_active_addresses.value = false
    is_modal_active.value = true
    if (edit_id != null) {
        // === MODE EDIT ===
        is_editing_address.value = true
        editing_address_id.value = edit_id
        loading.value.editAddress = true

        const addressToEdit = addresses.value.find((address) => address.address_id == edit_id)

        pickup.name = addressToEdit.name
        pickup.phone = addressToEdit.phone
        pickup.address_detail = addressToEdit.address_detail

        await select_province(addressToEdit.province_id)
        await select_district(addressToEdit.district_id)
        await select_ward(addressToEdit.ward_code)
        loading.value.editAddress = false
    } else {
        // === MODE ADD ===
        is_editing_address.value = false
        editing_address_id.value = null

        // Reset form pickup
        pickup.name = ''
        pickup.phone = ''
        pickup.address_detail = ''

        // Reset địa chỉ chọn
        address.selected_province = null
        address.selected_district = null
        address.selected_ward = null

        address.district_list = []
        address.ward_list = []
        address.step = 'province'
    }


}

const close_modal_address = () => {
    is_modal_active.value = false
    is_modal_active_addresses.value = true
}
const open_address = () => select_address.value = !select_address.value
const step_address = (step) => address.step = step

const select_province = async (province) => {
    address.selected_province = province
    address.selected_district = null
    address.selected_ward = null

    const res = await api.get(`/districts/${province}`)
    address.district_list = res.data.districts

    address.step = 'district'

}

const select_district = async (district) => {
    address.selected_district = district
    address.selected_ward = null

    const res = await api.get(`/wards/${district}`)
    address.ward_list = res.data.wards

    address.step = 'ward'
}

const select_ward = (ward) => {
    address.selected_ward = ward
    select_address.value = false
}

const validate_pickup = () => {
    let is_error = true
    errors.value = {}
    if (!pickup.name.trim()) {
        errors.value.name = "Vui lòng nhập tên người gửi!"
        is_error = false
    }

    if (!pickup.phone.trim()) {
        errors.value.phone = "Vui lòng nhập số điện thoại!"
        is_error = false
    } else if (!/^0\d{9}$/.test(pickup.phone.trim())) {
        errors.value.phone = "Số điện thoại không hợp lệ!"
        is_error = false
    }

    if (!address.selected_province) {
        errors.value.province = "Vui lòng chọn tỉnh/thành!"
        is_error = false
    }

    if (!address.selected_district) {
        errors.value.district = "Vui lòng chọn quận/huyện!"
        is_error = false
    }

    if (!address.selected_ward) {
        errors.value.ward = "Vui lòng chọn phường/xã!"
        is_error = false
    }

    if (!pickup.address_detail.trim()) {
        errors.value.address_detail = "Vui lòng nhập địa chỉ chi tiết!"
        is_error = false
    } else if (pickup.address_detail.trim().length < 5) {
        errors.value.address_detail = "Địa chỉ chi tiết quá ngắn!"
        is_error = false
    }
    return is_error
}

const save_address_pickup = async () => {
    const is_error = validate_pickup()
    if (!is_error) return

    errors.value = {}

    const selectedProvince = address.province_list.find(p => p.ProvinceID == address.selected_province)
    const selectedDistrict = address.district_list.find(d => d.DistrictID == address.selected_district)
    const selectedWard = address.ward_list.find(w => w.WardCode == address.selected_ward)

    if (!selectedProvince || !selectedDistrict || !selectedWard) {
        message.emit("show-message", {
            title: "Lỗi dữ liệu",
            text: "Không thể xác định địa chỉ. Vui lòng thử lại.",
            type: "error"
        })
        return
    }
    close_modal_address()
    is_modal_active.value = false
    try {
        if (is_editing_address.value) {
            const dataEdit = {
                name: pickup.name,
                phone: pickup.phone,
                address_detail: pickup.address_detail,
                province: selectedProvince.ProvinceName,
                district: selectedDistrict.DistrictName,
                district_id: selectedDistrict.DistrictID,
                ward: selectedWard.WardName,
                ward_code: selectedWard.WardCode
            }
            const inx = addresses.value.findIndex(a => a.address_id == editing_address_id.value);

            if (inx != -1) {
                addresses.value[inx] = {
                    ...addresses.value[inx],
                    ...dataEdit
                }
            }
            await api.put(`/address/${editing_address_id.value}`, dataEdit)
            message.emit("show-message", {
                title: "Thành công",
                text: "Địa chỉ đã được cập nhật thành công.",
                type: "success"
            })
        } else {
            const dataAdd = {
                name: pickup.name,
                phone: pickup.phone,
                address_detail: pickup.address_detail,
                province: selectedProvince.ProvinceName,
                province_id: selectedProvince.ProvinceID,
                district: selectedDistrict.DistrictName,
                district_id: selectedDistrict.DistrictID,
                ward: selectedWard.WardName,
                ward_code: selectedWard.WardCode
            }
            const res = await api.post('/address', dataAdd)
            if (res.status == 201) {
                dataAdd.address_id = res.data.address.id
                dataAdd.is_default = res.data.address.is_default
                addresses.value.push(dataAdd)
            }
            message.emit("show-message", {
                title: "Thành công",
                text: "Địa chỉ đã được thêm thành công.",
                type: "success"
            })
        }
        getAddress()
    } catch (error) {
        if (error.response?.status === 422) {
            errors.value = error.response.data.errors
        } else {
            console.error(error)
        }
    } finally {
        editing_address_id.value = null
    }
}

const getAddress = async () => {
    try {
        loading.value.fetch = true
        const res = await api.get('/addresses')
        addresses.value = res.data.addresses

        addresses.value.sort((a, b) => {
            return b.is_default - a.is_default
        })
    } catch (error) {
        console.error(error)
        message.emit("show-message", {
            title: "Lỗi",
            text: "Không thể tải danh sách địa chỉ.",
            type: "error"
        })
    } finally {
        loading.value.fetch = false
    }
}

const setDefaultAddress = async (address_id) => {
    try {
        await api.post(`/address/set-default/${address_id}`)
        message.emit("show-message", {
            title: "Thành công",
            text: "Địa chỉ đã được đặt làm mặc định.",
            type: "success"
        })
        // Cập nhật trạng thái is_default cho địa chỉ
        addresses.value.forEach(addr => {
            addr.is_default = (addr.address_id === address_id) ? 1 : 0
        })
    } catch (error) {
        console.error(error)
        message.emit("show-message", {
            title: "Lỗi",
            text: "Không thể đặt địa chỉ này làm mặc định.",
            type: "error"
        })
    }
}
onMounted(async () => {
    await getAddress()
    const res_province = await api.get("/provinces")
    address.province_list = res_province.data.provinces
})
</script>