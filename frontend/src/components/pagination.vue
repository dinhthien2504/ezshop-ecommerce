<template>
    <div v-if="meta && meta.last_page > 1" class="container__pagination">
        <!-- First -->
        <!-- <button type="button" class="sub-btn px-3 py-1" :disabled="meta.current_page === 1" @click="$emit('changePage', 1)">
            First
        </button> -->

        <!-- Prev -->
        <button type="button" class="fs-30" :disabled="meta.current_page === 1"
            @click="$emit('changePage', meta.current_page - 1)">
            <i class="ri-arrow-left-s-line"></i>
        </button>

        <!-- Page numbers -->
        <template v-for="page in pages" :key="page">
            <span v-if="page === '...'" class="select-none cursor-default">...</span>

            <button type="button" v-else @click="$emit('changePage', page)" :class="[
                page === meta.current_page ? 'active' : ''
            ]">
                {{ page }}
            </button>
        </template>

        <!-- Next -->
        <button type="button" class="fs-30" :disabled="meta.current_page === meta.last_page"
            @click="$emit('changePage', meta.current_page + 1)">
            <i class="ri-arrow-right-s-line"></i>
        </button>

        <!-- Last -->
        <!-- <button type="button" class="sub-btn px-3 py-1" :disabled="meta.current_page === meta.last_page"
            @click="$emit('changePage', meta.last_page)">
            Last
        </button> -->
    </div>
</template>
<style scoped>
.container__pagination {
    display: flex;
    align-items: center;
    gap: 13px;
}

.container__pagination button {
    outline: none;
    border: none;
    padding: 2px 12px;
    color: #00000066;
    font-size: 20px;
    font-weight: 500;
    background-color: transparent;
}

.container__pagination button.active {
    background-color: var(--text-color);
    color: var(--white-color);
}
</style>
<script>
export default {
    props: {
        meta: {
            type: Object,
            required: true,
        },
    },
    computed: {
        pages() {
            const current = this.meta.current_page
            const last = this.meta.last_page
            const range = []

            if (last <= 7) {
                for (let i = 1; i <= last; i++) range.push(i)
            } else {
                range.push(1)

                if (current > 4) range.push('...')

                const start = Math.max(2, current - 1)
                const end = Math.min(last - 1, current + 1)

                for (let i = start; i <= end; i++) range.push(i)

                if (current < last - 3) range.push('...')

                range.push(last)
            }

            return range
        },
    },
}
</script>