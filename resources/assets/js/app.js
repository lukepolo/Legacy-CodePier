
require('./bootstrap')

import * as buoyPages from './pages/buoy'
import * as userPages from './pages/user'
import * as teamPages from './pages/team'
import * as sitePages from './pages/site'
import * as bittPages from './pages/bitts'
import * as adminPages from './pages/admin'
import * as serverPages from './pages/server'

import * as setupPages from './pages/setup'

import store from './store'
import Piles from './pages/pile/Piles.vue'
import PageNotFound from './core/PageNotFound.vue'

const filesizeParser = require('filesize-parser')

Vue.directive('file-editor', {
    bind: function (element, params) {
        const editor = ace.edit(element)

        editor.$blockScrolling = Infinity
        editor.getSession().setMode('ace/mode/sh')
        editor.setOption('maxLines', 45)
    }
})

Vue.directive('cronjob', {
    inserted: function (el) {
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
        teamsEnabled () {
            return Laravel.teams
        },
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
            if (timeout === undefined) {
                timeout = 5000
            }

            this.$store.dispatch('addNotification', {
                title: !_.isEmpty(title) ? title : 'Error!!',
                text: message,
                class: 'error',
                timeout: timeout
            })
        },
        showSuccess (message, title, timeout) {
            if (timeout === undefined) {
                timeout = 5000
            }

            this.$store.dispatch('addNotification', {
                title: !_.isEmpty(title) ? title : 'Success!!',
                text: message,
                class: 'success',
                timeout: timeout
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
            const pile = _.find(this.$store.state.pilesStore.all_user_piles, { id: parseInt(pildId) })
            if (pile) {
                if (attribute) {
                    return pile[attribute]
                }
                return pile
            }
            return {}
        },
        getSite (siteId, attribute) {
            const site = _.find(this.$store.state.sitesStore.all_sites, { id: parseInt(siteId) })
            if (site) {
                if (attribute) {
                    return site[attribute]
                }
                return site
            }
            return {}
        },
        getServer (serverId, attribute) {
            const server = _.find(this.$store.state.serversStore.all_servers, { id: parseInt(serverId) })
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
        },
        getBytesFromString (string) {
            return filesizeParser(string, { base: 10 })
        }
    }
})

/*
 |--------------------------------------------------------------------------
 | Core
 |--------------------------------------------------------------------------
 |
 */

Vue.component('Back', require('./core/Back.vue'))
Vue.component('Tooltip', require('./core/ToolTip.vue'))
Vue.component('Clipboard', require('./core/Clipboard.vue'))
Vue.component('Confirm', require('./components/Confirm.vue'))
Vue.component('ConfirmDropdown', require('./components/ConfirmDropdown.vue'))
Vue.component('Navigation', require('./core/Navigation.vue'))
Vue.component('NotificationBar', require('./core/NotificationBar.vue'))

const router = new VueRouter({
    mode: 'history',
    routes: [
    { path: '/', name: 'dashboard', component: Piles },
    { path: '/piles', name: 'piles', component: Piles },

        {
            path: '/bitts', component: bittPages.BittsArea,
            children: [
                {
                    path: '/',
                    name: 'bitts_market_place',
                    components: {
                        default: bittPages.BittsMarketPlace,
                        right: bittPages.BittInstall
                    }
                },
                {
                    path: 'create',
                    name: 'bitt_create',
                    components: {
                        default: bittPages.BittsForm
                    }
                },
                {
                    path: ':bitt_id/edit',
                    name: 'bitt_edit',
                    components: {
                        default: bittPages.BittsForm
                    }
                }
            ]
        },

        {
            path: '/buoys', component: buoyPages.BuoyArea,
            children: [
                {
                    path: '/',
                    name: 'buoy_market_place',
                    components: {
                        default: buoyPages.BuoyMarketPlace,
                        right: buoyPages.BuoyInstall
                    }
                }
            ]
        },

    { path: '/servers', name: 'servers', component: serverPages.Servers },
    { path: '/servers/archived', name: 'archived_servers', component: serverPages.Servers },
    { path: '/my/teams', name: 'teams', component: teamPages.Teams },
    { path: '/my/team/:team_id/members', name: 'team_members', component: teamPages.TeamMembers },

    { path: '/server/create', name: 'server_form', component: serverPages.ServerForm },
    { path: '/server/create/:site_id/:type', name: 'server_form_with_site', component: serverPages.ServerForm },
        {
            path: '/server/:server_id', component: serverPages.ServerArea,
            children: [
                {
                    path: '',
                    name: 'server_sites',
                    components: {
                        default: serverPages.ServerSites,
                        nav: serverPages.ServerNav,
                        subNav: serverPages.ServerInformationNav
                    }
                },
                {
                    path: 'monitoring',
                    name: 'server_monitoring',
                    components: {
                        default: serverPages.ServerMonitoring,
                        nav: serverPages.ServerNav,
                        subNav: serverPages.ServerInformationNav
                    }
                },
                {
                    path: 'buoys',
                    name: 'server_buoys',
                    components: {
                        default: serverPages.ServerBuoys,
                        nav: serverPages.ServerNav,
                        subNav: serverPages.ServerInformationNav
                    }
                },
                {
                    path: 'security',
                    name: 'server_ssh_keys',
                    components: {
                        default: setupPages.SshKeys,
                        nav: serverPages.ServerNav,
                        subNav: serverPages.SecurityNav
                    }
                },
                {
                    path: 'security/firewall-rules',
                    name: 'server_firewall_rules',
                    components: {
                        default: setupPages.FirewallRules,
                        nav: serverPages.ServerNav,
                        subNav: serverPages.SecurityNav
                    }
                },
                {
                    path: 'setup',
                    name: 'server_cron_jobs',
                    components: {
                        default: setupPages.CronJobs,
                        nav: serverPages.ServerNav,
                        subNav: serverPages.ServerSetupNav
                    }
                },
                {
                    path: 'setup/workers',
                    name: 'server_workers',
                    components: {
                        default: setupPages.Workers,
                        nav: serverPages.ServerNav,
                        subNav: serverPages.ServerSetupNav
                    }
                },
                {
                    path: 'setup/files',
                    name: 'server_files',
                    components: {
                        default: serverPages.ServerFiles,
                        nav: serverPages.ServerNav,
                        subNav: serverPages.ServerSetupNav
                    }
                },
                {
                    path: 'setup/features',
                    name: 'server_features',
                    components: {
                        default: serverPages.ServerFeatures,
                        nav: serverPages.ServerNav,
                        subNav: serverPages.ServerSetupNav
                    }
                }
            ]
        },
        {
            path: '/site/:site_id', component: sitePages.SiteArea,
            children: [
                {
                    alias: '',
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
                    path: 'setup/databases',
                    name: 'site_databases',
                    components: {
                        default: setupPages.Databases,
                        nav: sitePages.SiteNav,
                        subNav: sitePages.SiteSetupNav
                    }
                },
                {
                    path: 'security/firewall-rules',
                    name: 'site_firewall_rules',
                    components: {
                        default: setupPages.FirewallRules,
                        nav: sitePages.SiteNav,
                        subNav: sitePages.SecurityNav
                    }
                },
                {
                    path: 'security',
                    name: 'site_ssh_keys',
                    components: {
                        default: setupPages.SshKeys,
                        nav: sitePages.SiteNav,
                        subNav: sitePages.SecurityNav
                    }
                },
                {
                    path: 'security/ssl-certificates',
                    name: 'site_ssl_certs',
                    components: {
                        default: setupPages.SslCertificates,
                        nav: sitePages.SiteNav,
                        subNav: sitePages.SecurityNav
                    }
                },
                {
                    path: 'server-setup',
                    name: 'site_cron_jobs',
                    components: {
                        default: setupPages.CronJobs,
                        nav: sitePages.SiteNav,
                        subNav: sitePages.ServerSetupNav
                    }
                },
                {
                    path: 'server-setup/workers',
                    name: 'site_workers',
                    components: {
                        default: setupPages.Workers,
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
            path: '/my', component: userPages.UserArea,
            children: [
                {
                    path: 'account',
                    name: 'my_account',
                    components: {
                        default: userPages.UserInfo,
                        nav: userPages.UserNav
                    }
                },
                {
                    path: 'account/oauth',
                    name: 'oauth',
                    components: {
                        default: userPages.UserOauth,
                        nav: userPages.UserNav
                    }
                },
                {
                    path: 'account/ssh-keys',
                    name: 'user_ssh_keys',
                    components: {
                        default: userPages.UserSshKeys,
                        nav: userPages.UserNav
                    }
                },
                {
                    path: 'account/subscription',
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
                    path: 'categories/create',
                    name: 'category_create',
                    components: {
                        default: adminPages.CategoryForm
                    }
                },
                {
                    path: 'categories/edit/:category_id',
                    name: 'category_edit',
                    components: {
                        default: adminPages.CategoryForm
                    }
                },
                {
                    path: 'buoys/edit/:buoy_id',
                    name: 'buoy_edit',
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
