/**
 * First we will load all of this project's JavaScript dependencies which
 * include Vue and Vue Resource. This gives a great starting point for
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

import * as userPages from "./pages/user";
import * as teamPages from "./pages/team";
import * as sitePages from "./pages/site";
import * as serverPages from "./pages/server";

import store from "./store";
import Piles from "./pages/pile/Piles.vue";
import Dashboard from "./pages/dashboard/Dashboard.vue";

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the body of the page. From here, you may begin adding components to
 * the application, or feel free to tweak this setup for your needs.
 */

Vue.directive('file-editor', {
    bind: function (element, params) {
        const editor = ace.edit(element);

        editor.$blockScrolling = Infinity;
        editor.getSession().setMode("ace/mode/sh");
        editor.setOption("maxLines", 45);
    }
});

Vue.mixin({
    methods: {
        now() {
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
        action: function (action, parameters) {
            return laroute.action(action, parameters);
        },
        // NOTE - this will not work with PUT!!!
        // https://github.com/symfony/symfony/issues/9226
        getFormData: function (form) {

            if (!$(form).is('form')) {
                form = $(form).find('form')[0];
            }

            return new FormData(form);
        },
        serverHasFeature: function (server, feature) {
            return _.get(server.server_features, feature, false);
        }
    }
});


// Vue.config.errorHandler = function (err, vm) {
// }

/*
 |--------------------------------------------------------------------------
 | Core
 |--------------------------------------------------------------------------
 |
 */

Vue.component('Navigation', require('./core/Navigation.vue'));
Vue.component('NotificationBar', require('./core/NotificationBar.vue'));

const router = new VueRouter({
    mode: 'history',
    routes: [
        {path: '/', component: Dashboard},

        {path: '/server/create', component: serverPages.ServerForm},
        {path: '/server/:server_id/sites', component: serverPages.ServerSites},
        {path: '/server/:server_id/files', component: serverPages.ServerFiles},
        {path: '/server/:server_id/workers', component: serverPages.ServerWorkers},
        {path: '/server/:server_id/ssh-keys', component: serverPages.ServerSshKeys},
        {path: '/server/:server_id/features', component: serverPages.ServerFeatures},
        {path: '/server/:server_id/cron-jobs', component: serverPages.ServerCronJobs},
        {path: '/server/:server_id/monitoring', component: serverPages.ServerMonitoring},
        {path: '/server/:server_id/firewall-rules', component: serverPages.ServerFirewallRules},

        {path: '/piles', component: Piles},

        {
            path: '/site', component: sitePages.SiteArea,
            children: [
                {
                    path: ':site_id/repository',
                    components: {
                        default: sitePages.SiteRepository,
                        nav: sitePages.SiteArea
                    }
                },
                {
                    path: ':site_id/workers',
                    components: {
                        default: sitePages.SiteWorkers,
                        nav: sitePages.SiteArea
                    }
                },
                {
                    path: ':site_id/framework-files',
                    components: {
                        default: sitePages.SiteFrameworkFiles,
                        nav: sitePages.SiteArea
                    }
                },
                {
                    path: ':site_id/server-features',
                    components: {
                        default: sitePages.SiteServerFeatures,
                        nav: sitePages.SiteArea
                    }
                },
                {
                    path: ':site_id/ssl-certificates',
                    components: {
                        default: sitePages.SiteSSLCertificates,
                        nav: sitePages.SiteArea
                    }
                },

            ]
        },

        {path: '/my-profile', component: userPages.UserInfo},
        {path: '/my-profile/oauth', component: userPages.UserOauth},
        {path: '/my-profile/ssh-keys', component: userPages.UserSshKeys},
        {path: '/my-profile/subscription', component: userPages.UserSubscription},
        {path: '/my-profile/server-providers', component: userPages.UserServerProviders},
        {path: '/my-profile/repository-providers', component: userPages.UserRepositoryProviders},
        {path: '/my-profile/notification-providers', component: userPages.UserNotificationProviders},


        {path: '/my/teams', component: teamPages.Teams},
        {path: '/my/team/:team_id/members', component: teamPages.TeamMembers},

        {path: '*', redirect: '/'},
    ]
});

var app = new Vue({
    store,
    router,
}).$mount('#app-layout');

window.app = app;