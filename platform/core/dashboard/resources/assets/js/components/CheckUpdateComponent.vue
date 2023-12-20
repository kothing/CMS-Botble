<template>
    <div class="note note-warning" v-if="hasNewVersion">
        <p>{{ message }}, please go to <a :href="settingUrl">System Updater</a> to upgrade to the latest version!</p>
    </div>
</template>

<script>
export default {
    props: {
        checkUpdateUrl: {
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
            hasNewVersion: false,
            message: null,
        }
    },
    mounted() {
        this.checkUpdate()
    },

    methods: {
        checkUpdate() {
            axios.get(this.checkUpdateUrl).then((res) => {
                if (!res.data.error && res.data.data.has_new_version) {
                    this.hasNewVersion = true
                    this.message = res.data.message
                }
            })
        },
    },
}
</script>
