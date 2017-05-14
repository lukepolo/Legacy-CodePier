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
})

window.app = app

app.$store.dispatch('user_commands/get')
app.$store.dispatch('user_site_deployments/getDeployments')

Echo.channel('app').listen('ReleasedNewVersion', (data) => {
    app.$store.dispatch('system/setVersion', data)
})

app.$mount('#app-layout')

require('./emitters')