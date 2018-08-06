<template>
    <nav aria-label="..." v-if="shouldPaginate">
        <ul class="pagination">
            <li class="page-item" :class="{ disabled: !preUrl }" @click="page--">
                <a class="page-link" href="#" tabindex="-1" ref="previous">Previous</a>
            </li>
            <li class="page-item" :class="{ disabled: !nextUrl }"  @click="page++">
                <a class="page-link" href="#" ref="next">Next</a>
            </li>
        </ul>
    </nav>
</template>

<script>
export default {
    props: ['dataSet'],
    data() {
        return {
            page: 1,
            preUrl: false,
            nextUrl: false,
        }
    },
    computed: {
        shouldPaginate() {
            return Boolean(this.preUrl || this.nextUrl);
        }
    },
    watch: {
        dataSet() {
            this.page = this.dataSet.current_page;
            this.preUrl = this.dataSet.prev_page_url;
            this.nextUrl = this.dataSet.next_page_url;
        },
        page() {
            this.broadcast().updateUrl();
        }
    },
    methods: {
        broadcast() {
            return this.$emit('updated', this.page);
        },
        updateUrl() {
            history.pushState(null, null, `?page=${this.page}`)
        }
    }
}
</script>

<style lang="scss" scoped>
.page-item {
    &.disabled {
        pointer-events: none;
    }
}
</style>
