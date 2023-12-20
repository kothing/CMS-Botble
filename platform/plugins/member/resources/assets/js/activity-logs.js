import ActivityLogComponent from './components/dashboard/ActivityLogComponent.vue'

if (typeof vueApp !== 'undefined') {
    vueApp.booting((vue) => {
        vue.component('activity-log-component', ActivityLogComponent)
    })
}
