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

Vue.directive('file-editor', {
    bind: function (element) {
        const editor = ace.edit(element);
        const form = $(element).closest('form');

        $(element).after('<input type="hidden" name="path" value="' + $(element).data('path') + '">')
        $(element).after('<textarea class="hide" name="file">Loading . . .</textarea>');

        editor.getSession().setMode("ace/mode/sh");
        editor.setOption("maxLines", 45);

        editor.getSession().on('change', function () {
            form.find('textarea[name="file"]').val(editor.getSession().getValue());
        });

        $.get(laroute.action('Server\ServerController@getFile', {
            server_id: $(element).data('server_id'),
            path: $(element).data('path')
        }), function (envFile) {
            editor.getSession().setValue(envFile);
        });
    }
});

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
            const value = window.Cookies.get(name);
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

            const data = {};

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
 | Stores
 |--------------------------------------------------------------------------
 |
 */
import userStore from './stores/UserStore'
window.userStore = userStore;

import userTeamStore from './stores/UserTeamStore'
window.userTeamStore = userTeamStore;

import serverStore from './stores/ServerStore'
window.serverStore = serverStore;

import siteStore from './stores/SiteStore'
window.siteStore = siteStore;

import pileStore from './stores/PileStore'
window.pileStore = pileStore;

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

/*
 |--------------------------------------------------------------------------
 | Profile Pages
 |--------------------------------------------------------------------------
 |
 */
import UserInfo from './pages/user/UserInfo.vue';
import UserOauth from './pages/user/UserOauth.vue';
import UserSshKeys from './pages/user/UserSSHKeys.vue';
import UserSubscription from './pages/user/UserSubscription.vue';
import UserServerProviders from './pages/user/UserServerProviders.vue';
import UserRepositoryProviders from './pages/user/UserRepositoryProvders.vue';
import UserNotificationProviders from './pages/user/UserNotificationProviders.vue';

/*
 |--------------------------------------------------------------------------
 | Server Pages
 |--------------------------------------------------------------------------
 |
 */
import ServerForm from "./pages/server/ServerForm.vue";
import ServerSites from "./pages/server/ServerSites.vue";
import ServerFiles from "./pages/server/ServerFiles.vue";
import ServerDaemons from "./pages/server/ServerDaemons.vue";
import ServerSshKeys from "./pages/server/ServerSshKeys.vue";
import ServerCronjobs from "./pages/server/ServerCronJobs.vue";
import ServerMonitoring from "./pages/server/ServerMonitoring.vue";
import ServerFirewallRules from "./pages/server/ServerFirewallRules.vue";

/*
 |--------------------------------------------------------------------------
 | Site Pages
 |--------------------------------------------------------------------------
 |
 */
import SiteForm from "./pages/site/SiteForm.vue";
import SiteFiles from "./pages/site/SiteFiles.vue";
import SiteWorkers from "./pages/site/SiteWorkers.vue";
import SiteRepository from "./pages/site/SiteRepository.vue";
import SitePHPSettings from "./pages/site/SitePHPSettings.vue";
import SiteEnvironment from "./pages/site/SiteEnvironment.vue";
import SiteSSLCertificates from "./pages/site/SiteSSLCertificates.vue";


const router = new VueRouter({
    mode: 'history',
    routes: [
        {path: '/', component : Dashboard},

        {path: '/server/create', component : ServerForm},
        {path: '/server/:server_id/sites', component : ServerSites},
        {path: '/server/:server_id/files', component : ServerFiles},
        {path: '/server/:server_id/daemons', component : ServerDaemons},
        {path: '/server/:server_id/ssh-keys', component : ServerSshKeys},
        {path: '/server/:server_id/cron-jobs', component : ServerCronjobs},
        {path: '/server/:server_id/monitoring', component : ServerMonitoring},
        {path: '/server/:server_id/firewall-rules', component : ServerFirewallRules},

        {path: '/piles', component: Piles},

        {path: '/site/create', component : SiteForm},
        {path: '/site/:site_id', component: SiteRepository},
        {path: '/site/:site_id/files', component: SiteFiles},
        {path: '/site/:site_id/workers', component: SiteWorkers},
        {path: '/site/:site_id/environment', component: SiteEnvironment},
        {path: '/site/:site_id/php-settings', component: SitePHPSettings},
        {path: '/site/:site_id/ssl-certificates', component: SiteSSLCertificates},

        {path: '/my-profile', component: UserInfo},
        {path: '/my-profile/ssh-keys', component: UserSshKeys},
        {path: '/my-profile/subscription', component: UserSubscription},
        {path: '/my-profile/server-providers', component: UserServerProviders},
        {path: '/my-profile/repository-providers', component: UserRepositoryProviders},
        {path: '/my-profile/notification-providers', component: UserNotificationProviders},
        {path: '/my-profile/oauth', component: UserOauth},
        {path: '*', redirect: '/'}
    ]
});

const app = new Vue({
    router,
    siteStore,
    userStore,
    serverStore,
    userTeamStore
}).$mount('#app-layout');

window.app = app;