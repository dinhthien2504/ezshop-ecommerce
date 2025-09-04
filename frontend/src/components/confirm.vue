<template>
  <div class="modal-background container-fluid d-flex justify-content-center align-items-center" v-if="show">
            <div class="modal-ezeshop bg-white shadow-sm px-4 pt-4 pb-2">
                <div class="modal-ezeshop__top d-flex justify-content-between align-items-center">
                    <div class="modal-ezeshop__name fs-18 fw-500">Bạn chắc chứ?</div>
                    <div class="modal-ezeshop__cancel cursor-pointer"><i class="fa-solid fa-xmark fs-18" @click="close_modal"></i></div>
                </div>

                <div class="modal-ezeshop__main w-100 overflow-y-auto">
                    <p class="fw-500 fs-14">{{ message }}</p>
                </div>

                <div class="modal-ezeshop__bottom d-flex justify-content-end w-100 align-items-center border-top">
                    <div class="modal-ezeshop__cancel"><button class="white-btn py-2 px-4 fs-14" @click="cancel">Huỷ</button></div>
                    <div class="modal-ezeshop__save"><button class="main-btn py-2 px-4 fs-14" form="form-edit-address" type="button" @click="confirm">Đồng ý</button></div>
                </div>
            </div>
        </div>
</template>

<script setup>
import { ref, defineExpose } from 'vue';

const show = ref(false);
const message = ref('');
let resolve_callback = null;

const open_confirm = (msg) => {
  message.value = msg;
  show.value = true;
  return new Promise((resolve) => {
    resolve_callback = resolve;
  });
};

const confirm =()=> {
  show.value = false;
  resolve_callback(true);
}

const cancel =()=> {
  show.value = false;
  resolve_callback(false);
}

defineExpose({ open_confirm });
</script>


