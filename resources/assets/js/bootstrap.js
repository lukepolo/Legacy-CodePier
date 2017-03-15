window._ = require('lodash')
_.mixin(require("lodash-inflection"))

window.Vue = require('vue')

window.Clipboard = require('clipboard')

window.VueRouter = require('vue-router/dist/vue-router.common.js')
require('vue-resource')

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

window.Pusher = require('pusher-js');

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: Laravel.pusherKey
})
