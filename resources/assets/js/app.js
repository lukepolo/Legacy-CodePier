/**
 * First we will load all of this project's JavaScript dependencies which
 * include Vue and Vue Resource. This gives a great starting point for
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

import store from "./store";
import Piles from "./pages/pile/Piles.vue";
import Dashboard from "./pages/dashboard/Dashboard.vue";
import UserInfo from "./pages/user/UserInfo.vue";
import UserOauth from "./pages/user/UserOauth.vue";
import UserSshKeys from "./pages/user/UserSSHKeys.vue";
import UserSubscription from "./pages/user/UserSubscription.vue";
import UserServerProviders from "./pages/user/UserServerProviders.vue";
import UserRepositoryProviders from "./pages/user/UserRepositoryProvders.vue";
import UserNotificationProviders from "./pages/user/UserNotificationProviders.vue";
import Teams from "./pages/team/Teams.vue";
import TeamMembers from "./pages/team/TeamMembers.vue";
import ServerForm from "./pages/server/ServerForm.vue";
import ServerSites from "./pages/server/ServerSites.vue";
import ServerFiles from "./pages/server/ServerFiles.vue";
import ServerWorkers from "./pages/server/ServerWorkers.vue";
import ServerSshKeys from "./pages/server/ServerSshKeys.vue";
import ServerCronjobs from "./pages/server/ServerCronJobs.vue";
import ServerFeatures from "./pages/server/ServerFeatures.vue";
import ServerMonitoring from "./pages/server/ServerMonitoring.vue";
import ServerFirewallRules from "./pages/server/ServerFirewallRules.vue";
import SiteArea from "./pages/site/SiteArea.vue";
import SiteNav from "./pages/site/components/SiteNav.vue";
import SiteWorkers from "./pages/site/SiteWorkers.vue";
import SiteRepository from "./pages/site/SiteRepository.vue";
import SiteFrameworkFiles from "./pages/site/SiteFrameworkFiles.vue";
import SiteServerFeatures from "./pages/site/SiteServerFeatures.vue";
import SiteSSLCertificates from "./pages/site/SiteSSLCertificates.vue";

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

        {path: '/server/create', component: ServerForm},
        {path: '/server/:server_id/sites', component: ServerSites},
        {path: '/server/:server_id/files', component: ServerFiles},
        {path: '/server/:server_id/workers', component: ServerWorkers},
        {path: '/server/:server_id/ssh-keys', component: ServerSshKeys},
        {path: '/server/:server_id/features', component: ServerFeatures},
        {path: '/server/:server_id/cron-jobs', component: ServerCronjobs},
        {path: '/server/:server_id/monitoring', component: ServerMonitoring},
        {path: '/server/:server_id/firewall-rules', component: ServerFirewallRules},

        {path: '/piles', component: Piles},


        {
            path: '/site', component: SiteArea,
            children: [
                {
                    path: '/site/:site_id',
                    components: {
                        default: SiteRepository,
                        nav: SiteNav
                    }
                },
                {
                    path: '/site/:site_id/workers',
                    components: {
                        default: SiteWorkers,
                        nav: SiteNav
                    }
                },
                {
                    path: '/site/:site_id/framework-files',
                    components: {
                        default: SiteFrameworkFiles,
                        nav: SiteNav
                    }
                },
                {
                    path: '/site/:site_id/server-features',
                    components: {
                        default: SiteServerFeatures,
                        nav: SiteNav
                    }
                },
                {
                    path: '/site/:site_id/ssl-certificates',
                    components: {
                        default: SiteSSLCertificates,
                        nav: SiteNav
                    }
                },

            ]
        },

        {path: '/my-profile', component: UserInfo},
        {path: '/my-profile/ssh-keys', component: UserSshKeys},
        {path: '/my-profile/subscription', component: UserSubscription},
        {path: '/my-profile/server-providers', component: UserServerProviders},
        {path: '/my-profile/repository-providers', component: UserRepositoryProviders},
        {path: '/my-profile/notification-providers', component: UserNotificationProviders},
        {path: '/my-profile/oauth', component: UserOauth},

        {path: '/my/teams', component: Teams},
        {path: '/my/team/:team_id/members', component: TeamMembers},

        {path: '*', redirect: '/'},
    ]
});

var app = new Vue({
    store,
    router,
}).$mount('#app-layout');

window.app = app;