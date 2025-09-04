<template>
    <template v-for="cate in categories_tree" :key="cate.id">
        <option :value="cate.id">
            {{ getPrefix(depth) + cate.name }}
        </option>
        <CategoryOption v-if="cate.children && cate.children.length > 0" :categories_tree="cate.children"
            :depth="depth + 1" />
    </template>
</template>

<script>
export default {
    name: 'CategoryOption',
    props: {
        categories_tree: {
            type: Array,
            required: true
        },
        depth: {
            type: Number,
            default: 0
        }
    },
    methods: {
        getPrefix(depth) {
            return '— '.repeat(depth) + ' ';
        }
    }
};
</script>