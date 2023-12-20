<template>
    <small>
        <i v-for="(s, index) in star" :key="index" class="fa-sm" :class="s"></i>
        <span class="fw-bold ms-1">
            <span class="badge bg-info text-wrap fw-bold">{{ count }}</span>
        </span>
    </small>
</template>

<script>
export default {
    name: 'marketplace-card-ratting',
    data() {
        return {
            star: [],
        }
    },
    props: {
        count: 0,
        avg: 0,
    },
    created() {
        this.array()
    },
    methods: {
        array() {
            for (let i = 1; i <= 5; i++) {
                if (this.avg >= i) {
                    this.star.push('fa-solid fa-star text-warning')
                } else if (this.avg + 0.5 > i) {
                    this.star.push('fa-regular fa-star-half-stroke text-warning')
                } else if (this.avg < i) {
                    this.star.push('fa-regular fa-star text-warning')
                }
            }
        },
    },
}
</script>
