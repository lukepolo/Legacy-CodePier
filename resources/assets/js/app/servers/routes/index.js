import {
    Servers,
    ServerForm,
    ServerSites,
    ServerFiles,
    ServerBuoys,
    ServerMonitoring
} from '../pages'

import {
    ServerNav,
    ServerArea,
    SecurityNav,
    ServerSetupNav,
    ServerInformationNav
} from '../components'

import {
    SshKeys,
    Workers,
    CronJobs,
    Databases,
    FirewallRules,
    ServerFeatures,
    SslCertificates,
    LanguageSettings,
    EnvironmentVariables
} from '../../core/setup/pages'

export default [
    { path: '/servers', name: 'servers', component: Servers },
    { path: '/servers/archived', name: 'archived_servers', component: Servers },
    { path: '/server/create', name: 'server_form', component: ServerForm },
    { path: '/server/create/:site_id/:type', name: 'server_form_with_site', component: ServerForm },
    {
        path: '/server/:server_id', component: ServerArea,
        children: [
            {
                alias: '',
                path: 'info',
                name: 'server_sites',
                components: {
                    default: ServerSites,
                    nav: ServerNav,
                    subNav: ServerInformationNav
                }
            },
            {
                path: 'info/monitoring',
                name: 'server_monitoring',
                components: {
                    default: ServerMonitoring,
                    nav: ServerNav,
                    subNav: ServerInformationNav
                }
            },
            {
                path: 'info/databases',
                name: 'server_databases',
                components: {
                    default: Databases,
                    nav: ServerNav,
                    subNav: ServerInformationNav
                }
            },
            {
                path: 'info/buoys',
                name: 'server_buoys',
                components: {
                    default: ServerBuoys,
                    nav: ServerNav,
                    subNav: ServerInformationNav
                }
            },
            {
                path: 'security',
                name: 'server_ssh_keys',
                components: {
                    default: SshKeys,
                    nav: ServerNav,
                    subNav: SecurityNav
                }
            },
            {
                path: 'security/firewall-rules',
                name: 'server_firewall_rules',
                components: {
                    default: FirewallRules,
                    nav: ServerNav,
                    subNav: SecurityNav
                }
            },
            {
                path: 'security/ssl-certificates',
                name: 'server_ssl_certs',
                components: {
                    default: SslCertificates,
                    nav: ServerNav,
                    subNav: SecurityNav
                }
            },
            {
                path: 'setup',
                name: 'server_environment_variables',
                components: {
                    default: EnvironmentVariables,
                    nav: ServerNav,
                    subNav: ServerSetupNav
                }
            },
            {
                path: 'setup/cron-jobs',
                name: 'server_cron_jobs',
                components: {
                    default: CronJobs,
                    nav: ServerNav,
                    subNav: ServerSetupNav
                }
            },
            {
                path: 'setup/workers',
                name: 'server_workers',
                components: {
                    default: Workers,
                    nav: ServerNav,
                    subNav: ServerSetupNav
                }
            },
            {
                path: 'setup/files',
                name: 'server_files',
                components: {
                    default: ServerFiles,
                    nav: ServerNav,
                    subNav: ServerSetupNav
                }
            },
            {
                path: 'setup/language-settings',
                name: 'server_language_settings',
                components: {
                    default: LanguageSettings,
                    nav: ServerNav,
                    subNav: ServerSetupNav
                }
            },
            {
                path: 'setup/features',
                name: 'server_features',
                components: {
                    default: ServerFeatures,
                    nav: ServerNav,
                    subNav: ServerSetupNav
                }
            }
        ]
    }
]
