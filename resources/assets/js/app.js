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
        },
        getFormData : function(el) {

            if(!$(el).is('form')) {
                el = $(el).find('form');
            }

            var data = {};

            $.each($(el).serializeArray(), function() {

                this.name = this.name.replace('[]', '');
                if (data[this.name]) {
                    if (!data[this.name].push) {
                        data[this.name] = [data[this.name]];
                    }
                    data[this.name].push(this.value || '');
                } else {
                    data[this.name] = this.value || '';
                }
            });

            return data;
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



import Piles from "./pages/pile/Piles.vue";
import Dashboard from "./pages/dashboard/Dashboard.vue";
import ServerForm from "./pages/server/ServerForm.vue";

/*
 |--------------------------------------------------------------------------
 | Profile Pages
 |--------------------------------------------------------------------------
 |
 */
import UserInfo from './pages/auth/UserInfo.vue';
import UserSshKeys from './pages/auth/UserSSHKeys.vue';
import UserSubscription from './pages/auth/UserSubscription.vue';
import UserServerProviders from './pages/auth/UserServerProviders.vue';
import UserRepositoryProviders from './pages/auth/UserRepositoryProvders.vue';
import UserNotificationProviders from './pages/auth/UserNotificationProviders.vue';

const router = new VueRouter({
    mode: 'history',
    routes: [
        {path: '/', component : Dashboard},
        {path: '/server/create', component : ServerForm},

        {path: '/piles', component: Piles},

        {path: '/my-profile', component: UserInfo},
        {path: '/my-profile/ssh-keys', component: UserSshKeys},
        {path: '/my-profile/subscription', component: UserSubscription},
        {path: '/my-profile/server-providers', component: UserServerProviders},
        {path: '/my-profile/repository-providers', component: UserRepositoryProviders},
        {path: '/my-profile/notification-providers', component: UserNotificationProviders},

    ]
});

window.vue = new Vue({
    router,
    data() {
        return {
            user: user
        }
    },
}).$mount('#app-layout');
