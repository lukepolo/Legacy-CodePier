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

app.$store.dispatch('getRunningCommands')
app.$store.dispatch('getRunningDeployments')

Echo.channel('app').listen('ReleasedNewVersion', (data) => {
    app.$store.dispatch('setVersion', data)
})
