require('./bootstrap')
require('./components')
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
app.$store.dispatch('user_ssh_keys/get')
app.$store.dispatch('user_site_deployments/getDeployments')
app.$store.dispatch('user_repository_providers/get', app.$store.state.user.user.id)

Echo.channel('app').listen('ReleasedNewVersion', (data) => {
    app.$store.dispatch('system/setVersion', data)
})

app.$mount('#app-layout')

require('./emitters')
