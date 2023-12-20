<template>
    <div class="note note-warning" v-if="!verified">
        <p>
            Your license is invalid, please contact support. If you didn't setup license code, please go to
            <a :href="settingUrl">Settings</a> to activate license!
        </p>
    </div>
</template>

<script>
export default {
    props: {
        verifyUrl: {
            type: String,
            default: () => null,
            required: true,
        },
        settingUrl: {
            type: String,
            default: () => null,
            required: true,
        },
    },

    data() {
        return {
            verified: true,
        }
    },
    mounted() {
        this.verifyLicense()
    },

    methods: {
        verifyLicense() {
            axios.get(this.verifyUrl).then((res) => {
                if (res.data.error) {
                    this.verified = false
                }
            })
        },
    },
}
</script>
