<template>
    <div class="p-3 col-12 col-sm-6 col-md-4 col-lg-3">
        <div class="card h-100">
            <img :src="data.image_url" class="card-img-top" :alt="data.name" />
            <div class="card-body">
                <h5 class="card-title">{{ data.name }}</h5>
                <div class="card-text text-truncate">{{ data.description }}</div>
                <div>
                    <span class="badge rounded-pill bg-info me-1">{{ __('base.version') }} {{ data.latest_version }}</span>
                    <span class="badge rounded-pill bg-info">{{ __('base.minimum_core_version') }} {{ data.minimum_core_version }}</span>
                </div>

                <div class="mt-2 card-text d-flex justify-content-between flex-wrap">
                    <small class="text-muted">
                        {{ __('base.last_update') }}:
                        {{ data.humanized_last_updated_at }}
                    </small>

                    <Rating :count="data.ratings_count" :avg="data.ratings_avg"></Rating>
                </div>

                <Compatible v-if="versionCheck"></Compatible>
            </div>

            <div class="card-footer d-flex">
                <button v-if="!installed" class="btn btn-warning" @click.prevent="install()">
                    <i :class="{
                        'fa-solid fa-download': !installing,
                        'fas fa-circle-notch fa-spin': installing,
                    }"></i>
                    <span
                        class="d-inline-block d-md-none d-xl-inline-block ms-1"
                        v-text="!installing ? __('base.install_now') : __('base.installing')"
                    ></span>
                </button>

                <button v-if="installed && !activated" class="btn btn-success" @click.prevent="changeStatus()">
                    <i :class="{
                        'fa-solid fa-check': !activating,
                        'fas fa-circle-notch fa-spin': activating,
                    }"></i>
                    <span
                        class="d-inline-block d-md-none d-xl-inline-block ms-1"
                        v-text="!activating ? __('base.activate') : __('base.activating')"
                    ></span>
                </button>

                <button v-if="installed && activated" class="btn btn-info btn-disabled" disabled="disabled">
                    <span>{{ __('base.activated') }}</span>
                </button>

                <button type="button" class="btn btn-secondary ms-auto" @click.prevent="detail()">
                    <i class="fa-solid fa-info-circle"></i>
                    <span class="d-inline-block d-md-none d-xl-inline-block ms-1">{{ __('base.detail') }}</span>
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import Rating from './Rating.vue'
import Compatible from './Compatible.vue'

export default {
    name: 'marketplace-card',
    data() {
        return {
            versionCheck: false,
            installing: false,
            installed: false,
            activating: false,
            activated: false,
            pluginName: '',
        }
    },
    props: {
        data: [],
    },
    components: {
        Rating,
        Compatible,
    },
    created() {
        $event.on('assignInstalled', this.assignInstalled)
        $event.on('assignActivated', this.assignActivated)
        $event.on('onError', this.onError)

        this.setNamePlugin()
        this.checkVersion()
        this.checkInstalled()
        this.checkActivated()
    },
    methods: {
        setNamePlugin() {
            const packageName = this.data.package_name
            this.pluginName = packageName.substring(packageName.indexOf('/') + 1)
        },
        detail() {
            $event.emit('detail', this.data)
        },
        install() {
            this.installing = true
            $event.emit('install', this.data.id)
        },
        changeStatus() {
            if (!this.activated) {
                this.activating = true
                $event.emit('changeStatus', this.pluginName)
            }
        },
        assignInstalled(name) {
            const size = Object.keys(window.marketplace.installed).length
            if (this.pluginName === name) {
                this.installing = false
                window.marketplace.installed[size] = this.pluginName
            }
            this.checkInstalled()
        },
        assignActivated(name) {
            const size = Object.keys(window.marketplace.activated).length
            if (this.pluginName === name) {
                this.activated = false
                window.marketplace.activated[size] = this.pluginName
            }
            this.checkActivated()
        },
        onError() {
            this.installing = false
            this.activating = false
        },
        checkVersion() {
            return this.versionCheck = this.data.version_check
        },
        checkInstalled() {
            if (Object.values(window.marketplace.installed).indexOf(this.pluginName) > -1) {
                this.installed = true
            }
        },
        checkActivated() {
            if (Object.values(window.marketplace.activated).indexOf(this.pluginName) > -1) {
                this.activated = true
            }
        },
    },
}
</script>
