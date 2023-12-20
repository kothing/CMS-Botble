import RepeaterComponent from './form/fields/RepeaterComponent.vue'

if (typeof vueApp !== 'undefined') {
    vueApp.booting((vue) => {
        vue.component('repeater-component', RepeaterComponent)
    })
}
