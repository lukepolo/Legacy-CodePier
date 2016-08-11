
window._ = require('lodash');

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

window.$ = window.jQuery = require('jquery');

/**
 * Vue is a modern JavaScript for building interactive web interfaces using
 * reacting data binding and reusable components. Vue's API is clean and
 * simple, leaving you to focus only on building your next great idea.
 */

window.Vue = require('vue/dist/vue');
window.VueRouter = require('vue-router');
window.VueResource = require('vue-resource');

/**
 * We'll register a HTTP interceptor to attach the "XSRF" header to each of
 * the outgoing requests issued by this application. The CSRF middleware
 * included with Laravel will automatically verify the header's value.
 */

Vue.use(VueRouter);

// Vue.http.interceptors.push(function (request, next) {
//     request.headers['X-CSRF-TOKEN'] = Laravel.csrfToken;
//
//     next();
// });

import Echo from "laravel-echo"

window.Echo = new Echo({
    connector: 'pusher',
    pusherKey: '92790f94d685df8a2c16'
});
