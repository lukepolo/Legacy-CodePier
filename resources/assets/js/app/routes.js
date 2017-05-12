import admin from './admin/routes'
import bitts from './bitts/routes'
import buoy from './buoy/routes'
import piles from './piles/routes'

import {Piles} from './piles/components/index'
import PageNotFound from './core/PageNotFound.vue'

export default [
    { path: '/', name: 'dashboard', component: Piles },

    ... admin,
    ... bitts,
    ... buoy,
    ... piles,


    { path: '*', component: PageNotFound }
]

//     { path: '/servers', name: 'servers', component: serverPages.Servers },
//     { path: '/servers/archived', name: 'archived_servers', component: serverPages.Servers },
//     { path: '/my/teams', name: 'teams', component: teamPages.Teams },
//     { path: '/my/team/:team_id/members', name: 'team_members', component: teamPages.TeamMembers },
//
//     { path: '/server/create', name: 'server_form', component: serverPages.ServerForm },
//     { path: '/server/create/:site_id/:type', name: 'server_form_with_site', component: serverPages.ServerForm },
//         {
//             path: '/server/:server_id', component: serverPages.ServerArea,
//             children: [
//                 {
//                     alias: '',
//                     path: 'info',
//                     name: 'server_sites',
//                     components: {
//                         default: serverPages.ServerSites,
//                         nav: serverPages.ServerNav,
//                         subNav: serverPages.ServerInformationNav
//                     }
//                 },
//                 {
//                     path: 'info/monitoring',
//                     name: 'server_monitoring',
//                     components: {
//                         default: serverPages.ServerMonitoring,
//                         nav: serverPages.ServerNav,
//                         subNav: serverPages.ServerInformationNav
//                     }
//                 },
//                 {
//                     path: 'info/databases',
//                     name: 'server_databases',
//                     components: {
//                         default: setupPages.Databases,
//                         nav: serverPages.ServerNav,
//                         subNav: serverPages.ServerInformationNav
//                     }
//                 },
//                 {
//                     path: 'info/buoys',
//                     name: 'server_buoys',
//                     components: {
//                         default: serverPages.ServerBuoys,
//                         nav: serverPages.ServerNav,
//                         subNav: serverPages.ServerInformationNav
//                     }
//                 },
//                 {
//                     path: 'security',
//                     name: 'server_ssh_keys',
//                     components: {
//                         default: setupPages.SshKeys,
//                         nav: serverPages.ServerNav,
//                         subNav: serverPages.SecurityNav
//                     }
//                 },
//                 {
//                     path: 'security/firewall-rules',
//                     name: 'server_firewall_rules',
//                     components: {
//                         default: setupPages.FirewallRules,
//                         nav: serverPages.ServerNav,
//                         subNav: serverPages.SecurityNav
//                     }
//                 },
//                 {
//                     path: 'security/ssl-certificates',
//                     name: 'server_ssl_certs',
//                     components: {
//                         default: setupPages.SslCertificates,
//                         nav: serverPages.ServerNav,
//                         subNav: serverPages.SecurityNav
//                     }
//                 },
//                 {
//                     path: 'setup',
//                     name: 'server_environment_variables',
//                     components: {
//                         default: setupPages.EnvironmentVariables,
//                         nav: serverPages.ServerNav,
//                         subNav: serverPages.ServerSetupNav
//                     }
//                 },
//                 {
//                     path: 'setup/cron-jobs',
//                     name: 'server_cron_jobs',
//                     components: {
//                         default: setupPages.CronJobs,
//                         nav: serverPages.ServerNav,
//                         subNav: serverPages.ServerSetupNav
//                     }
//                 },
//                 {
//                     path: 'setup/workers',
//                     name: 'server_workers',
//                     components: {
//                         default: setupPages.Workers,
//                         nav: serverPages.ServerNav,
//                         subNav: serverPages.ServerSetupNav
//                     }
//                 },
//                 {
//                     path: 'setup/files',
//                     name: 'server_files',
//                     components: {
//                         default: serverPages.ServerFiles,
//                         nav: serverPages.ServerNav,
//                         subNav: serverPages.ServerSetupNav
//                     }
//                 },
//                 {
//                     path: 'setup/language-settings',
//                     name: 'server_language_settings',
//                     components: {
//                         default: setupPages.LanguageSettings,
//                         nav: serverPages.ServerNav,
//                         subNav: serverPages.ServerSetupNav
//                     }
//                 },
//                 {
//                     path: 'setup/features',
//                     name: 'server_features',
//                     components: {
//                         default: setupPages.ServerFeatures,
//                         nav: serverPages.ServerNav,
//                         subNav: serverPages.ServerSetupNav
//                     }
//                 }
//             ]
//         },
//         {
//             path: '/site/:site_id', component: sitePages.SiteArea,
//             children: [
//                 {
//                     alias: '',
//                     path: 'setup',
//                     name: 'site_repository',
//                     components: {
//                         default: sitePages.SiteRepository,
//                         nav: sitePages.SiteNav,
//                         subNav: sitePages.SiteSetupNav
//                     }
//                 },
//                 {
//                     path: 'setup/deployment',
//                     name: 'site_deployment',
//                     components: {
//                         default: sitePages.SiteDeployment,
//                         nav: sitePages.SiteNav,
//                         subNav: sitePages.SiteSetupNav
//                     }
//                 },
//                 {
//                     path: 'setup/framework-files',
//                     name: 'site_files',
//                     components: {
//                         default: sitePages.SiteFiles,
//                         nav: sitePages.SiteNav,
//                         subNav: sitePages.SiteSetupNav
//                     }
//                 },
//                 {
//                     path: 'setup/databases',
//                     name: 'site_databases',
//                     components: {
//                         default: setupPages.Databases,
//                         nav: sitePages.SiteNav,
//                         subNav: sitePages.SiteSetupNav
//                     }
//                 },
//                 {
//                     path: 'security/firewall-rules',
//                     name: 'site_firewall_rules',
//                     components: {
//                         default: setupPages.FirewallRules,
//                         nav: sitePages.SiteNav,
//                         subNav: sitePages.SecurityNav
//                     }
//                 },
//                 {
//                     path: 'security',
//                     name: 'site_ssh_keys',
//                     components: {
//                         default: setupPages.SshKeys,
//                         nav: sitePages.SiteNav,
//                         subNav: sitePages.SecurityNav
//                     }
//                 },
//                 {
//                     path: 'security/ssl-certificates',
//                     name: 'site_ssl_certs',
//                     components: {
//                         default: setupPages.SslCertificates,
//                         nav: sitePages.SiteNav,
//                         subNav: sitePages.SecurityNav
//                     }
//                 },
//                 {
//                     path: 'server-setup',
//                     name: 'site_environment_variables',
//                     components: {
//                         default: setupPages.EnvironmentVariables,
//                         nav: sitePages.SiteNav,
//                         subNav: sitePages.ServerSetupNav
//                     }
//                 },
//                 {
//                     path: 'server-setup/cron-jobs',
//                     name: 'site_cron_jobs',
//                     components: {
//                         default: setupPages.CronJobs,
//                         nav: sitePages.SiteNav,
//                         subNav: sitePages.ServerSetupNav
//                     }
//                 },
//                 {
//                     path: 'server-setup/workers',
//                     name: 'site_workers',
//                     components: {
//                         default: setupPages.Workers,
//                         nav: sitePages.SiteNav,
//                         subNav: sitePages.ServerSetupNav
//                     }
//                 },
//                 {
//                     path: 'server-setup/server-files',
//                     name: 'site_server_files',
//                     components: {
//                         default: sitePages.SiteServerFiles,
//                         nav: sitePages.SiteNav,
//                         subNav: sitePages.ServerSetupNav
//                     }
//                 },
//                 {
//                     path: 'server-setup/language-settings',
//                     name: 'site_language_settings',
//                     components: {
//                         default: setupPages.LanguageSettings,
//                         nav: sitePages.SiteNav,
//                         subNav: sitePages.ServerSetupNav
//                     }
//                 },
//                 {
//                     path: 'server-setup/server-features',
//                     name: 'site_server_features',
//                     components: {
//                         default: setupPages.ServerFeatures,
//                         nav: sitePages.SiteNav,
//                         subNav: sitePages.ServerSetupNav
//                     }
//                 }
//             ]
//         },
//         {
//             path: '/my', component: userPages.UserArea,
//             children: [
//                 {
//                     path: 'account',
//                     name: 'my_account',
//                     components: {
//                         default: userPages.UserInfo,
//                         nav: userPages.UserNav
//                     }
//                 },
//                 {
//                     path: 'account/oauth',
//                     name: 'oauth',
//                     components: {
//                         default: userPages.UserOauth,
//                         nav: userPages.UserNav
//                     }
//                 },
//                 {
//                     path: 'account/ssh-keys',
//                     name: 'user_ssh_keys',
//                     components: {
//                         default: userPages.UserSshKeys,
//                         nav: userPages.UserNav
//                     }
//                 },
//                 {
//                     path: 'account/subscription',
//                     name: 'subscription',
//                     components: {
//                         default: userPages.UserSubscription,
//                         nav: userPages.UserNav
//                     }
//                 },
//                 {
//                     path: 'server-providers',
//                     name: 'user_server_providers',
//                     components: {
//                         default: userPages.UserServerProviders,
//                         nav: userPages.UserNav
//                     }
//                 },
//                 {
//                     path: 'repository-providers',
//                     name: 'user_repository_providers',
//                     components: {
//                         default: userPages.UserRepositoryProviders,
//                         nav: userPages.UserNav
//                     }
//                 },
//                 {
//                     path: 'notification-providers',
//                     name: 'user_notification_providers',
//                     components: {
//                         default: userPages.UserNotificationProviders,
//                         nav: userPages.UserNav
//                     }
//                 }
//             ]
//         },
