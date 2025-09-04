<template>
  <div class="__main mt-3">
    <div v-if="loading_check" class="d-flex align-items-center justify-content-center" style="min-height: 90vh;">
      <loading_dot />
    </div>
    <div v-else class="__content bg-white px-4 py-2 d-flex align-items-center border-bottom shadow-sm">
      <div class="__register w-100">
        <!-- <div class="content__header d-flex justify-content-center">
          <div class="content__header--step active fs-14">
            Thông tin Shop
            <div class="step-progress"></div>
          </div>

          <div class="content__header--step fs-14">
            Cài đặt vận chuyển
            <div class="step-progress"></div>
          </div>

          <div class="content__header--step d-lg-flex d-sm-none fs-14">
            Hoàn tất
            <div class="step-progress"></div>
          </div>
        </div> -->

        <div class="content__main mt-5 fs-14">
          <div class="row mb-4 d-flex align-items-center">
            <label class="col-lg-2 col-sm-4 d-flex justify-content-end fs-14"><span class="text-danger">*</span> Tên
              Shop</label>
            <div class="col-lg-4 col-sm-6">
              <input type="text" name="" id="" class="radius-3 w-100 outline-none border px-2 padding-t-2 padding-b-2"
                v-model="shop_name" />
              <p v-if="shop_name_error" class="text-danger small">
                {{ shop_name_error }}
              </p>
            </div>
          </div>

          <div class="row mb-4 d-flex">
            <label class="col-lg-2 col-sm-4 d-flex justify-content-end fs-14"><span class="text-danger">*</span> Địa chỉ
              lấy hàng</label>
            <div class="col-lg-4 col-sm-6 d-flex flex-column">
              <div class="name-number d-flex align-items-center">
                <span class="shop-chanel__name fs-12">{{
                  display_address_pickup.name
                }}</span>
                <span class="mx-1" v-show="display_address_pickup.name">|</span>
                <span class="shop-chanel__number fs-12">{{
                  display_address_pickup.phone
                }}</span>
              </div>

              <span class="shop-chanel__detail--address fs-12">{{
                display_address_pickup.detail_address
              }}</span>
              <span class="shop-chanel__province/city fs-12">{{
                display_address_pickup.province_name
              }}</span>
              <span class="shop-chanel__district fs-12">{{
                display_address_pickup.district_name
              }}</span>
              <span class="shop-chanel__ward fs-12">{{
                display_address_pickup.ward_name
              }}</span>

              <div class="shop-chanel__edit--address mt-2 cursor-pointer" @click="open_modal">
                <i class="fa-solid fa-pen-to-square fs-12"></i>
                <span class="ms-1 fs-14">Chỉnh sửa địa chỉ</span>
                <p v-if="address_error" class="text-danger small">
                  {{ address_error }}
                </p>
              </div>
            </div>
          </div>

          <div class="row mb-4 d-flex align-items-center">
            <label class="col-lg-2 col-sm-4 d-flex justify-content-end fs-14"><span class="text-danger">*</span>
              Email</label>
            <div class="col-lg-4 col-sm-6">
              <input type="text" name="" id=""
                class="radius-3 w-100 outline-none border px-2 padding-t-2 padding-b-2 bg-grey" readonly
                v-model="user_email" />
            </div>
          </div>

          <!-- <div class="row mb-4 d-flex align-items-center">
            <label class="col-lg-2 col-sm-4 d-flex justify-content-end fs-14"><span class="text-danger">*</span> Số điện
              thoại</label>
            <div class="col-lg-4 col-sm-6 fs-14">{{ user_phone }}</div>
          </div> -->
        </div>

        <div class="content__footer border-top py-2">
          <div class="d-flex justify-content-end mt-3">
            <button class="main-btn fs-12 fw-500 px-3 py-2" type="button" @click="handle_add_shop">
              Tiếp theo
            </button>
          </div>

          <!-- <div class="d-flex justify-content-between mt-3">
                <button class="sub-btn fs-12 fw-500 px-3 py-2">Quay lại</button>
                <button class="main-btn fs-12 fw-500 px-3 py-2">Tiếp theo</button>
              </div> -->
        </div>
      </div>
    </div>
  </div>

  <div class="modal-background container-fluid d-flex justify-content-center align-items-center" v-if="is_modal_active">
    <div class="modal-edit-address modal-ezeshop bg-white shadow-sm px-4 pt-4 pb-2">
      <div class="modal-ezeshop__top d-flex justify-content-between align-items-center">
        <div class="modal-ezeshop__name fs-18 fw-500">Sửa địa chỉ</div>
        <div class="modal-ezeshop__cancel cursor-pointer" @click="close_modal">
          <i class="fa-solid fa-xmark fs-18"></i>
        </div>
      </div>
      <!-- <div class="scroll-wrapper"> -->
      <div class="scroll-wrapper" style="max-height: calc(90vh - 120px); overflow-y: auto; padding: 16px">
        <div class="modal-ezeshop__main w-100">
          <form action="" id="form-edit-address">
            <div class="col-12 mb-4">
              <span class="fs-14">Họ & tên</span>
              <input type="text" name="" id=""
                class="fs-12 radius-3 w-100 outline-none border px-2 padding-t-4 padding-b-4 mt-2"
                v-model="pickup.name" />
              <p v-if="pickup_errors.name" class="text-danger small">
                {{ pickup_errors.name }}
              </p>
            </div>

            <div class="col-12 mb-4">
              <span class="fs-14">Số điện thoại</span>
              <input type="text" name="" id=""
                class="fs-12 radius-3 w-100 outline-none border px-2 padding-t-4 padding-b-4 mt-2"
                v-model="pickup.phone" />
            </div>
            <p v-if="pickup_errors.phone" class="text-danger small">
              {{ pickup_errors.phone }}
            </p>

            <div class="col-12 mb-4">
              <p class="mb-4 fs-18 fw-500">Địa chỉ</p>

              <span class="fs-14 mb-2">Tỉnh/Thành phố/Quận/Huyện/Phường/Xã</span>
              <div
                class="col-12 border radius-3 w-100 outline-none border px-2 padding-t-2 padding-b-2 d-flex justify-content-between mt-2 position-relative cursor-pointer">
                <div class="province-district-ward d-flex fs-14 align-items-center" @click="open_address">
                  <div class="__province">
                    {{
                      address.selected_province
                        ? address.province_list.find(
                          (p) => p.ProvinceID == address.selected_province
                        )?.ProvinceName
                        : "Tỉnh"
                    }}
                  </div>
                  <span>/</span>
                  <div class="__district">
                    {{
                      address.selected_district
                        ? address.district_list.find(
                          (d) => d.DistrictID == address.selected_district
                        )?.DistrictName
                        : "Quận"
                    }}
                  </div>
                  <span>/</span>
                  <div class="__ward">
                    {{
                      address.selected_ward
                        ? address.ward_list.find(
                          (w) => w.WardCode == address.selected_ward
                        )?.WardName
                        : "Huyện"
                    }}
                  </div>
                </div>

                <div class="drop-icon">
                  <i class="fa-solid fa-chevron-down fs-12 text-grey"></i>
                </div>

                <div
                  class="province-district-ward__select position-absolute bg-white w-100 shadow-sm overflow-hidden p-2"
                  v-show="select_address">
                  <div class="select__tab d-flex gap-1 justify-content-between">
                    <div class="tab__item" :class="{ active: address.step === 'province' }"
                      @click="step_address('province')">
                      Tỉnh/Thành phố
                    </div>
                    <div class="tab__item" :class="{ active: address.step === 'district' }" :style="address.selected_province == null
                      ? 'cursor: not-allowed'
                      : 'cursor: pointer'
                      " @click="
                        address.selected_province !== null &&
                        step_address('district')
                        ">
                      Quận/Huyện
                    </div>
                    <div class="tab__item" :class="{ active: address.step === 'ward' }" :style="address.selected_district == null
                      ? 'cursor: not-allowed'
                      : 'cursor: pointer'
                      " @click="
                        address.selected_district !== null &&
                        step_address('ward')
                        ">
                      Phường/Xã
                    </div>
                  </div>

                  <div class="select__main mt-2 d-flex flex-wrap gap-2 shadow-sm" v-if="address.step === 'province'">
                    <div class="select__item px-3 py-2 fs-12 cursor-pointer" :class="{
                      active: province.ProvinceID == address.selected_province,
                    }" v-for="province in address.province_list" :key="province.ProvinceID"
                      @click="select_province(province.ProvinceID)">
                      {{ province.ProvinceName }}
                    </div>
                  </div>

                  <div class="select__main mt-2 d-flex flex-wrap gap-2 shadow-sm"
                    v-else-if="address.step === 'district'">
                    <div class="select__item px-3 py-2 fs-12 cursor-pointer" :class="{
                      active: district.DistrictID == address.selected_district,
                    }" v-for="district in address.district_list" :key="district.DistrictName"
                      @click="select_district(district.DistrictID)">
                      {{ district.DistrictName }}
                    </div>
                  </div>

                  <div class="select__main mt-2 d-flex flex-wrap gap-2 shadow-sm" v-else-if="address.step === 'ward'">
                    <div class="select__item px-3 py-2 fs-12 cursor-pointer"
                      :class="{ active: ward.WardCode == address.selected_ward }" v-for="ward in address.ward_list"
                      :key="ward.WardCode" @click="select_ward(ward.WardCode)">
                      {{ ward.WardName }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <p v-if="pickup_errors.province" class="text-danger small">
              {{ pickup_errors.province }}
            </p>
            <p v-if="pickup_errors.district" class="text-danger small">
              {{ pickup_errors.district }}
            </p>
            <p v-if="pickup_errors.ward" class="text-danger small">
              {{ pickup_errors.ward }}
            </p>

            <div class="col-12 mb-4">
              <span class="fs-14">Địa chỉ chi tiết</span>
              <textarea name="" id="" class="fs-12 radius-3 w-100 outline-none border px-2 py-3 mt-2"
                v-model="pickup.detail_address"></textarea>
            </div>
            <p v-if="pickup_errors.detail_address" class="text-danger small">
              {{ pickup_errors.detail_address }}
            </p>
          </form>
        </div>
      </div>

      <div class="modal-ezeshop__bottom d-flex justify-content-end w-100 align-items-center border-top">
        <div class="modal-ezeshop__cancel me-4">
          <button class="white-btn py-2 px-4 fs-14">Huỷ</button>
        </div>
        <div class="modal-ezeshop__save">
          <button class="main-btn py-2 px-4 fs-14" form="form-edit-address" type="button"
            @click="save_display_address_pickup">
            Lưu
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref, reactive, watch } from "vue";
import api from "@/configs/api";
import loading_dot from "@/components/loading/loading__dot.vue";
import message from "@/utils/messageState";
import { useRouter } from "vue-router";

const router = useRouter();

const is_modal_active = ref(false);
const select_address = ref(false);

watch(is_modal_active, (visible) => {
  document.body.style.overflow = visible ? "hidden" : "auto";
});

const open_modal = () => (is_modal_active.value = true);
const close_modal = () => (is_modal_active.value = false);
const open_address = () => (select_address.value = !select_address.value);

const step_address = (step) => (address.step = step);

const pickup = reactive({
  name: "",
  phone: "",
  detail_address: "",
  province_id: "",
  district_id: "",
  ward_code: "",
  province_name: "",
  district_name: "",
  ward_name: "",
});

const address = reactive({
  province_list: [],
  district_list: [],
  ward_list: [],

  selected_province: null,
  selected_district: null,
  selected_ward: null,

  province_name: "",
  district_name: "",
  ward_name: "",

  step: "province",
});

const display_address_pickup = ref({
  name: "",
  phone: "",
  detail_address: "",
  province_id: "",
  province_name: "",
  district_id: "",
  district_name: "",
  ward_code: "",
  ward_name: "",
});

const select_province = async (province) => {
  address.selected_province = province;
  address.selected_district = null;
  address.selected_ward = null;

  const res = await api.get(`/districts/${province}`);
  address.district_list = res.data.districts;

  // console.log(address.district_list)
  address.province_name = address.province_list.find(p => p.ProvinceID == province).ProvinceName
  address.step = "district";
};

const select_district = async (district) => {
  address.selected_district = district;
  address.selected_ward = null;

  const res = await api.get(`/wards/${district}`);
  address.ward_list = res.data.wards;

  address.district_name = address.district_list.find(p => p.DistrictID == district).DistrictName
  address.step = "ward";

  // console.log(address);

};

const select_ward = (ward) => {
  address.selected_ward = ward;
  select_address.value = false;

  address.ward_name = address.ward_list.find(p => p.WardCode == ward).WardName
};

const pickup_errors = ref({
  name: "",
  phone: "",
  detail_address: "",
  province: "",
  district: "",
  ward: "",
});

const validate_pickup = () => {
  let is_error = true;
  pickup_errors.value = {
    name: "",
    phone: "",
    detail_address: "",
    province: "",
    district: "",
    ward: "",
    ward_id: "",
  };

  if (!pickup.name.trim()) {
    pickup_errors.value.name = "Vui lòng nhập tên người gửi!";
    is_error = false;
  } else if (!/^[\p{L}\s]+$/u.test(pickup.name.trim())) {
    pickup_errors.value.name = "Tên người gửi không hợp lệ!";
    is_error = false;
  }

  if (!pickup.phone.trim()) {
    pickup_errors.value.phone = "Vui lòng nhập số điện thoại!";
    is_error = false;
  } else if (!/^0\d{9}$/.test(pickup.phone.trim())) {
    pickup_errors.value.phone = "Số điện thoại không hợp lệ!";
    is_error = false;
  }

  if (!address.selected_province) {
    pickup_errors.value.province = "Vui lòng chọn tỉnh/thành!";
    is_error = false;
  }

  if (!address.selected_district) {
    pickup_errors.value.district = "Vui lòng chọn quận/huyện!";
    is_error = false;
  }

  if (!address.selected_ward) {
    pickup_errors.value.ward = "Vui lòng chọn phường/xã!";
    is_error = false;
  }

  if (!pickup.detail_address.trim()) {
    pickup_errors.value.detail_address = "Vui lòng nhập địa chỉ chi tiết!";
    is_error = false;
  } else if (pickup.detail_address.trim().length < 5) {
    pickup_errors.value.detail_address = "Địa chỉ chi tiết quá ngắn!";
    is_error = false;
  }

  return is_error;
};

const save_display_address_pickup = () => {
  const is_error = validate_pickup();
  if (!is_error) return;

  display_address_pickup.value.name = pickup.name;
  display_address_pickup.value.phone = pickup.phone;
  display_address_pickup.value.detail_address = pickup.detail_address;

  display_address_pickup.value.province_id = address.selected_province;
  display_address_pickup.value.district_id = address.selected_district;
  display_address_pickup.value.ward_code = address.selected_ward;

  display_address_pickup.value.province_name = address.province_name;
  display_address_pickup.value.district_name = address.district_name;
  display_address_pickup.value.ward_name = address.ward_name;

  address_error.value = "";

  message.emit("show-message", {
    title: "Thông báo",
    text: "Lưu địa chỉ thành công",
    type: "success",
  });

  close_modal();
};

const shop_name = ref("");
const shop_name_error = ref("");
const address_error = ref("");
const handle_add_shop = async () => {
  let is_error = false;

  shop_name_error.value = "";
  address_error.value = "";

  if (!shop_name.value.trim()) {
    shop_name_error.value = "Vui lòng nhập tên Shop!";
    is_error = true;
  } else if (shop_name.value.trim().length < 3) {
    shop_name_error.value = "Tên Shop không được ít hơn 3 kí tự!";
    is_error = true;
  }

  if (
    !display_address_pickup.value.detail_address.trim()
    // !display_address_pickup.value.province.trim() ||
    // !display_address_pickup.value.district.trim() ||
    // !display_address_pickup.value.ward.trim()
  ) {
    address_error.value = "Vui lòng nhập địa chỉ hợp lệ!";
    is_error = true;
  }

  if (is_error) return;

  try {
    // is_loading.value = true;
    const data = {
      shop_name: shop_name.value,
      ...display_address_pickup.value,
    };

    const res = await api.post("/shops", data);
    if (res.status == 200 || res.status == 201) {
      localStorage.setItem('shop_id', res.data.shop_id.toString())
      message.emit("show-message", { title: "Thông báo", text: res.data.message, type: "success" });
    }
  } catch (err) {
    const res = err.response.data.message;
    message.emit("show-message", {
      title: "Thông báo",
      text: res,
      type: "error",
    });
  } finally {
    // is_loading.value = false;
    router.push("/kenh-nguoi-ban/them-san-pham");
  }
};

const user_email = ref(null);
const user_phone = ref(null);
const loading_check = ref(false)
onMounted(async () => {
  const token = localStorage.getItem("token");
  if (!token) {
    router.push("/dang-nhap");
    message.emit("show-message", { title: "Thông báo", text: 'Bạn cần đăng nhập để thực hiện hành động này', type: "error" });
    return
  }
  try {
    loading_check.value = true
    const shop_id = localStorage.getItem("shop_id");
    if (shop_id != 0) {
      router.push("/kenh-nguoi-ban/doanh-thu");
      return
    }
    loading_check.value = false
    const res_user = await api.get("/user");

    user_email.value = res_user.data.user.email;
    user_phone.value = res_user.data.user.phone; ``

    const res_province = await api.get("/provinces");

    address.province_list = res_province.data.provinces;
  } catch (error) {
    console.error("Lỗi khi kiểm tra shop:", error);
    // message.emit("show-message", { title: "Thông báo", text: 'Bắt đầu đăng ký shop của bạn', type: "info" });
  }
});
</script>

<style scoped>
.content__header {
  padding: 60px 20px 30px 20px;
  background-color: #fff;
  border-radius: 3px;
  border-bottom: 1px solid #f0f0f0;
  flex-shrink: 0;
}

.content__header>.content__header--step.active {
  font-weight: 500;
}

.content__header>.content__header--step.active .step-progress::after,
.content__header>.content__header--step.active .step-progress {
  background-color: var(--text-color);
}

.content__header>.content__header--step {
  position: relative;
  width: 170px;
  margin: 0 20px 0 0px;
  display: flex;
  justify-content: center;
}

.content__header>.content__header--step>.step-progress {
  position: absolute;
  top: -20px;
  left: 50%;
  transform: translateX(-50%);
  width: 10px;
  height: 10px;
  border-radius: 50%;
  background-color: #e1dfdf;
}

.content__header>.content__header--step>.step-progress::after {
  content: "";
  position: absolute;
  left: -160px;
  top: 5px;
  width: 150px;
  height: 1px;
  background-color: #f0f0f0;
}

.content__header>.content__header--step:first-child>.step-progress::after {
  content: none;
}

.shop-chanel__content>.content__footer {
  flex-shrink: 0;
}

.shop-chanel__content>.content__main {
  flex: 1;
}
</style>
