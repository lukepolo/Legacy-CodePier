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
    bind: function (element, params) {
        const editor = ace.edit(element);

        editor.getSession().setMode("ace/mode/sh");
        editor.setOption("maxLines", 45);

        editor.getSession().on('change', function () {
            $('textarea[name="'+ params.value.server + params.value.file +'"]').val(editor.getSession().getValue());
        });

        Vue.http.post(laroute.action('Server\ServerController@getFile', {
            file: params.value.file,
            server: params.value.server
        })).then((response) => {
            editor.getSession().setValue(response.json());
        }, (errors) => {
            alert(error);
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
        action: function (action, parameters) {
            return laroute.action(action, parameters);
        },
        getFormData : function(el) {

            if(!$(el).is('form')) {
                el = $(el).find('form');
            }

            // TODO - copy jquerys way of getting the proper data strings
            return $(el).serializeArray();
        }
    }
});


// Vue.config.errorHandler = function (err, vm) {
// }

/*
 |--------------------------------------------------------------------------
 | User Stores
 |--------------------------------------------------------------------------
 |
 */
import userStore from './stores/User/UserStore'
window.userStore = userStore;

import userTeamStore from './stores/User/UserTeamStore'
window.userTeamStore = userTeamStore;

import userSshKeyStore from './stores/User/UserSshKeyStore'
window.userSshKeyStore = userSshKeyStore;

import userSubscriptionStore from './stores/User/UserSubscriptionStore'
window.userSubscriptionStore = userSubscriptionStore;

/*
 |--------------------------------------------------------------------------
 | Server Stores
 |--------------------------------------------------------------------------
 |
 */

import serverStore from './stores/Server/ServerStore';
window.serverStore = serverStore;

import serverSshKeyStore from './stores/Server/ServerSshKeyStore';
window.serverSshKeyStore = serverSshKeyStore;

import serverProviderStore from './stores/Server/ServerProviderStore';
window.serverProviderStore = serverProviderStore;

import serverCronJobStore from './stores/Server/ServerCronJobStore';
window.serverCronJobStore = serverCronJobStore;

import serverDaemonStore from './stores/Server/ServerDaemonStore';
window.serverDaemonStore = serverDaemonStore;

import serverFirewallStore from './stores/Server/ServerFirewallStore';
window.serverFirewallStore = serverFirewallStore;

import serverServicesStore from './stores/Server/ServerServicesStore';
window.serverServicesStore = serverServicesStore;

/*
 |--------------------------------------------------------------------------
 | Site Stores
 |--------------------------------------------------------------------------
 |
 */
import siteStore from './stores/SiteStore'
window.siteStore = siteStore;


/*
 |--------------------------------------------------------------------------
 | Pile Stores
 |--------------------------------------------------------------------------
 |
 */
import pileStore from './stores/PileStore'
window.pileStore = pileStore;

import subscriptionStore from './stores/subscriptionStore'
window.subscriptionStore = subscriptionStore;

import eventStore from './stores/EventStore'
window.eventStore = eventStore;

/*
 |--------------------------------------------------------------------------
 | Core
 |--------------------------------------------------------------------------
 |
 */

Vue.component('Navigation', require('./core/Navigation.vue'));
Vue.component('NotificationBar', require('./core/NotificationBar.vue'));

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
 | Team Pages
 |--------------------------------------------------------------------------
 |
 */
import Teams from './pages/team/Teams.vue';
import TeamMembers from './pages/team/TeamMembers.vue';


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
import ServerFeatures from "./pages/server/ServerFeatures.vue";
import ServerMonitoring from "./pages/server/ServerMonitoring.vue";
import ServerFirewallRules from "./pages/server/ServerFirewallRules.vue";

/*
 |--------------------------------------------------------------------------
 | Site Pages
 |--------------------------------------------------------------------------
 |
 */
import SiteFiles from "./pages/site/SiteFiles.vue";
import SiteWorkers from "./pages/site/SiteWorkers.vue";
import SiteRepository from "./pages/site/SiteRepository.vue";
import SiteFrameworkFiles from "./pages/site/SiteFrameworkFiles.vue";
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
        {path: '/server/:server_id/features', component : ServerFeatures},
        {path: '/server/:server_id/cron-jobs', component : ServerCronjobs},
        {path: '/server/:server_id/monitoring', component : ServerMonitoring},
        {path: '/server/:server_id/firewall-rules', component : ServerFirewallRules},

        {path: '/piles', component: Piles},

        {path: '/site/:site_id', component: SiteRepository},
        {path: '/site/:site_id/files', component: SiteFiles},
        {path: '/site/:site_id/workers', component: SiteWorkers},
        {path: '/site/:site_id/framework-files', component: SiteFrameworkFiles},
        {path: '/site/:site_id/ssl-certificates', component: SiteSSLCertificates},

        {path: '/my-profile', component: UserInfo},
        {path: '/my-profile/ssh-keys', component: UserSshKeys},
        {path: '/my-profile/subscription', component: UserSubscription},
        {path: '/my-profile/server-providers', component: UserServerProviders},
        {path: '/my-profile/repository-providers', component: UserRepositoryProviders},
        {path: '/my-profile/notification-providers', component: UserNotificationProviders},
        {path: '/my-profile/oauth', component: UserOauth},

        {path: '/my/teams', component: Teams},
        {path: '/my/team/:team_id/members', component: TeamMembers},

        {path: '*', redirect: '/'}
    ]
});

const app = new Vue({
    router
}).$mount('#app-layout');

window.app = app;