<template>
    <div class="content">
        <slot></slot>

        <div class="text-center" v-if="!performingUpdate">
            <button
                type="button"
                class="btn btn-warning"
                v-if="!askToProcessUpdate"
                @click.prevent="askToProcessUpdate = true"
            >
                <i class="fa me-2" :class="{ 'fa-download': isOutdated, 'fa-refresh': !isOutdated }"></i>
                <span v-if="isOutdated">Download & Install Update</span>
                <span v-else>Re-install The Latest Version</span>
            </button>

            <button type="button" class="btn btn-danger" v-if="askToProcessUpdate" @click="performUpdate">
                <i class="fa fa-check me-2"></i> <span>Click To Confirm!</span>
            </button>
        </div>

        <div class="updating" v-if="performingUpdate">
            <div class="updating-wrapper">
                <div class="updating-container">
                    <div class="loader" v-if="loading">
                        <half-circle-spinner :animation-duration="1000" :size="32" />
                    </div>
                    <div class="information">
                        <p v-for="result in results" v-text="result.text" :class="result.error ? 'bold text-danger' : 'bold'"></p>
                    </div>

                    <div class="important text-danger" v-if="loading">
                        <strong>DO NOT CLOSE WINDOWS DURING UPDATE!</strong>
                    </div>
                    <div v-else>
                        <div class="btn btn-info" @click="refresh">Click Here To Exit</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { HalfCircleSpinner } from 'epic-spinners'

export default {
    components: {
        HalfCircleSpinner,
    },

    props: {
        updateUrl: String,
        updateId: String,
        version: String,
        isOutdated: Boolean,
    },

    data() {
        return {
            askToProcessUpdate: false,
            performingUpdate: false,
            results: [],
            loading: false,
        }
    },

    methods: {
        async performUpdate() {
            this.loading = true
            this.performingUpdate = true

            try {
                this.results.push({text: 'Downloading update...', error: false})
                await this.triggerUpdate(1)

                this.results.push({text: 'Copying files & database...', error: false})
                await this.triggerUpdate(2)

                this.results.push({text: 'Publishing assets...', error: false})
                await this.triggerUpdate(3)

                this.results.push({text: 'Cleaning up...', error: false})
                await this.triggerUpdate(4)

                this.loading = false
                this.results.push({text: 'Done! Your browser will be refreshed in 30 seconds.', error: false})

                setTimeout(() => this.refresh(), 30000)
            } catch (e) {
                this.loading = false
                this.results.push({text: e.response.data.message, error: true})
            }
        },

        async triggerUpdate(step = 1) {
            return axios.post(this.updateUrl, {
                step: step,
                update_id: this.updateId,
                version: this.version,
            })
        },

        refresh() {
            window.location.reload()
        },
    },
}
</script>

<style lang="scss" scoped>
.updating {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    z-index: 9999;
    overflow: hidden;
    backdrop-filter: blur(5px);

    > .updating-wrapper {
        position: absolute;
        top: calc(50% - 100px);
        height: 100%;
        width: 100%;

        > .updating-container {
            max-width: 500px;
            margin: 0 auto;
            text-align: center;

            .information {
                padding: 0 8px;
                margin: 32px 0;
                font-size: 18px;
                color: #efefef;
            }
        }
    }
}
</style>
