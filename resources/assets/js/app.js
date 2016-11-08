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

Vue.directive('cronjob', {
    bind: function (el) {
        $(el).cron({
            onChange() {
                var cronTiming = $(this).cron("value");
                $('#cron-preview').text(cronTiming);
                $('input[name="cron_timing"]').val(cronTiming);
            }
        });
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
        },
        isCommandRunning(type, model_id) {
            console.log(type);
            return _.filter(this.$store.state.userStore.runningCommands, function(object, commandType) {
                if(type == commandType) {
                    if(_.find(object, function(item) {
                            return item.site_connection == model_id;
                    })) {
                        return true;
                    }
                }
                return false;
            }).length > 0;
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

        {path: '/server/create/:site/:type', name: 'server_form', component: serverPages.ServerForm},
        {path: '/server/:server_id/sites', name: 'server_sites', component: serverPages.ServerSites},
        {path: '/server/:server_id/files', name: 'server_files', component: serverPages.ServerFiles},
        {path: '/server/:server_id/workers', name: 'server_workers', component: serverPages.ServerWorkers},
        {path: '/server/:server_id/ssh-keys', name: 'server_ssh_keys', component: serverPages.ServerSshKeys},
        {path: '/server/:server_id/features', name: 'server_features', component: serverPages.ServerFeatures},
        {path: '/server/:server_id/cron-jobs', name: 'server_cron_jobs', component: serverPages.ServerCronJobs},
        {path: '/server/:server_id/monitoring', name: 'server_monitoring', component: serverPages.ServerMonitoring},
        {path: '/server/:server_id/firewall-rules', name: 'server_firewall_rules', component: serverPages.ServerFirewallRules},

        {path: '/piles', name: 'piles', component: Piles},

        {
            path: '/site', component: sitePages.SiteArea,
            children: [
                {
                    path: ':site_id/repository',
                    name: 'site_repository',
                    components: {
                        default: sitePages.SiteRepository,
                        nav: sitePages.SiteNav
                    }
                },
                {
                    path: ':site_id/deployment',
                    name: 'site_deployment',
                    components: {
                        default: sitePages.SiteDeployment,
                        nav: sitePages.SiteNav
                    }
                },
                {
                    path: ':site_id/workers',
                    name: 'site_workers',
                    components: {
                        default: sitePages.SiteJobs,
                        nav: sitePages.SiteNav
                    }
                },
                {
                    path: ':site_id/firewall-rules',
                    name: 'site_firewall_rules',
                    components: {
                        default: sitePages.SiteFirewallRules,
                        nav: sitePages.SiteNav
                    }
                },
                {
                    path: ':site_id/ssh-keys',
                    name: 'site_ssh_keys',
                    components: {
                        default: sitePages.SiteSshKeys,
                        nav: sitePages.SiteNav
                    }
                },
                {
                    path: ':site_id/framework-files',
                    name: 'site_files',
                    components: {
                        default: sitePages.SiteFiles,
                        nav: sitePages.SiteNav
                    }
                },
                {
                    path: ':site_id/server-features',
                    name: 'site_server_features',
                    components: {
                        default: sitePages.SiteServerFeatures,
                        nav: sitePages.SiteNav
                    }
                },
                {
                    path: ':site_id/ssl-certificates',
                    name: 'site_ssl_certs',
                    components: {
                        default: sitePages.SiteSSLCertificates,
                        nav: sitePages.SiteNav
                    }
                },

            ]
        },
        {
            path: '/my-profile', component: userPages.UserArea,
            children: [
                {
                    path: '/',
                    name: 'my_profile',
                    components: {
                        default: userPages.UserInfo,
                        nav: userPages.UserNav
                    }
                },
                {
                    path: 'oauth',
                    name: 'oauth',
                    components: {
                        default: userPages.UserOauth,
                        nav: userPages.UserNav
                    }
                },
                {
                    path: 'ssh-keys',
                    name: 'user_ssh_keys',
                    components: {
                        default: userPages.UserSshKeys,
                        nav: userPages.UserNav
                    }
                },
                {
                    path: 'subscription',
                    name: 'subscription',
                    components: {
                        default: userPages.UserSubscription,
                        nav: userPages.UserNav
                    }
                },
                {
                    path: 'server-providers',
                    name: 'user_server_providers',
                    components: {
                        default: userPages.UserServerProviders,
                        nav: userPages.UserNav
                    }
                },
                {
                    path: 'repository-providers',
                    name: 'user_repository_providers',
                    components: {
                        default: userPages.UserRepositoryProviders,
                        nav: userPages.UserNav
                    }
                },
                {
                    path: 'notification-providers',
                    name: 'user_notification_providers',
                    components: {
                        default: userPages.UserNotificationProviders,
                        nav: userPages.UserNav
                    }
                },
            ]
        },

        {path: '/my/teams', name: 'teams', component: teamPages.Teams},
        {path: '/my/team/:team_id/members', name: 'team_members', component: teamPages.TeamMembers},

        {path: '*', redirect: '/'},
    ]
});

var app = new Vue({
    store,
    router,
}).$mount('#app-layout');

window.app = app;