window.laroute = require('./laroute')
window.$ = window.jQuery = require('jquery')
window._ = require('lodash')
window.Vue = require('vue')
window.VueRouter = require('vue-router')

window.ace = require('brace')
require('brace/mode/javascript')
require('brace/theme/monokai')

require('bootstrap')
require('jcf-forms')
window.moment = require('moment-timezone')
moment.tz.setDefault('UTC')

/**
 * We'll register a HTTP interceptor to attach the "CSRF" header to each of
 * the outgoing requests issued by this application. The CSRF middleware
 * included with Laravel will automatically verify the header's value.
 */
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

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

import Echo from 'laravel-echo'

window.Echo = new Echo({
  broadcaster: 'pusher',
  key: Laravel.pusherKey
})
