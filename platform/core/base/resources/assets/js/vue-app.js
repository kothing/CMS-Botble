import emitter from 'tiny-emitter/instance'
import sanitizeHTML from 'sanitize-html'
import _ from 'lodash'

class VueApp {
    constructor() {
        const { createApp } = Vue
        this.vue = createApp({
            mounted() {
                $event.on('vue-app:force-update', () => {
                    this.$forceUpdate()
                })
            }
        })

        this.vue.use({
            install: (app) => {
                app.config.globalProperties.__ = (key) => {
                    if (typeof window.trans === 'undefined') {
                        return key
                    }

                    return _.get(window.trans, key, key)
                }

                app.config.globalProperties.$sanitize = sanitizeHTML
            }
        })

        this.eventBus = {
            $on: (...args) => $event.on(...args),
            $once: (...args) => $event.once(...args),
            $off: (...args) => $event.off(...args),
            $emit: (...args) => $event.emit(...args),
        }

        this.vuePlugins = []
        this.bootingCallbacks = []
        this.bootedCallbacks = []
        this.hasBooted = false
    }

    registerVuePlugins(plugin) {
        this.vuePlugins.push(plugin)
    }

    booting(callback) {
        this.bootingCallbacks.push(callback)
    }

    booted(callback) {
        this.bootedCallbacks.push(callback)
    }

    boot() {
        for (const callback of this.bootingCallbacks) {
            callback(this.vue)
        }

        for (const vuePlugin of this.vuePlugins) {
            this.vue.use(vuePlugin)
        }

        for (const callback of this.bootedCallbacks) {
            callback(this)
        }

        this.vue.mount('#app')

        this.hasBooted = true
    }
}

window.vueApp = new VueApp()
window.$event = emitter

document.addEventListener('DOMContentLoaded', () => {
    if (! window.vueApp.hasBooted) {
        window.vueApp.boot()
    }
})
