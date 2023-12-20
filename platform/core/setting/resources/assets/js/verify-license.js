import LicenseComponent from './components/LicenseComponent.vue'

if (typeof vueApp !== 'undefined') {
    vueApp.booting((vue) => {
        vue.component('license-component', LicenseComponent)
    })
}
