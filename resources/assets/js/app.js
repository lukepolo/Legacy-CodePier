/**
 * First we will load all of this project's JavaScript dependencies which
 * include Vue and Vue Resource. This gives a great starting point for
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the body of the page. From here, you may begin adding components to
 * the application, or feel free to tweak this setup for your needs.
 */

Vue.mixin({
    methods: {
        now: function () {
            return moment();
        },
        parseDate: function (date, timezone) {
            if (timezone) {
                return moment(date).tz(timezone);
            }
            return moment(date);
        },
        dateHumanize: function (date, timezone) {
            return moment(date).tz(timezone).fromNow();
        },
        getCookie(name, defaultValue) {
            var value = window.Cookies.get(name);
            if (value) {
                return value;
            }

            return defaultValue;
        },
        action: function (action, parameters) {
            return laroute.action(action, parameters);
        },
        route : function(route, parameters) {
            return laroute.route(route, { planet : 'world' });
        }
    }
});
/*
 |--------------------------------------------------------------------------
 | Core
 |--------------------------------------------------------------------------
 |
 */

Vue.component('Navigation', require('./core/Navigation.vue'));
Vue.component('AppFooter', require('./core/Footer.vue'));

/*
 |--------------------------------------------------------------------------
 | Pages
 |--------------------------------------------------------------------------
 |
 */
import Dashboard from "./pages/Dashboard.vue";

const Foo = {template: '<div>foo</div>'};
const Bar = {template: '<div>bar</div>'};

const router = new VueRouter({
    mode: 'history',
    routes: [
        {path: '/', component: Dashboard},
        {path: '/foo', component: Foo},
        {path: '/bar', component: Bar}
    ]
});

window.vue = new Vue({
    router,
    data() {
        return {
            user: user
        }
    }
}).$mount('#app-layout');