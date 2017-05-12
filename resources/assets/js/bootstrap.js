/*
 |--------------------------------------------------------------------------
 | Global Variables
 |--------------------------------------------------------------------------
 |
 */
window.Vue = require('vue')
window.laroute = require('./laroute')
window.moment = require('moment-timezone')
window.moment.tz.setDefault('UTC')
window.VueRouter = require('vue-router/dist/vue-router.common.js')

/*
 |--------------------------------------------------------------------------
 | Vendors
 |--------------------------------------------------------------------------
 |
 */

require('jcf-forms')
require('vue-resource')
_.mixin(require("lodash-inflection"))
require('../bower/jquery-cron/cron/jquery-cron.js')

/**
 * Ace editor
 */

window.ace = require('brace')
require('brace/mode/sh');
require('brace/ext/searchbox');
require('brace/theme/monokai');

/*
 |--------------------------------------------------------------------------
 | Vue Setup
 |--------------------------------------------------------------------------
 |
 */
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

/*
 |--------------------------------------------------------------------------
 | Laravel Echo Setup
 |--------------------------------------------------------------------------
 |
 */

import Echo from 'laravel-echo'

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: Laravel.pusherKey
})
