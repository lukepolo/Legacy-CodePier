window.Vue = require('vue')
window.laroute = require('./laroute')
window.moment = require('moment-timezone')
window.moment.tz.setDefault('UTC')

_.mixin(require("lodash-inflection"))

require('vue-resource')

window.ace = require('brace')
require('brace/mode/sh');
require('brace/ext/searchbox');
require('brace/theme/monokai');

require('jcf-forms')

window.VueRouter = require('vue-router/dist/vue-router.common.js')
Vue.use(VueRouter)

import NProgress from 'nprogress'

Vue.http.interceptors.push((request, next) => {
    request.headers.set('X-CSRF-TOKEN', Laravel.csrfToken)

    NProgress.start()
    next((response) => {
        if (_.isSet(response.data)) {
            if (response.data.error === 'Unauthenticated.') {
                location.reload()
            }
        }
        NProgress.done()
    })
})

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': Laravel.csrfToken
    }
})

import Echo from 'laravel-echo'

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: Laravel.pusherKey
})
