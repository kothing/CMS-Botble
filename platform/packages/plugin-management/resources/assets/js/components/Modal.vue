<template>
    <div v-bind="$attrs" class="modal-marketplace modal fade" aria-hidden="true" ref="modalProduct">
        <div class="modal-dialog modal-xl my-1 modal-dialog-marketplace">
            <div class="modal-content modal-content-marketplace">
                <div class="modal-header bg-warning">
                    <h4 class="modal-title">
                        <i class="til_img"></i><strong>{{ product.name }}</strong>
                    </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body marketplace-modal-body">
                    <div class="row row-iframe">
                        <div class="overlay" v-if="loaded">
                            <div class="overlay__inner">
                                <div class="overlay__content"><span class="spinner"></span></div>
                            </div>
                        </div>
                        <iframe :src="iframeUrl"></iframe>
                    </div>
                </div>
                <div class="modal-footer">
                    <button v-if="!installed" class="btn btn-warning" @click.prevent="install()">
                        <span v-if="!installing"
                            ><i class="fa-solid fa-download"></i> {{ __('base.install_now') }}</span
                        >
                        <span v-else><i class="fas fa-circle-notch fa-spin"></i> {{ __('base.installing') }}</span>
                    </button>

                    <button v-if="installed && !activated" class="btn btn-success" @click.prevent="changeStatus()">
                        <span v-if="!activating"><i class="fa-solid fa-check"></i> {{ __('base.activate') }}</span>
                        <span v-else><i class="fas fa-circle-notch fa-spin"></i> {{ __('base.activating') }}</span>
                    </button>

                    <button v-if="installed && activated" class="btn btn-info btn-disabled" disabled="disabled">
                        <span>{{ __('base.activated') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'marketplace-modal',
    data() {
        return {
            product: {},
            pluginName: '',
            installing: false,
            installed: false,
            activating: false,
            activated: false,
            loaded: false,
        }
    },
    props: {
        iframeUrl: String,
    },
    created() {
        $event.on('assignInstalled', this.assignInstalled)
        $event.on('assignActivated', this.assignActivated)
    },
    methods: {
        setProduct(data) {
            this.product = data
            this.installed = false
            this.activated = false
            this.setNamePlugin(data)
        },
        showModal(id) {
            this.id = id
            const modal = new bootstrap.Modal(document.getElementById(id))
            return modal.show()
        },
        hideModal(id) {
            const modal = bootstrap.Modal.getInstance(document.getElementById(id))
            this.product = {}

            return modal.hide()
        },
        install() {
            this.installing = true
            $event.emit('install', this.product.id)
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
        setNamePlugin(data) {
            const packageName = data.package_name
            this.pluginName = packageName.substring(packageName.indexOf('/') + 1)
            this.checkInstalled()
            this.checkActivated()
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

<style lang="scss" scoped>
.modal-dialog-marketplace {
    min-width: 70%;
}

.marketplace-modal-body {
    padding: 0 0 0 0;
}

.modal-content-marketplace {
    height: 97vh;
    position: relative;
}

.row-iframe {
    height: 100%;
}

.overlay {
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    position: absolute;
    background-color: rgba(255, 255, 255, 0.5);
    z-index: 10000000;
    border-radius: var(--bs-border-color-translucent);
}

.overlay__inner {
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    position: absolute;
}

.overlay__content {
    left: 50%;
    position: absolute;
    top: 50%;
    transform: translate(-50%, -50%);
}

.spinner {
    width: 75px;
    height: 75px;
    display: inline-block;
    border-width: 2px;
    border-color: rgba(0, 0, 0, 0.1);
    border-top-color: #fff;
    animation: spin 1s infinite linear;
    border-radius: 100%;
    border-style: solid;
}

@keyframes spin {
    100% {
        transform: rotate(360deg);
    }
}
</style>
