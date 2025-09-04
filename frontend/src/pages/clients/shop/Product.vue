<template>
    <div class="__main pb-5">
        <div class="__title mt-3 bg-white px-4 d-flex align-items-center border-bottom"><span
                class="fs-14 fw-semibold text-grey">Sản Phẩm</span></div>

        <div class="__content bg-white px-4 py-2 d-flex align-items-center border-bottom shadow-sm">
            <div class="__product-management w-100">
                <div class="__type d-flex flex-wrap">
                    <div class="__item fs-14 fw-semibold text-grey me-4 my-2"
                        :class="{ active: selected_status === null }" @click="get_products(1, null)">Tất cả</div>

                    <div class="__item fs-14 fw-semibold text-grey me-4 my-2" :class="{ active: selected_status === 1 }"
                        @click="get_products(1, 1)">Đang hoạt động</div>

                    <div class="__item fs-14 fw-semibold text-grey me-4 my-2" :class="{ active: selected_status === 0 }"
                        @click="get_products(1, 0)">Chờ duyệt</div>

                    <div class="__item fs-14 fw-semibold text-grey me-4 my-2" :class="{ active: selected_status === 2 }"
                        @click="get_products(1, 2)">Tạm ngừng</div>

                    <div class="__item fs-14 fw-semibold text-grey me-4 my-2" :class="{ active: selected_status === 3 }"
                        @click="get_products(1, 3)">Từ chối</div>
                </div>

                <div class="__task d-flex flex-wrap mt-3">
                    <div class="__search-box me-3">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" placeholder="Tìm kiếm sản phẩm..." v-model="keyword">
                    </div>

                    <div class="__select me-3">
                        <div class="__selected" @click="sort_drowdown = !sort_drowdown">Mặc định <i
                                class="fa-solid fa-caret-down"></i></div>

                        <div class="__choice bg-white py-2 mt-2 shadow-sm" :class="{ active: sort_drowdown }">
                            <div class="__title">Giá trị đề xuất </div>

                            <div class="__box-item mt-1">
                                <div class="__item" v-for="sort in sorts" @click="select_sort(sort.value)">{{ sort.name
                                    }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="__select">
                        <div class="__selected" @click="cate_dropdown = !cate_dropdown">Danh mục <i
                                class="fa-solid fa-caret-down"></i></div>

                        <div class="__choice bg-white py-2 mt-2 shadow-sm" :class="{ active: cate_dropdown }">
                            <div class="__title">Giá trị đề xuất </div>

                            <div class="__box-item mt-1">
                                <div class="__item" v-for="cate in categories" :key="cate.id"
                                    @mousedown="select_category(cate.id)">{{ cate.name }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="__count fw-500 fs-14 mt-3"> <span>{{ products.length }}</span> sản phẩm</div>
                <div v-if="is_loading"
                    class="d-flex align-items-center justify-content-center p-4 text-center flex-column bg-white"
                    style="min-height: 500px;">
                    <loading__dot />
                    <p class="mt-2">Đang tải dữ liệu đơn hàng...</p>
                </div>
                <div class="__product mt-1" v-else>
                    <no_data v-if="products.length == 0" />
                    <div v-else class="table-responsive table-bordered">
                        <table class="table fs-14">
                            <thead class="table-light">
                                <tr class="fw-500 align-middle">
                                    <td scope="col">Sản phẩm</td>
                                    <td scope="col">Lượt bán</td>
                                    <td scope="col">Giá</td>
                                    <td scope="col">Kho hàng</td>
                                    <td scope="col">Thao tác</td>
                                </tr>
                            </thead>

                            <tbody v-for="product in products" :key="product.id">
                                <tr class="align-middle fw-500">
                                    <td style="min-width: 200px;">
                                        <div class="d-flex">
                                            <img :src="get_main_image(product)" alt="" class="__product-img me-2"
                                                style="max-width: 50px; height: 60px; object-fit: contain;">

                                            <div class="__product-info d-flex flex-column">
                                                <span class="__product-name fw-semibold">{{ product.name }}</span>
                                                <span class="__product-id fs-12 text-grey fw-500 mt-3">ID:
                                                    {{ product.id }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="min-width: 100px;">{{ product.total_sell }}</td>
                                    <td style="min-width: 100px;" class="fw-semibold">{{ get_price_range(product) }}
                                    </td>
                                    <td style="min-width: 100px;">{{ product.total_stock }}</td>
                                    <td style="min-width: 100px;" class="fs-12 fw-semibold"><router-link
                                            :to="`/kenh-nguoi-ban/chinh-sua-san-pham/${product.id}`"><span
                                                class="text-primary">Sửa</span></router-link>|
                                        <span v-if="product.status != 2" class="cursor-pointer fw-semibold text-danger"
                                            @click="update_shutdown_status(product.id)">Tạm
                                            ngưng</span>
                                        <span v-else class="cursor-pointer fw-semibold text-success"
                                        @click="update_pending_status(product.id)">Hoạt động</span>
                                    </td>
                                </tr>

                                <tr v-for="(variant, i) in is_expanded(product.id) ? product.variants : product.variants.slice(0, 1)"
                                    :key="variant.id" class="align-middle fw-500">
                                    <td>
                                        <div class="d-flex">
                                            <img :src="`/imgs/products/${variant.image}`" alt=""
                                                class="__variant-img ms-5"
                                                style="max-width: 40px; height: 40px; object-fit: contain;">
                                            <div class="__variant-info d-flex flex-column">
                                                <span class="__variant-attribute fs-12 fw-semibold">{{
                                                    get_attribute(variant) }}</span>
                                                <span class="__variant-sku fs-12 text-grey fw-500 mt-2">SKU: {{
                                                    variant.sku.slice(0, 15) }}...</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ variant.sell }}</td>
                                    <td>{{ Number(variant.price).toLocaleString('vi-VN') + 'đ' }}</td>
                                    <td>{{ variant.stock }}</td>
                                    <td></td>
                                </tr>

                                <!-- Nút xem thêm / ẩn bớt -->
                                <tr v-if="product.variants.length > 1">
                                    <td colspan="5">
                                        <button class="btn btn-sm text-primary fs-13"
                                            @click="toggle_variants(product.id)">
                                            {{ is_expanded(product.id) ? 'Ẩn bớt' : `Xem thêm (${product.variants.length
                                                - 1}) biến thể` }}
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <!-- Phân trang -->
                        <Pagination :meta="meta" @changePage="get_products" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <confirm__popup ref="confirm_popup" />
</template>

<script setup>
import { onMounted, ref, reactive, watch, computed } from 'vue';
import api from "@/configs/api"
import Pagination from '../../../components/pagination.vue';
import no_data from '../../../components/no_data.vue';
import message from '@/utils/messageState';
import { useRouter } from 'vue-router'
import confirm__popup from '@/components/confirm.vue';
import loading__dot from '@/components/loading/loading__dot.vue'

//variables
const confirm_popup = ref(null);
const is_loading = ref(false);

const products = ref([])
const products_for_sort = ref([])
const categories = ref([])
const selected_status = ref(null)

const expanded_variants = ref({}) //xem thêm biến thể

const cate_dropdown = ref(false)
const sort_drowdown = ref(false)

const meta = ref({}) // phan trang
const keyword = ref('')
const sorts = ref([{ name: 'Cao tới thấp', value: 'desc' }, { name: 'Thấp tới cao', value: 'asc' }])
const selected_category = ref('Tất cả')
const sort_order = ref('default')
//methods

/**
    Products
 */
const get_products = async (page = 1, status = null) => {
    try {
        is_loading.value = true
        const res = await api.get('/product-shop', {
            params: { page, status }
        })
        selected_status.value = status
        products_for_sort.value = res.data.Products.data // dữ liệu gốc
        meta.value = {
            current_page: res.data.Products.current_page,
            last_page: res.data.Products.last_page,
        }

        get_categories()
        filter()
    } catch (error) {
        console.log('loi lay san pham', error)
    } finally {
        is_loading.value = false
    }
}

const get_main_image = (product) => {
    return product.medias[0]?.url ? `/imgs/products/${product.medias[0].url}` : '/imgs/products/default.jpg'
}

const get_price_range = (product) => {
    const prices = product.variants.map(v => v.price)

    const min = Math.min(...prices)
    const max = Math.max(...prices)

    if (min === max) return format_price(min)

    return `${format_price(min)} - ${format_price(max)}`
}

const format_price = (price) => {
    return price.toLocaleString('vi-VN') + 'đ';
}

//sort
const select_category = (cate_id) => {
    selected_category.value = cate_id
    cate_dropdown.value = false
}

const select_sort = (sort) => {
    sort_order.value = sort
    sort_drowdown.value = false
}

const filter = () => {
    let result = [...products_for_sort.value] // luôn từ dữ liệu gốc

    // Lọc theo danh mục
    if (selected_category.value !== 'Tất cả') {
        result = result.filter(p => p.category?.id === selected_category.value)
    }

    // Tìm kiếm theo từ khóa
    if (keyword.value.trim() !== '') {
        const search = keyword.value.trim().toLowerCase()
        result = result.filter(p => p.name.toLowerCase().includes(search))
    }

    // Sắp xếp theo giá
    if (sort_order.value === 'asc') {
        result.sort((a, b) => {
            const aPrice = Math.min(...a.variants.map(v => v.price))
            const bPrice = Math.min(...b.variants.map(v => v.price))
            return aPrice - bPrice
        })
    } else if (sort_order.value === 'desc') {
        result.sort((a, b) => {
            const aPrice = Math.min(...a.variants.map(v => v.price))
            const bPrice = Math.min(...b.variants.map(v => v.price))
            return bPrice - aPrice
        })
    }

    products.value = result
}

const update_shutdown_status = async (product_id) => {
    const confirm = await confirm_popup.value.open_confirm("Bạn có chắc chắn muốn cập nhật trạng thái đơn hàng này không?");
    if (!confirm) return;

    try {
        const res = await api.post('/product/update/shutdown', { product_id })
        message.emit("show-message", { title: "Thành công", text: res.data.message, type: "success" })
        get_products()
    } catch (error) {
        message.emit("show-message", { title: "Lỗi", text: "Tạm ngưng sản phẩm thất bại!", type: "error" })
        console.error("Error updating product status:", error)
    }
}

const update_pending_status = async (product_id) => {
    const confirm = await confirm_popup.value.open_confirm("Bạn có chắc chắn muốn cập nhật trạng thái đơn hàng này không?");
    if (!confirm) return;

    try {
        const res = await api.post('/product/update/pending', { product_id })
        message.emit("show-message", { title: "Thành công", text: res.data.message, type: "success" })
        get_products()
    } catch (error) {
        message.emit("show-message", { title: "Lỗi", text: "Duyệt sản phẩm thất bại!", type: "error" })
        console.error("Error updating product status:", error)
    }
}
/**
    Variants
 */
const get_attribute = (variant) => {
    const values = variant.variant_attribute.map(v => v.attribute_value.value)

    if (values.length === 1) return values[0];

    return values.join(', ')
}

const toggle_variants = (product_id) => {
    expanded_variants.value[product_id] = !expanded_variants.value[product_id]
}

const is_expanded = (product_id) => {
    return expanded_variants.value[product_id]
}
/**
    Categories
 */
const get_categories = () => {
    const seen = new Set()
    categories.value = products_for_sort.value
        .map(p => p.category)
        .filter(c => c && !seen.has(c.id) && seen.add(c.id))
}

/**
    Mount
 */

onMounted(() => {
    get_products()
})

/**
    Watch
 */

watch([selected_category, sort_order, keyword], () => {
    filter()
})
</script>

<style scoped>
tbody>tr>td {
    border-bottom: white;
}


.__main>.__title {
    height: 50px;
}

.__main>.__content>.__product-management>.__type>.__item {
    cursor: pointer;
}

.__main>.__content>.__product-management>.__type>.__item.active {
    border-bottom: 2px solid var(--text-color);
    color: var(--text-color);
}

table>thead {
    height: 60px;
}
</style>