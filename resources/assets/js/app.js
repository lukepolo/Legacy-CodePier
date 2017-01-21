/**
 * First we will load all of this project's JavaScript dependencies which
 * include Vue and Vue Resource. This gives a great starting point for
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap')

import * as buoyPages from './pages/buoy'
import * as userPages from './pages/user'
import * as teamPages from './pages/team'
import * as sitePages from './pages/site'
import * as adminPages from './pages/admin'
import * as serverPages from './pages/server'

import store from './store'
import Piles from './pages/pile/Piles.vue'
import PageNotFound from './core/PageNotFound.vue'

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the body of the page. From here, you may begin adding components to
 * the application, or feel free to tweak this setup for your needs.
 */

Vue.directive('file-editor', {
    bind: function (element, params) {
        const editor = ace.edit(element)

        editor.$blockScrolling = Infinity
        editor.getSession().setMode('ace/mode/sh')
        editor.setOption('maxLines', 45)
    }
})

Vue.directive('cronjob', {
    bind: function (el) {
        $(el).cron({
            onChange () {
                const cronTiming = $(this).cron('value')
                $('#cron-preview').text(cronTiming)
                $('input[name="cron_timing"]').val(cronTiming)
            }
        })
    }
})

Vue.mixin({
    methods: {
        now () {
            return moment()
        },
        parseDate: function (date, timezone) {
            if (timezone) {
                return moment(date).tz(timezone)
            }
            return moment(date)
        },
        dateHumanize: function (date, timezone) {
            return moment(date).tz(timezone).fromNow()
        },
        action: function (action, parameters) {
            return laroute.action(action, parameters)
        },
        // NOTE - this will not work with PUT!!!
        // https://github.com/symfony/symfony/issues/9226
        getFormData: function (form) {
            if (!$(form).is('form')) {
                form = $(form).find('form')[0]
            }

            return new FormData(form)
        },
        serverHasFeature: function (server, feature) {
            return _.get(server.server_features, feature, false)
        },
        isCommandRunning (type, modelId) {
            const commands = _.filter(this.$store.state.serversStore.running_commands[type], (command) => {
                return command.commandable_id === modelId && command.status !== 'Completed' && command.status !== 'Failed'
            })

            if (commands) {
                // we only need the first one, this allows us to not allow other changes
                // on the rest of the servers till they are all completed
                return commands[0]
            }
        },
        handleApiError (response) {
            let message = response

            if (_.isObject(response)) {
                if (_.isSet(response.errors)) {
                    message = response.errors
                } else if (_.isObject(response.data)) {
                    message = ''
                    _.each(response.data, function (error) {
                        message += error + '<br>'
                    })
                } else {
                    message = response.data
                }
            }

            if (_.isString(message)) {
                this.showError(message)
            } else {
                console.info('UNABLE TO PARSE ERROR')
                console.info(message)
            }
        },
        showError (message, title, timeout) {
            this.$store.dispatch('addNotification', {
                title: !_.isEmpty(title) ? title : 'Error!!',
                text: message,
                class: 'error',
                timeout: false
            })
        },
        showSuccess (message, title, timeout) {
            this.$store.dispatch('addNotification', {
                title: !_.isEmpty(title) ? title : 'Success!!',
                text: message,
                class: 'success',
                timeout: false
            })
        },
        back () {
            window.history.back()
        },
        getRepositoryProvider (providerId, attribute) {
            const provider = _.find(this.$store.state.userStore.repository_providers, { id: providerId })
            if (provider) {
                if (attribute) {
                    return provider[attribute]
                }
                return provider
            }
            return {}
        },
        getPile (pildId, attribute) {
            const pile = _.find(this.$store.state.pilesStore.all_user_piles, { id: pildId })
            if (pile) {
                if (attribute) {
                    return pile[attribute]
                }
                return pile
            }
            return {}
        },
        getSite (siteId, attribute) {
            const site = _.find(this.$store.state.sitesStore.all_sites, { id: siteId })
            if (site) {
                if (attribute) {
                    return site[attribute]
                }
                return site
            }
            return {}
        },
        getServer (serverId, attribute) {
            const server = _.find(this.$store.state.serversStore.all_servers, { id: serverId })
            if (server) {
                if (attribute) {
                    return server[attribute]
                }
                return server
            }
            return {}
        },
        timeAgo (time) {
            time = moment(time)
            const currentTime = moment()

            if (currentTime.diff(time, 'hour') < 5) {
                return time.fromNow()
            }

            return time.format('M-D-YY h:mm A')
        },
        isAdmin () {
            return this.$store.state.userStore.user.role === 'admin'
        }
    }
})

/*
 |--------------------------------------------------------------------------
 | Core
 |--------------------------------------------------------------------------
 |
 */

Vue.component('back', require('./core/Back.vue'))
Vue.component('Confirm', require('./components/Confirm.vue'))
Vue.component('Navigation', require('./core/Navigation.vue'))
Vue.component('NotificationBar', require('./core/NotificationBar.vue'))

const router = new VueRouter({
    mode: 'history',
    routes: [
    { path: '/', name: 'dashboard', component: Piles },
    { path: '/piles', name: 'piles', component: Piles },
    { path: '/buoys', name: 'buoys', component: buoyPages.BuoyMarketPlace },
    { path: '/servers', name: 'servers', component: serverPages.Servers },
    { path: '/my/teams', name: 'teams', component: teamPages.Teams },
    { path: '/my/team/:team_id/members', name: 'team_members', component: teamPages.TeamMembers },

    { path: '/server/create', name: 'server_form', component: serverPages.ServerForm },
    { path: '/server/create/:site/:type', name: 'server_form_with_site', component: serverPages.ServerForm },
        {
            path: '/server', component: serverPages.ServerArea,
            children: [
                {
                    path: ':server_id/sites',
                    name: 'server_sites',
                    components: {
                        default: serverPages.ServerSites,
                        nav: serverPages.ServerNav
                    }
                },
                {
                    path: ':server_id/files',
                    name: 'server_files',
                    components: {
                        default: serverPages.ServerFiles,
                        nav: serverPages.ServerNav
                    }
                },
                {
                    path: ':server_id/workers',
                    name: 'server_workers',
                    components: {
                        default: serverPages.ServerWorkers,
                        nav: serverPages.ServerNav
                    }
                },
                {
                    path: ':server_id/ssh-keys',
                    name: 'server_ssh_keys',
                    components: {
                        default: serverPages.ServerSshKeys,
                        nav: serverPages.ServerNav
                    }
                },
                {
                    path: ':server_id/features',
                    name: 'server_features',
                    components: {
                        default: serverPages.ServerFeatures,
                        nav: serverPages.ServerNav
                    }
                },
                {
                    path: ':server_id/cron-jobs',
                    name: 'server_cron_jobs',
                    components: {
                        default: serverPages.ServerCronJobs,
                        nav: serverPages.ServerNav
                    }
                },
                {
                    path: ':server_id/monitoring',
                    name: 'server_monitoring',
                    components: {
                        default: serverPages.ServerMonitoring,
                        nav: serverPages.ServerNav
                    }
                },
                {
                    path: ':server_id/firewall-rules',
                    name: 'server_firewall_rules',
                    components: {
                        default: serverPages.ServerFirewallRules,
                        nav: serverPages.ServerNav
                    }
                }
            ]
        },
        {
      // BUG with vue router https://github.com/vuejs/vue-router/issues/1091
            path: '/site/:site_id/config', component: sitePages.SiteArea,
            children: [
                {
                    path: '',
                    name: 'site_default',
                    components: {
                        default: sitePages.SiteRepository,
                        nav: sitePages.SiteNav,
                        subNav: sitePages.SiteSetupNav
                    }
                },
                {
                    path: 'setup',
                    name: 'site_repository',
                    components: {
                        default: sitePages.SiteRepository,
                        nav: sitePages.SiteNav,
                        subNav: sitePages.SiteSetupNav
                    }
                },
                {
                    path: 'setup/deployment',
                    name: 'site_deployment',
                    components: {
                        default: sitePages.SiteDeployment,
                        nav: sitePages.SiteNav,
                        subNav: sitePages.SiteSetupNav
                    }
                },
                {
                    path: 'setup/framework-files',
                    name: 'site_files',
                    components: {
                        default: sitePages.SiteFiles,
                        nav: sitePages.SiteNav,
                        subNav: sitePages.SiteSetupNav
                    }
                },
                {
                    path: 'security/firewall-rules',
                    name: 'site_firewall_rules',
                    components: {
                        default: sitePages.SiteFirewallRules,
                        nav: sitePages.SiteNav,
                        subNav: sitePages.SecurityNav
                    }
                },
                {
                    path: 'security',
                    name: 'site_ssh_keys',
                    components: {
                        default: sitePages.SiteSshKeys,
                        nav: sitePages.SiteNav,
                        subNav: sitePages.SecurityNav
                    }
                },
                {
                    path: 'security/ssl-certificates',
                    name: 'site_ssl_certs',
                    components: {
                        default: sitePages.SiteSSLCertificates,
                        nav: sitePages.SiteNav,
                        subNav: sitePages.SecurityNav
                    }
                },
                {
                    path: 'server-setup',
                    name: 'site_workers',
                    components: {
                        default: sitePages.SiteJobs,
                        nav: sitePages.SiteNav,
                        subNav: sitePages.ServerSetupNav
                    }
                },
                {
                    path: 'server-setup/server-files',
                    name: 'site_server_files',
                    components: {
                        default: sitePages.SiteServerFiles,
                        nav: sitePages.SiteNav,
                        subNav: sitePages.ServerSetupNav
                    }
                },
                {
                    path: 'server-setup/server-features',
                    name: 'site_server_features',
                    components: {
                        default: sitePages.SiteServerFeatures,
                        nav: sitePages.SiteNav,
                        subNav: sitePages.ServerSetupNav
                    }
                }
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
                }
            ]
        },
        {
            path: '/admin', component: adminPages.AdminArea,
            children: [
                {
                    path: 'categories',
                    name: 'categories',
                    components: {
                        default: adminPages.Categories
                    }
                },
                {
                    path: 'buoys',
                    name: 'buoy_form',
                    components: {
                        default: buoyPages.BuoyForm
                    }
                }
            ]
        },
    { path: '*', component: PageNotFound }
    ]
})

const app = new Vue({
    store,
    router
}).$mount('#app-layout')

window.app = app

app.$store.dispatch('getRunningCommands')
app.$store.dispatch('getRunningDeployments')

Echo.channel('app').listen('ReleasedNewVersion', (data) => {
    app.$store.dispatch('setVersion', data)
})
