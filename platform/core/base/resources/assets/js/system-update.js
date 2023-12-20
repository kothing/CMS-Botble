import SystemUpdateComponent from './components/SystemUpdateComponent.vue'

if (typeof vueApp !== 'undefined') {
    vueApp.booting((vue) => {
        vue.component('system-update-component', SystemUpdateComponent)
    })
}
