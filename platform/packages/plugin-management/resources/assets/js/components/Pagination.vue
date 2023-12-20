<template>
    <nav v-if="pagination.total > pagination.per_page" class="d-flex justify-items-center justify-content-between">
        <div class="d-flex justify-content-between flex-fill d-sm-none">
            <ul class="pagination pagination-sm">
                <li v-if="pagination.current_page === 1" class="page-item disabled" aria-disabled="true">
                    <span class="page-link" v-html="__('base.previous')"></span>
                </li>

                <li v-else class="page-item">
                    <a
                        class="page-link"
                        @click.prevent="changePage(pagination.current_page - 1)"
                        href="#"
                        rel="prev"
                        v-html="__('base.previous')"
                    ></a>
                </li>

                <li v-if="pagination.current_page < pagination.last_page" class="page-item">
                    <a
                        class="page-link"
                        @click.prevent="changePage(pagination.current_page + 1)"
                        href="#"
                        rel="next"
                        v-html="__('base.next')"
                    ></a>
                </li>

                <li v-else class="page-item disabled" aria-disabled="true">
                    <span class="page-link" v-html="__('base.next')"></span>
                </li>
            </ul>
        </div>

        <div class="d-none flex-sm-fill d-sm-flex align-items-sm-center justify-content-sm-between">
            <div>
                <p class="small text-muted">
                    {{ __('base.showing') }}
                    <span class="fw-semibold">{{ pagination.from }}</span>
                    {{ __('base.to') }}
                    <span class="fw-semibold">{{ pagination.to }}</span>
                    {{ __('base.of') }}
                    <span class="fw-semibold">{{ pagination.total }}</span>
                    {{ __('base.results') }}
                </p>
            </div>

            <div>
                <ul class="pagination">
                    <li v-if="pagination.current_page === 1" class="page-item disabled" aria-disabled="true">
                        <span class="page-link" aria-hidden="true" v-html="__('base.previous')"></span>
                    </li>

                    <li v-else class="page-item">
                        <a
                            class="page-link"
                            @click.prevent="changePage(pagination.current_page - 1)"
                            href="#"
                            rel="prev"
                            v-html="__('base.previous')"
                        ></a>
                    </li>

                    <li
                        v-for="(page, index) in pages"
                        :key="index"
                        class="page-item"
                        :class="{
                            active: pagination.current_page === page,
                            disabled: page === '...',
                        }"
                    >
                        <a
                            v-if="page !== pagination.current_page"
                            class="page-link"
                            @click.prevent="changePage(page)"
                            :class="{ active: pagination.current_page === page }"
                            v-html="page"
                        ></a>

                        <span v-else class="page-link" v-html="page"></span>
                    </li>

                    <li v-if="pagination.current_page !== pagination.last_page" class="page-item">
                        <a
                            class="page-link"
                            @click.prevent="changePage(pagination.current_page + 1)"
                            href="#"
                            rel="next"
                            v-html="__('base.next')"
                        ></a>
                    </li>

                    <li v-else class="page-item disabled" aria-disabled="true">
                        <span class="page-link" aria-hidden="true" v-html="__('base.next')"></span>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</template>

<script>
export default {
    name: 'marketplace-pagination',
    data() {
        return {
            pages: [],
        }
    },
    props: {
        pagination: {
            type: [Object],
            required: true,
            default: {},
        },
    },
    created() {
        this.pageRange()
    },
    methods: {
        changePage(page) {
            this.$emit('change-page', page)
        },
        pageRange() {
            let current = this.pagination.current_page
            let last = this.pagination.last_page
            let delta = this.pagination.per_page

            let left = current - delta
            let right = current + delta + 1
            let range = []
            let pages = []
            let l
            for (let i = 1; i <= last; i++) {
                if (i === 1 || i === last || (i >= left && i < right)) {
                    range.push(i)
                }
            }

            range.forEach(function (i) {
                if (l) {
                    if (i - l === 2) {
                        pages.push(l + 1)
                    } else if (i - l !== 1) {
                        pages.push('...')
                    }
                }
                pages.push(i)
                l = i
            })

            return (this.pages = pages)
        },
    },
}
</script>
