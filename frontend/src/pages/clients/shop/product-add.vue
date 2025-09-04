<template>
    <div class="__main pb-5">
        <div class="__title mt-3 bg-white px-4 d-flex align-items-center border-bottom" style="height: 50px;"><span class="fs-14 fw-semibold text-grey">Thêm Sản Phẩm</span></div>
        
        <div class="__content d-flex align-items-center w-100">
        <div class="__product-add w-100">
            <div class="__basic-info px-4 py-3 bg-white shadow-sm">
                <div class="__title fs-18 fw-semibold pt-3">Thông tin cơ bản</div>

                <div class="__content mt-4">
                    <div class="__product-name">
                        <div class="__title fs-14"><span class="text-danger">*</span>Tên sản phẩm</div>
                        <input type="text" name="" id="" placeholder="Ex: Áo thun elmall" class="w-100 mt-1 py-1 px-3 fs-14 border" style="height: 40px; border-radius: 3px;" v-model="product.name">
                        <p v-if="errors.product_name" class="text-danger small mt-2">{{ errors.product_name }}</p>
                    </div>

                    <div class="__product-category mt-3">
                        <div class="__title fs-14"><span class="text-danger">*</span>Danh mục sản phẩm</div>
                        <div class="border d-flex align-items-center px-3 text-grey fs-14 mt-1"
                            style="height: 40px; border-radius: 3px; cursor: pointer;"
                            @click="() => { open_modal(); get_categories() }">
                        {{ categories.find(c => c.id == selected_category)?.name || 'Chọn ngành hàng' }}
                        </div>

                        <p v-if="errors.category" class="text-danger small mt-2">{{ errors.category }}</p>
                    </div>

                    <div class="__product-description mt-3">
                        <div class="__title fs-14 mb-1"><span class="text-danger">*</span>Mô tả sản phẩm</div>
                        
                        <!-- ai generate  -->
                        <div class="ai-generate-description d-flex align-items-center gap-2 mb-2">
                            <button class="main-btn fs-14 px-3 py-1" @click="generate_description(product.name)">Sinh mô tả chuẩn Seo</button>

                            <div class="fs-14" style="color: blue;">Yêu cầu có tên sản phẩm!!</div>
                        </div>

                        <QuillEditor v-model:content="product.description" content-type="html" theme="snow" style="min-height: 300px"/>
                        <p v-if="errors.description" class="text-danger small mt-2">{{ errors.description }}</p>
                    </div>

                    <div class="__product-images mt-3">
                        <div class="__title fs-14"><span class="text-danger">*</span>Ảnh sản phẩm</div>

                        <div class="__content bg-grey p-3 d-flex gap-2 flex-wrap">
                            <!-- Hình ảnh đã chọn -->
                            <div class="__images position-relative me-2" style="width: 85px; height: 85px;" v-for="(img, i) in preview_images" :key="i">
                                <img :src="img" alt="Product Image" class="img-fluid rounded-1" style="width: 100%; height: 100%; object-fit: cover;">
                                <div @click="remove_image(i)" class="remove-img position-absolute cursor-pointer bg-white" style="z-index: 999; right: -4px; top: -6px; border-radius: 50%; width: 15px; height: 15px;">
                                    <i class="fa-solid fa-circle-xmark text-danger fs-20"></i>
                                </div>
                            </div>

                            <!-- Nút thêm hình -->
                            <label class="__add-image text-center border border-dashed rounded p-3 text-danger d-flex flex-column align-items-center justify-content-center" style="width: 85px; height: 85px; cursor: pointer;">
                                <input type="file" name="images[]" class="d-none" multiple accept="image/*" @change="add_image">
                                <div class="d-flex gap-2 align-items-center"> 
                                    <i class="fas fa-image fa-2x"></i>
                                    <i class="fas fa-plus small"></i>
                                </div>
                                <div class="mt-1 small fw-semibold fs-12 text-nowrap">Thêm hình <br> ảnh ({{ preview_images.length }}/8)</div>
                            </label>

                            <p v-if="errors.images" class="text-danger small mt-2">{{ errors.images }}</p>
                        </div>

                    </div>

                    <div class="__product-video mt-3">
                        <div class="__title fs-14"><span class="text-danger">*</span>Video sản phẩm</div>

                        <div class="__content bg-grey p-3">
                            <div class="d-flex gap-2 flex-wrap">
                            <!-- Video đã chọn -->
                            <div v-if="preview_video" class="position-relative me-2" style="width: 85px; height: 85px;">
                                <video :src="preview_video" class="img-fluid rounded-1" controls
                                    style="width: 100%; height: 100%; object-fit: cover;"></video>
                            </div>

                            <label class="text-center border border-dashed rounded p-3 text-primary d-flex flex-column align-items-center justify-content-center" style="width: 85px; height: 85px; cursor: pointer;">
                                <input type="file" name="video" class="d-none" accept="video/*" @change="add_video">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-video fa-2x"></i> &nbsp;
                                    <i class="fas fa-plus small"></i>
                                </div>
                                <div class="mt-1 small fw-semibold fs-12 text-nowrap">Thêm video</div>
                            </label>

                            <div class="__notice fs-14 text-muted small ms-3 mt-2">
                                • Kích thước tối đa 30Mb, độ phân giải không vượt quá 1280x1280px<br>
                                • Độ dài: 10s-60s<br>
                                • Định dạng: MP4<br>
                                • Lưu ý: Sản phẩm có thể hiển thị trong khi video đang được xử lý. Video sẽ tự động hiển thị sau
                                khi đã xử lý thành công<br>
                            </div>
                        </div>
                        <p v-if="errors.video" class="text-danger small mt-2">{{ errors.video }}</p>
                        </div>                        
                    </div>

                </div>

            </div>

            <div class="__sale-info px-4 bg-white mt-3 shadow-sm">
                <div class="__title fs-18 fw-semibold pt-3">Thông tin bán hàng</div>
                                
                <div class="product-classification mt-16">
                    <div class="product-classification__title fs-16 fw-semibold"><span class="text-danger">*</span> Phân loại hàng</div>

                    <div class="product-classification__content bg-grey mt-2"
                        v-if="classification.length"
                        v-for="(group, i) in classification"
                        :key="i">

                        <!-- Phân loại -->
                        <div class="d-flex align-items-center px-4 py-3">
                            <div class="count-classification fs-14 col-2">Phân loại {{ i + 1 }}</div>
                            <div class="classification box-input-select">
                                <div class="d-flex align-items-center gap-2">
                                    <input class="input-select" type="text" placeholder="Type or Select"
                                        :value="group.attribute_name" readonly />
                                    <i class="fa-solid fa-trash-can text-grey fs-16 cursor-pointer"
                                    @click="remove_attribute(i)"></i>
                                </div>

                                <div class="select-content bg-white py-2 mt-2 shadow-sm">
                                    <div class="content__title fs-12 text-grey ms-2">Chọn thuộc tính</div>
                                    <div class="content__items mt-1">
                                        <template v-for="a in attributes" :key="a.id">
                                            <div class="content__item"
                                                v-if="!classification.map(g => g.attribute_id).includes(a.id)"
                                                @mousedown="on_select_attribute(a.id, i)">
                                                {{ a.name }}
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tuỳ chọn giá trị -->
                        <div class="d-flex px-4 py-3">
                            <div class="classification-optional fs-14 col-2">Tuỳ chọn</div>
                            <div class="classification d-flex flex-wrap gap-2">
                                <div class="box-input-select box-input-select__item me-4"
                                    v-for="(value, valIndex) in group.attribute_value_objects"
                                    :key="valIndex">
                                    <div class="d-flex align-items-center gap-2">
                                        <input class="input-select" type="text" placeholder="Nhập"
                                            :value="value.value" readonly />
                                        <i class="fa-solid fa-trash-can text-grey fs-16 cursor-pointer"
                                        @click="remove_attribute_value_input(i, valIndex)"></i>
                                    </div>

                                    <div class="select-content bg-white py-2 mt-2 shadow-sm">
                                        <div class="content__title fs-12 text-grey ms-2">Giá trị đề xuất</div>
                                        <div class="content__items mt-1">
                                            <template v-for="v in group.attribute_values" :key="v.id">
                                                <div class="content__item"
                                                    v-if="!group.attribute_value_objects.map(val => val.id).includes(v.id)"
                                                    @mousedown="on_select_attribute_value(i, v.id)">
                                                    {{ v.value }}
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="product-classification__content bg-grey mt-16 p-3">
                        <button type="button"
                            class="add-classification-btn fs-14 fw-500 d-flex align-items-center py-1 px-4"
                            @click="add_classification">
                            <i class="fas fa-plus me-2"></i>
                            Thêm nhóm phân loại
                        </button>
                    </div>

                    <p v-if="errors.attributes" class="text-danger small mt-2">{{ errors.attributes }}</p>
                    </div>

                <div class="__classification-list mt-16">
                    <div class="__title fs-14"> <span class="text-danger">*</span> Dach sách phân loại hàng</div>

                    <div class="__shortcut d-flex fs-14 gap-3 mb-32">
                        <div class="__left d-flex">
                            <span class="border px-1 d-flex align-items-center" style="border-radius: 3px 0 0 3px; height: 30px;">đ</span>
                            <input type="text" name="" id="" placeholder="Giá" class="outline-none border px-1" style="border-left: none !important; height: 30px;" v-model="apply_data.price">
                            <input type="text" name="" id="" placeholder="Kho hàng" class="outline-none border px-1" style="border-left: none !important; height: 30px;" v-model="apply_data.stock">
                            <input type="text" name="" id="" placeholder="SKU phân loại" class="outline-none border px-1" style="border-left: none !important; border-radius: 0 3px 3px 0; height: 30px;" v-model="apply_data.sku">
                        </div>

                        <div class="__right w-100" @click="apply_all">
                            <button class="w-100 fs-14 d-flex align-items-center justify-content-center main-btn" style="height: 30px; border-radius: 2px; min-width: 400px;">Áp dụng cho tất cả biến thể</button>
                        </div>
                    </div>  

                    <div class="__manual">
                        <p v-if="errors.variants" class="text-danger small mt-2">{{ errors.variants }}</p>
                        <div class="table-responsive table-bordered">
                            <table class="table fs-14">
                            <thead class="table-light" style="height: 60px;">
                                <tr class="fw-500 align-middle text-center">
                                    <td scope="col" class="border-end">Ảnh</td>

                                    <!-- Tên các thuộc tính (phân loại) -->
                                    <td scope="col"
                                        class="border-end"
                                        v-for="(attr, index) in classification"
                                        :key="index">
                                    {{ attr.attribute_name }}
                                    </td>

                                    <td scope="col" class="border-end">Giá</td>
                                    <td scope="col" class="border-end">Kho hàng</td>
                                    <td scope="col" class="border-end">SKU phân loại</td>
                                    <!-- <td scope="col" class="border-end">Chức năng</td> -->
                                </tr>
                            </thead>

                            <tbody style="height: 100px;">
                            <tr class="text-center align-middle" v-for="(variant, index) in variants" :key="index">
                                <!-- Ảnh biến thể -->
                                <td class="border-end" style="min-width: 130px;">
                                    <img v-if="variant.preview" :src="variant.preview" style="max-height: 50px;" @click="open_modal_variant_image(index)"/>

                                    <div class="__img-add p-2"
                                        style="position: relative; display: inline-block; cursor: pointer; border: 1px dashed grey; border-radius: 3px;"
                                        v-else
                                        @click="open_modal_variant_image(index)">
                                        <i class="fa-solid fa-image" style="font-size: 30px; color: #888;"></i>
                                    </div>
                                </td>

                                <!-- Các giá trị phân loại -->
                                <td class="border-end fw-semibold"
                                v-for="(attr, attr_index) in variant.attributes"
                                :key="attr_index"
                                style="min-width: 230px;"
                                >
                                {{ attr.value_name }}
                                </td>

                                <!-- Nhập giá -->
                                <td class="border-end" style="min-width: 250px;">
                                    <div class="d-flex justify-content-center">
                                        <span class="border px-2 d-flex align-items-center fw-500"
                                            style="border-radius: 3px 0 0 3px; height: 30px;">đ</span>
                                        <input type="text"
                                            v-model="variant.price"
                                            placeholder="Nhập giá"
                                            class="outline-none border px-1 fw-500"
                                            style="border-left: none !important; height: 30px; border-radius: 0 3px 3px 0;">
                                    </div>
                                </td>

                                <!-- Nhập kho -->
                                <td class="border-end" style="min-width: 250px;">
                                    <input type="text"
                                        v-model="variant.stock"
                                        placeholder="Nhập kho hàng"
                                        class="outline-none border px-1 fw-500"
                                        style="height: 30px; border-radius: 3px;">
                                </td>

                                 <!-- Nhập SKU -->
                                <td class="border-end" style="min-width: 260px;">
                                    <input type="text"
                                        v-model="variant.sku"
                                        placeholder="SKU"
                                        class="outline-none border px-1 fw-500"
                                        style="height: 30px; border-radius: 3px;">
                                </td>

                                <!-- Nút xoá biến thể -->
                                <!-- <td class="border-end">
                                    <button class="btn btn-danger fs-14" style="height: 30px; border-radius: 3px;"
                                        @click="variants.splice(index, 1)">Xoá</button> 
                                </td> -->
                            </tr>
                            </tbody>
                        </table>

                        </div>
                        
                    </div>
                    
                </div>  
            </div>

            <div class="__transport px-4 bg-white mt-3 shadow-sm">
                <div class="__title fs-18 fw-semibold pt-3">Vận chuyển</div>

                <div class="__content mt-4">
                    <div class="__weight">
                    <div class="__title fs-14"><span class="text-danger">*</span>Cân nặng (sau khi đóng gói)</div>
                    <input type="text" name="" id="" placeholder="Gram" class="w-100 mt-1 py-1 px-3 fs-14 border" style="height: 40px; border-radius: 3px;" v-model="product.weight">
                    </div>

                    <div class="__size mt-16 pb-16">
                    <div class="__title fs-14"><span class="text-danger">*</span>Kích thước đóng gói</div>
                    <div class="d-flex gap-3">
                        <input type="text" name="" id="" placeholder="Dài(cm)" class="w-100 mt-1 py-1 px-3 fs-14 border" style="height: 40px; border-radius: 3px;" v-model="product.length">
                        <input type="text" name="" id="" placeholder="Rộng(cm)" class="w-100 mt-1 py-1 px-3 fs-14 border" style="height: 40px; border-radius: 3px;" v-model="product.width">
                        <input type="text" name="" id="" placeholder="Cao(cm)" class="w-100 mt-1 py-1 px-3 fs-14 border" style="height: 40px; border-radius: 3px;" v-model="product.height">
                    </div>
                    <p v-if="errors.height" class="text-danger small mt-2">{{ errors.height }}</p>
                    <p v-if="errors.length" class="text-danger small mt-2">{{ errors.length }}</p>
                    <p v-if="errors.weight" class="text-danger small mt-2">{{ errors.weight }}</p>
                    <p v-if="errors.width" class="text-danger small mt-2">{{ errors.width }}</p>
                    </div>
                </div>
                
            </div>  

            <div class="__submit px-4 py-3 bg-white my-3 shadow-sm d-flex justify-content-end">
                <button class="border fs-14 fw-500 py-2 px-3 me-2">Huỷ</button>
                <button class="main-btn fs-14 fw-500 p-2" @click="submit_product">Thêm sản phẩm</button>
            </div>
        </div>
        </div>

    </div>

    <div class="modal-background modal_categories container-fluid p-0 d-flex justify-content-center align-items-center"
        v-if="is_modal_active">
        <div class="modal-choose-category modal-ezeshop bg-white shadow-sm px-4 pt-4 pb-2"
            style="height: 80%; width: 80%;">
            <div class="modal-ezeshop__top d-flex justify-content-between align-items-center">
                <div class="modal-ezeshop__name fs-18 fw-500">Chỉnh sửa ngành hàng</div>
                <div class="modal-ezeshop__cancel cursor-pointer" @click="close_modal"><i
                        class="fa-solid fa-xmark fs-18"></i></div>
            </div>

            <div class="modal-ezeshop__main w-100 bg-grey p-2 overflow-x-auto" style="width: 800px;">
                <div class="main__top d-flex justify-content-between align-items-center">
                    <div class="top__search col-3 border py-1 pe-2">
                        <div class="d-flex">
                            <input type="text" class="px-2 w-100 border-0 outline-none"
                                placeholder="Vui lòng nhập tối thiểu 1 kí tự!">
                            <div class="top__btn-search" type="button"><i
                                    class="fa-solid fa-magnifying-glass fs-14 text-grey"></i></div>
                        </div>
                    </div>

                    <div class="top__guide--select-category fs-14">
                        <span>Chọn ngành hàng chính xác</span>
                        <span class="text-primary mx-2 cursor-pointer">Bấm vào đây!</span>
                    </div>
                </div>

                <div class="main__content bg-white mt-4 d-flex overflow-x-auto">
                    <div class="__wrapper d-flex">
                        <div class="content__categories d-flex flex-column col-3 pt-3 px-3 overflow-y-auto border-end"
                            style="height: 350px;">
                            <!-- <div class="category__item d-flex justify-content-between py-2 align-items-center cursor-pointer"
                                :class="{ 'active': category_step >= 1 }" v-for="c in categories_level_1" :key="c.id"
                                @click="on_select_level_1(c.id)">{{ c.name }}
                                <i class="fa-solid fa-caret-right"></i>
                            </div> -->

                            <div class="category__item d-flex justify-content-between py-2 align-items-center cursor-pointer"
                                :class="{ 'active': selected_level_1_id === c.id }"
                                v-for="c in categories_level_1" :key="c.id"
                                @click="on_select_level_1(c.id)">
                                {{ c.name }}
                                <i class="fa-solid fa-caret-right"></i>
                            </div>

                        </div>

                        <div class="content__categories d-flex flex-column col-3 pt-3 px-3 overflow-y-auto border-end"
                            style="height: 350px;">
                            <!-- <div class="category__item d-flex justify-content-between py-2 align-items-center cursor-pointer"
                                :class="{ 'active': category_step >= 2 }" v-for="c in categories_level_2" :key="c.id"
                                @click="on_select_level_2(c.id)">{{ c.name
                                }}
                                <i class="fa-solid fa-caret-right"></i>
                            </div> -->

                            <div class="category__item d-flex justify-content-between py-2 align-items-center cursor-pointer"
                                :class="{ 'active': selected_level_2_id === c.id }"
                                v-for="c in categories_level_2" :key="c.id"
                                @click="on_select_level_2(c.id)">
                                {{ c.name }}
                                <i class="fa-solid fa-caret-right"></i>
                            </div>

                        </div>

                        <div class="content__categories d-flex flex-column col-3 pt-3 px-3 overflow-y-auto border-end"
                            style="height: 350px;">
                            <!-- <div class="category__item d-flex justify-content-between py-2 align-items-center cursor-pointer"
                                v-for="c in categories_level_3" :key="c.id" @click="on_select_level_3(c.id)">{{ c.name
                                }}
                                <i class="fa-solid fa-caret-right"></i>
                            </div> -->

                            <div class="category__item d-flex justify-content-between py-2 align-items-center cursor-pointer"
                                :class="{ 'active': selected_level_3_id === c.id }"
                                v-for="c in categories_level_3" :key="c.id"
                                @click="on_select_level_3(c.id)">
                                {{ c.name }}
                                <i class="fa-solid fa-caret-right"></i>
                            </div>

                        </div>

                        <div class="content__categories d-flex flex-column col-3 pt-3 px-3 overflow-y-auto border-end"
                            style="height: 350px;">
                            <!-- <div class="category__item d-flex justify-content-between py-2 align-items-center cursor-pointer"
                                v-for="c in categories_level_4" :key="c.id" @click="on_select_level_4(c.id)">{{ c.name
                                }}
                                <i class="fa-solid fa-caret-right"></i>
                            </div> -->

                            <div class="category__item d-flex justify-content-between py-2 align-items-center cursor-pointer"
                                :class="{ 'active': selected_level_4_id === c.id }"
                                v-for="c in categories_level_4" :key="c.id"
                                @click="on_select_level_4(c.id)">
                                {{ c.name }}
                                <i class="fa-solid fa-caret-right"></i>
                            </div>

                        </div>

                    </div>
                </div>

            </div>

            <div class="modal-ezeshop__bottom d-flex justify-content-between w-100 align-items-center border-top">

                <div class="selected_category d-flex">

                </div>

                <div class="modal-ezeshop__buttons d-flex">
                    <div class="modal-ezeshop__cancel me-4"><button class="white-btn py-2 px-4 fs-14"
                            @click="close_modal">Huỷ</button></div>
                    <div class="modal-ezeshop__save"><button class="main-btn py-2 px-4 fs-14" form="form-edit-address"
                            type="submit" @click="save_category_id">Lưu</button></div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-background modal_imgs container-fluid d-flex justify-content-center align-items-center"
        v-if="is_modal_variant_image">
        <div class="modal-edit-address modal-ezeshop bg-white shadow-sm px-4 pt-4 pb-2">
            <div class="modal-ezeshop__top d-flex justify-content-between align-items-center">
                <div class="modal-ezeshop__name fs-18 fw-500">Chọn ảnh biến thể</div>
                <div class="modal-ezeshop__cancel cursor-pointer" @click="close_modal_variant_image"><i
                        class="fa-solid fa-xmark fs-18"></i></div>
            </div>

            <div class="modal-ezeshop__main w-100 overflow-y-auto">
                <div class=" " style="height: 500px;">
                    <div class="d-flex gap-2 flex-wrap mt-5">
                        <!-- Hình ảnh đã chọn -->
                        <div class="position-relative me-2" style="width: 120px; height: 120px;"
                            v-for="(img, i) in preview_images" :key="i">
                            <img :src="img" alt="Product Image" class="img-fluid rounded-1"
                                style="width: 100%; height: 100%; object-fit: cover;"
                                @click="select_existing_image(img)">
                        </div>

                        <!-- Nút thêm hình -->
                        <label
                            class="text-center border border-dashed rounded p-3 text-danger d-flex flex-column align-items-center justify-content-center"
                            style="width: 120px; height: 120px; cursor: pointer;">
                            <input type="file" name="image" class="d-none" multiple accept="image/*"
                                @change="upload_variant_image($event, i)">

                            <div>
                                <i class="fas fa-image fa-2x"></i> &nbsp;
                                <span class="position-relative" style="top: -10px; left: -5px;">
                                    <i class="fas fa-plus small"></i>
                                </span>
                            </div>
                            <div class="mt-1 small fw-semibold">Thêm ảnh<br> biến thể</div>
                        </label>
                    </div>
                </div>
            </div>


            <div class="modal-ezeshop__bottom d-flex justify-content-end w-100 align-items-center border-top">
                <div class="modal-ezeshop__cancel me-4"><button class="white-btn py-2 px-4 fs-14"
                        @click="close_modal_variant_image">Huỷ</button></div>
                <div class="modal-ezeshop__save"><button class="main-btn py-2 px-4 fs-14" form="form-edit-address"
                        type="button" @click="">Lưu</button></div>
            </div>
        </div>
    </div>
    <confirm__popup ref="confirm_popup" />
</template>

<script setup>
import { onMounted, ref, reactive, watch, computed } from 'vue';
import api from "@/configs/api"

import { QuillEditor } from '@vueup/vue-quill'
import message from '@/utils/messageState';
import '@vueup/vue-quill/dist/vue-quill.snow.css'
import { useRouter } from 'vue-router'
import confirm__popup from '@/components/confirm.vue';

const confirm_popup = ref(null);
const router = useRouter()

const shop_id = ref(null)
//Handle modal
const is_modal_active = ref(false)
const open_modal = () => is_modal_active.value = true
const close_modal = () => is_modal_active.value = false

watch(is_modal_active, (visible) => {
    document.body.style.overflow = visible ? 'hidden' : 'auto'
})

//Hanlde imgs
const preview_images = ref([])
const file_images = ref([])

const add_image = (e) => {
    const selected = Array.from(e.target.files)

    const limit = 8 - preview_images.value.length
    const images = selected.slice(0, limit)

    images.forEach((i) => {
        const reader = new FileReader()
        reader.onload = (e) => {
            const base64 = e.target.result
            const is_duplicate = preview_images.value.includes(base64)
            if (is_duplicate) return message.emit("show-message", { title: "Thông báo", text: 'Ảnh đã tồn tại!!!.', type: "error" })

            preview_images.value.push(base64)
            file_images.value.push(i)
        }
        reader.readAsDataURL(i)
    })

    e.target.value = ''
}

const remove_image = (i) => {
    preview_images.value.splice(i, 1)
    file_images.value.splice(i, 1)
}

//Handle video
const preview_video = ref(null)
const file_video = ref(null)

const add_video = (e) => {
    const file = e.target.files[0]
    if (!file) return

    const reader = new FileReader()
    reader.onload = (e) => {
        preview_video.value = e.target.result
        file_video.value = file
    }
    reader.readAsDataURL(file)

    e.target.value = ''
}

//Handle product
const categories = ref([])
const categories_level_1 = ref([])
const categories_level_2 = ref([])
const categories_level_3 = ref([])
const categories_level_4 = ref([])
const category_step = ref(0)

const selected_level_1_id = ref(null)
const selected_level_2_id = ref(null)
const selected_level_3_id = ref(null)
const selected_level_4_id = ref(null)

const selected_category = ref(null)

const get_categories = async () => {
    const res = await api.get('/categories')
    categories.value = res.data.categories

    categories_level_1.value = categories.value.filter(c => !c.parent_id)
}

const is_leaf_category = (cate_id) => {
    return !categories.value.some(c => c.parent_id === cate_id)
}

const on_select_level_1 = (cate_id) => {
    selected_level_1_id.value = cate_id
    selected_level_2_id.value = null
    selected_level_3_id.value = null
    selected_level_4_id.value = null

    categories_level_2.value = categories.value.filter(c => c.parent_id === cate_id) || []
    categories_level_3.value = []
    categories_level_4.value = []
    category_step.value = 1

    if (is_leaf_category(cate_id)) {
        selected_category.value = cate_id
    } else {
        selected_category.value = null
    }
}


const on_select_level_2 = (cate_id) => {
    selected_level_2_id.value = cate_id
    selected_level_3_id.value = null
    selected_level_4_id.value = null

    categories_level_3.value = categories.value.filter(c => c.parent_id === cate_id) || []
    categories_level_4.value = []
    category_step.value = 2

    if (is_leaf_category(cate_id)) {
        selected_category.value = cate_id
    } else {
        selected_category.value = null
    }
}


const on_select_level_3 = (cate_id) => {
    selected_level_3_id.value = cate_id
    selected_level_4_id.value = null

    categories_level_4.value = categories.value.filter(c => c.parent_id === cate_id) || []
    category_step.value = 3

    if (is_leaf_category(cate_id)) {
        selected_category.value = cate_id
    } else {
        selected_category.value = null
    }
}


const on_select_level_4 = (cate_id) => {
    selected_level_4_id.value = cate_id
    category_step.value = 4
    selected_category.value = cate_id
}


const save_category_id = () => {
    if (!selected_category.value) {
        message.emit("show-message", { title: "Lỗi", text: 'Vui lòng chọn ngành hàng hợp lệ.', type: "warning" });
        return;
    }
    message.emit("show-message", { title: "Thông báo", text: 'Chọn ngành hàng thành công.', type: "success" });
    close_modal()
}

const product = ref({
    category_id: '',
    name: '',
    description: '',
    weight: '',
    length: '',
    width: '',
    height: ''
})

//AI generate description
const generate_description = async (product_name)=>{
    if(!product_name) return message.emit("show-message", {title: "Thông báo", text: 'Vui lòng nhập tên sản phẩm!', type: "error"})
        
    product.value.description = '<em>⏳ Đang tạo mô tả...</em>'

    try {
        const res = await api.post('/ai/generate-description', {
            'name': product_name
        })

        const html = String(res.data.description || '<p></p>');

        console.log('Generated HTML:', html);


        product.value.description = html
    } catch (error) {
        product.value.description = '<span style="color: red;">⚠️ Lỗi khi tạo mô tả</span>'
        console.log(error);
        
    }finally{}

    
}

// generate sku
const slugify = (str) => {
  return str
    .replace(/Đ/g, "D")
    .replace(/đ/g, "d")
    .normalize("NFD")               // Tách dấu
    .replace(/[\u0300-\u036f]/g, "")// Xoá dấu
    .replace(/[^a-zA-Z0-9]+/g, "-") // Thay thế khoảng trắng và ký tự đặc biệt bằng dấu "-"
    .replace(/^-+|-+$/g, "")        // Xoá dấu "-" ở đầu/cuối
    .toUpperCase();                 // Viết hoa nếu muốn SKU viết hoa
};

const generate_sku = (product_name, attribute_values) => {
  const base = slugify(product_name);
  const attrPart = attribute_values.map(v => slugify(v.value_name)).join("-");
  return `${base}-${attrPart}`;
};

const apply_data = ref({
  price: '',
  stock: '',
  sku: ''
});

const apply_all = () => {

  variants.value = variants.value.map((variant, index) => {
    const attrs = Array.isArray(variant.attributes) ? variant.attributes : [];

    const updated = {
      ...variant,
      price: apply_data.value.price !== '' ? apply_data.value.price : variant.price,
      stock: apply_data.value.stock !== '' ? apply_data.value.stock : variant.stock,
      sku: apply_data.value.sku ? apply_data.value.sku : generate_sku(product.value.name, attrs),
    };

    return updated;
  });
};

//Handle Attributes
const attributes = ref([]);
const classification = ref([]);

const createValueObj = (id = null, value = 'Giá trị') => ({ id, value });

const add_classification = () => {
    classification.value.push({
        attribute_id: null,
        attribute_name: 'Thuộc tính',
        attribute_values: [],
        attribute_value_objects: [createValueObj()]
    });
};

const on_select_attribute = async (attribute_id, index) => {
    const attr = attributes.value.find(a => a.id === attribute_id);
    if (!attr) return;

    try {
        const { data } = await api.get(`/attribute_values/${attribute_id}`);
        const values = data.attribute_values || [];

        classification.value[index] = {
            attribute_id: attr.id,
            attribute_name: attr.name,
            attribute_values: values,
            attribute_value_objects: [createValueObj()]
        };
    } catch (e) {
        console.error("Failed to fetch attribute values:", e);
    }
};

const on_select_attribute_value = (group_index, value_id) => {
    const group = classification.value[group_index];
    const inputs = group.attribute_value_objects;

    const last_index = inputs.length - 1;
    const value_obj = group.attribute_values.find(v => v.id === value_id);
    if (!value_obj) return;

    // Cập nhật vào ô cuối
    inputs[last_index] = {
        id: value_id,
        value: value_obj.value
    };

    // Loại trùng ID đã chọn
    const selected_ids = inputs.map(v => v?.id).filter(Boolean);
    const all_ids = group.attribute_values.map(v => v.id);
    const remaining = all_ids.filter(id => !selected_ids.includes(id));

    // Chỉ thêm ô mới nếu:
    // - còn giá trị chưa chọn
    // - và chưa có ô nào là null
    const hasNull = inputs.some(v => v?.id === null);
    if (remaining.length > 0 && !hasNull) {
        inputs.push({ id: null, value: 'Giá trị' });
    }
};

const remove_attribute_value_input = (group_index, index) => {
    const group = classification.value[group_index];
    group.attribute_value_objects.splice(index, 1);

    const selected_ids = group.attribute_value_objects.map(v => v?.id).filter(Boolean);
    const all_ids = group.attribute_values.map(v => v.id);
    const remaining = all_ids.filter(id => !selected_ids.includes(id));

    const hasNull = group.attribute_value_objects.some(v => v?.id === null);
    if (remaining.length > 0 && !hasNull) {
        group.attribute_value_objects.push({ id: null, value: 'Giá trị' });
    }
};

const remove_attribute = (group_index) => {
    classification.value.splice(group_index, 1);
};

//Hanlde Variants
const variants = ref([
    {
        attribute_value: [],
        preview: '',
        image: null,
        price: '',
        stock: '',
        sku: ''
    }
])

const generate_variants = () => {
    const old_variants = [...variants.value]; // Lưu các biến thể cũ

    // Lấy ra các nhóm giá trị đã chọn (loại bỏ null)
    const value_groups = classification.value.map(g =>
        g.attribute_value_objects.filter(v => v && v.id !== null)
    );

    const hasEmpty = value_groups.some(group => group.length === 0);

    // Hàm đệ quy sinh tổ hợp
    const generate = (arr, prefix = []) => {
        if (!arr.length) return [prefix];
        const [head, ...tail] = arr;
        const result = [];
        head.forEach(val => {
            result.push(...generate(tail, [...prefix, val]));
        });
        return result;
    };

    let combinations = [];

    // Nếu đã chọn đầy đủ → sinh tổ hợp thực sự
    if (!hasEmpty) {
        combinations = generate(value_groups);
    } else {
        // Tổ hợp đầy đủ, nhưng nhóm trống thì gán 1 giá trị "Giá trị"
        const groups = value_groups.map(group => group.length ? group : [{ id: null, value: 'Giá trị' }]);
        combinations = generate(groups);
    }

    variants.value = combinations.map(comb => {
        const attributes = comb.map((v, i) => ({
            attribute_id: classification.value[i].attribute_id,
            attribute_name: classification.value[i].attribute_name,
            value_id: v.id,
            value_name: v.value
        }));

        const old = old_variants.find(v =>
            JSON.stringify(v.attributes?.map(a => ({ attribute_id: a.attribute_id, value_id: a.value_id }))) ===
            JSON.stringify(attributes.map(a => ({ attribute_id: a.attribute_id, value_id: a.value_id })))
        );

        

        return {
            attributes,
            preview: old?.preview || '',
            image: old?.image || null,
            price: old?.price || '',
            stock: old?.stock || '',
            sku: old?.sku || generate_sku(product.value.name, attributes) 
        };
    });
};


watch(classification, () => {
  generate_variants()
//   console.log('Classification changed:', classification.value);
//   console.log('Generated variants:', variants.value);
}, { immediate: true, deep: true })

//Handle modal variant image
const selected_variant_index = ref(null)

const is_modal_variant_image = ref(false)
const open_modal_variant_image = (index) => {
    selected_variant_index.value = index
    is_modal_variant_image.value = true
}

const close_modal_variant_image = () => {
    is_modal_variant_image.value = false
}

const select_existing_image = (img) => {
    const i = selected_variant_index.value
    if (i !== null && variants.value[i]) {
        variants.value[i].preview = img
        const idx = preview_images.value.findIndex(u => u === img)
        console.log(idx);

        variants.value[i].image = file_images.value[idx]

        file_images.value.splice(idx, 1)
        preview_images.value.splice(idx, 1)
    }

    close_modal_variant_image()
    return message.emit("show-message", {
        title: "Thông báo",
        text: 'Thêm ảnh thành công!',
        type: "success"
    })
}

const upload_variant_image = (e) => {
    const file = e.target.files[0]
    if (!file) return

    const reader = new FileReader()
    reader.onload = (event) => {
        const base64 = event.target.result

        if (preview_images.value.includes(base64)) {
            return message.emit("show-message", {
                title: "Thông báo",
                text: 'Ảnh đã tồn tại!',
                type: "warning"
            })
        }

        const i = selected_variant_index.value
        if (i !== null && variants.value[i]) {
            variants.value[i].preview = base64
            variants.value[i].image = file
        }

        message.emit("show-message", {
            title: "Thông báo",
            text: 'Lưu ảnh thành công!',
            type: "success"
        })
        close_modal_variant_image()
    }
    reader.readAsDataURL(file)
    e.target.value = ''
}

const submit_product = async () => {
    const confirm = await confirm_popup.value.open_confirm("Thêm sản phẩm này?");
    if (!confirm) return;

    if (!validateProduct()) return;

    const formData = new FormData();

    formData.append('shop_id', shop_id.value);
    formData.append('product_name', product.value.name);
    formData.append('category_id', selected_category.value);
    formData.append('description', product.value.description);
    formData.append('weight', product.value.weight);
    formData.append('length', product.value.length);
    formData.append('width', product.value.width);
    formData.append('height', product.value.height);

    // Ảnh chính
    file_images.value.forEach((image, index) => {
        formData.append(`file_images[${index}]`, image);
    });

    // Video
    if (file_video.value) {
        formData.append('file_video', file_video.value);
    }

    // Dữ liệu biến thể (gửi text)
    const variant_data = variants.value.map(variant => ({
        attributes: variant.attributes, // chứa attribute_id, name, value_id, value_name
        price: variant.price,
        stock: variant.stock,
        sku: variant.sku ?? ''
    }));
    formData.append('variants', JSON.stringify(variant_data));

    // Ảnh riêng từng biến thể
    variants.value.forEach((variant, index) => {
        if (variant.image) {
            formData.append(`variant_images[${index}]`, variant.image);
        }
    });

    try {
        // console.log('--- Dữ liệu FormData ---');
        // for (let [key, value] of formData.entries()) {
        //     console.log(`${key}:`, value);
        // }

        const res = await api.post('/product', formData);
        
        if (res.status === 200) {
            message.emit("show-message", { title: "Thông báo", text: "Thêm sản phẩm thành công.", type: "success" });
            
            product.value = {
                category_id: '',
                name: '',
                description: '',
                weight: '',
                length: '',
                width: '',
                height: ''
            };

            preview_images.value = [];
            file_images.value = [];

            preview_video.value = null;
            file_video.value = null;

            selected_level_1_id.value = null;
            selected_level_2_id.value = null;
            selected_level_3_id.value = null;
            selected_level_4_id.value = null;
            selected_category.value = null;

            categories_level_2.value = [];
            categories_level_3.value = [];
            categories_level_4.value = [];
            category_step.value = 0;

            classification.value = [];
            variants.value = [];

            apply_data.value = {
                price: '',
                stock: '',
                sku: ''
            };

            router.push('/kenh-nguoi-ban/san-pham');
        }
    } catch (error) {
        console.error('Lỗi khi gửi sản phẩm:', error);
    }
};

//Errors
const errors = ref({
    images: '',
    video: '',
    product_name: '',
    category: '',
    description: '',
    attributes: '',
    variants: '',

})

const validateProduct = () => {
    errors.value = {
        images: '',
        video: '',
        product_name: '',
        category: '',
        description: '',
        attributes: '',
        variants: '',
        height: '',
        weight: '',
        width: '',
        length:'',
    };

    let isValid = true;

    // Kiểm tra ảnh
    if (preview_images.value.length < 1) {
        errors.value.images = 'Vui lòng chọn ít nhất 1 ảnh sản phẩm!';
        isValid = false;
    }

    // Kiểm tra video
    if (file_video.value) {
        const maxSizeMB = 10;
        if (file_video.value.size / 1024 / 1024 > maxSizeMB) {
            errors.value.video = `Video không được lớn hơn ${maxSizeMB}MB!`;
            isValid = false;
        }
    }

    // Tên sản phẩm
    if (!product.value.name.trim()) {
        errors.value.product_name = 'Vui lòng nhập tên sản phẩm!';
        isValid = false;
    } else if (product.value.name.trim().length < 3) {
        errors.value.product_name = 'Tên sản phẩm phải từ 3 ký tự trở lên!';
        isValid = false;
    }

    // Danh mục
    if (!selected_category.value) {
        errors.value.category = 'Vui lòng chọn ngành hàng!';
        isValid = false;
    }

    // Mô tả
    if (!product.value.description.trim()) {
        errors.value.description = 'Vui lòng nhập mô tả sản phẩm!';
        isValid = false;
    } else if (product.value.description.trim().length < 10) {
        errors.value.description = 'Mô tả quá ngắn (tối thiểu 10 ký tự)!';
        isValid = false;
    }

    // Kích thước vật lý
    const dimensionFields = ['weight', 'length', 'width', 'height'];
    for (let field of dimensionFields) {
        const value = product.value[field];
        if (value === '' || isNaN(value) || Number(value) <= 0) {
            errors.value[field] = `Trường "${field}" không hợp lệ (bắt buộc, số dương)!`;
            isValid = false;
        }
    }

    // Thuộc tính
    if (classification.value.length === 0) {
        errors.value.attributes = 'Vui lòng thêm ít nhất 1 thuộc tính!';
        isValid = false;
    } else {
        for (let i = 0; i < classification.value.length; i++) {
            const group = classification.value[i];
            if (!group.attribute_id) {
                errors.value.attributes = `Vui lòng chọn tên cho nhóm thuộc tính ${i + 1}!`;
                isValid = false;
                break;
            }
            const selected = group.attribute_value_objects.filter(v => v && v.id !== null);
            if (selected.length === 0) {
                errors.value.attributes = `Vui lòng chọn ít nhất một giá trị cho "${group.attribute_name}"!`;
                isValid = false;
                break;
            }
        }
    }

    // Biến thể
    if (variants.value.length === 0) {
        errors.value.variants = 'Không có biến thể nào được tạo!';
        isValid = false;
    } else {
        for (let i = 0; i < variants.value.length; i++) {
            const v = variants.value[i];
            if (!v.price || isNaN(v.price) || Number(v.price) <= 0) {
                errors.value.variants = `Biến thể ${i + 1}: giá không hợp lệ!`;
                isValid = false;
                break;
            }
            if (!v.stock || isNaN(v.stock) || Number(v.stock) < 0) {
                errors.value.variants = `Biến thể ${i + 1}: kho hàng không hợp lệ!`;
                isValid = false;
                break;
            }
        }

        // Kiểm tra trùng SKU
        const skuSet = new Set();
        for (let i = 0; i < variants.value.length; i++) {
            const sku = variants.value[i].sku;
            if (skuSet.has(sku)) {
                errors.value.variants = `Biến thể ${i + 1}: SKU bị trùng "${sku}"!`;
                isValid = false;
                break;
            }
            skuSet.add(sku);
        }
    }

    return isValid;
};

//OnMounted
onMounted(async () => {
    try {
        const res_attributes = await api.get('/attributes');
        attributes.value = res_attributes.data.attributes

        const res_shop_id = await api.get('/shop')
        shop_id.value = res_shop_id.data.shop_id

        if (!res_shop_id.data.shop_id) {
            router.push('/kenh-nguoi-ban/dang-ky')
            message.emit("show-message", { title: "Thông báo", text: 'Vui lòng đăng ký shop.', type: "warning" });
        }
    } catch (error) {

    }

})
</script>

<style scoped></style>