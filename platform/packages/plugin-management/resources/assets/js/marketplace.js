import Plugins from './components/Plugins.vue'
import CardPlugin from './components/CardPlugin.vue'

if (typeof vueApp !== 'undefined') {
    vueApp.booting((vue) => {
        vue.component('marketplace-plugins', Plugins)
        vue.component('marketplace-card-plugin', CardPlugin)
    })
}

