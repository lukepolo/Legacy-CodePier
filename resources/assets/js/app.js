require('./bootstrap')
require('./app/core')
require('./directives')

import store from './store'
import mixins from './mixins'
import router from './router'

Vue.mixin({
    methods: mixins
})

const app = new Vue({
    store,
    router
}).$mount('#app-layout')

window.app = app

app.$store.dispatch('user_commands/get')
app.$store.dispatch('user_sites_deployments/get')

Echo.channel('app').listen('ReleasedNewVersion', (data) => {
    app.$store.dispatch('system/setVersion', data)
})
