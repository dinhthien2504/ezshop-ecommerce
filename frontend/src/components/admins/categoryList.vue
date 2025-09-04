<template v-if="categories_tree && categories_tree.length">
    <template v-for="cate in categories_tree" :key="cate.id">
        <tr>
            <td scope="row" class="align-middle">
                <div class="checkbox me-2">
                    <label class="checkbox-label">
                        <input type="checkbox" :value="cate.id" :checked="selected.includes(cate.id)"
                            @change="$emit('select-category', cate.id)" />
                        <div class="checkbox-wrapper">
                            <div class="checkbox-bg"></div>
                            <svg fill="none" viewBox="0 0 24 24" class="checkbox-icon">
                                <path stroke-linejoin="round" stroke-linecap="round" stroke-width="3"
                                    stroke="currentColor" d="M4 12L10 18L20 6" class="check-path"></path>
                            </svg>
                        </div>
                    </label>
                </div>
            </td>
            <td class="align-middle py-4">
                <img v-if="cate.image" class="category__item--img" :src="`/imgs/categories/${cate.image}`"
                    :alt="`${cate.name}`">
                <img v-else class="category__item--img" src="/imgs/default-cate.jpg" :alt="`${cate.name}`">
            </td>
            <td class="align-middle">
                <i class="ri-git-commit-fill text-color fs-16" v-for="n in depth" :key="n"></i>
                {{ cate.name }}
            </td>
            <td class="align-middle">{{ cate.description ?? '—' }}</td>
            <td class="align-middle">{{ getTaxName(cate.tax_id) }}</td>
            <td class="align-middle">{{ getFeeName(cate.fee_id) }}</td>
            <td class="align-middle text-center">
                <div class="more-wrapper">
                    <button class="more-btn">
                        <i class="ri-more-fill"></i>
                    </button>
                    <div class="more-popup">
                        <button class="bg-warning" @click="$emit('edit-category', cate.id)">Sửa</button>
                        <button class="text-white bg-danger" @click="$emit('delete-category', cate.id)">Xóa</button>
                    </div>
                </div>
            </td>
        </tr>
        <categoryList v-if="cate.children && cate.children.length > 0" :categories_tree="cate.children"
            :depth="depth + 1" @edit-category="$emit('edit-category', $event)"
            @delete-category="$emit('delete-category', $event)" :selected="selected"
            @select-category="$emit('select-category', $event)" :taxes="taxes" :fees="fees" />
    </template>
</template>
<script>
export default {
    name: 'categoryList',
    methods: {
        getTaxName(taxId) {
            const found = this.taxes?.find(t => t.id === taxId);
            return found ? found.name : '—';
        },
        getFeeName(feeId) {
            const found = this.fees?.find(f => f.id === feeId);
            return found ? found.name : '—';
        }
    },
    emits: [
        'edit-category',
        'delete-category',
        'select-category'
    ],
    props: {
        categories_tree: {
            type: Array,
            required: true
        },
        taxes: {
            type: Array,
            required: false
        },
        fees: {
            type: Array,
            required: false
        },
        depth: {
            type: Number,
            default: 0
        },
        selected: {
            type: Array,
            default: () => []
        }
    }
};
</script>