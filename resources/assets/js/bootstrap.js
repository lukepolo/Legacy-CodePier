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
 | Axios Setup
 |--------------------------------------------------------------------------
 |
 */

import NProgress from 'nprogress'

window.axios = require('axios')
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

axios.interceptors.request.use((config) => {
    NProgress.configure({
        easing: 'ease',
        speed: 500,
        showSpinner: false,
    })
    NProgress.start()
    NProgress.inc(0.3)
    return config
}, function (error) {
    return Promise.reject(error)
});

axios.interceptors.response.use((response) => {
    NProgress.done()
    return response
}, function (error) {
    return Promise.reject(error)
});

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
