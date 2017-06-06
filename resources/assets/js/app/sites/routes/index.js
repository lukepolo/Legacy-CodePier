import {
    SiteRepository,
    SiteDeployment,
    SiteFiles,
    SiteServerFiles

} from '../pages'

import {
    SiteNav,
    SiteArea,
    SecurityNav,
    SiteSetupNav,
    ServerSetupNav
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
} from '../../setup/pages'

export default [
    {
        path: '/site/:site_id', component: SiteArea,
        children: [
            {
                alias: '',
                path: 'setup',
                name: 'site_repository',
                components: {
                    default: SiteRepository,
                    nav: SiteNav,
                    subNav: SiteSetupNav
                }
            },
            {
                path: 'setup/deployment',
                name: 'site_deployment',
                components: {
                    default: SiteDeployment,
                    nav: SiteNav,
                    subNav: SiteSetupNav
                }
            },
            {
                path: 'setup/framework-files',
                name: 'site_files',
                components: {
                    default: SiteFiles,
                    nav: SiteNav,
                    subNav: SiteSetupNav
                }
            },
            {
                path: 'setup/databases',
                name: 'site_databases',
                components: {
                    default: Databases,
                    nav: SiteNav,
                    subNav: SiteSetupNav
                }
            },
            {
                path: 'security/firewall-rules',
                name: 'site_firewall_rules',
                components: {
                    default: FirewallRules,
                    nav: SiteNav,
                    subNav: SecurityNav
                }
            },
            {
                path: 'security',
                name: 'site_ssh_keys',
                components: {
                    default: SshKeys,
                    nav: SiteNav,
                    subNav: SecurityNav
                }
            },
            {
                path: 'security/ssl-certificates',
                name: 'site_ssl_certs',
                components: {
                    default: SslCertificates,
                    nav: SiteNav,
                    subNav: SecurityNav
                }
            },
            {
                path: 'server-setup',
                name: 'site_environment_variables',
                components: {
                    default: EnvironmentVariables,
                    nav: SiteNav,
                    subNav: ServerSetupNav
                }
            },
            {
                path: 'server-setup/cron-jobs',
                name: 'site_cron_jobs',
                components: {
                    default: CronJobs,
                    nav: SiteNav,
                    subNav: ServerSetupNav
                }
            },
            {
                path: 'server-setup/workers',
                name: 'site_workers',
                components: {
                    default: Workers,
                    nav: SiteNav,
                    subNav: ServerSetupNav
                }
            },
            {
                path: 'server-setup/server-files',
                name: 'site_server_files',
                components: {
                    default: SiteServerFiles,
                    nav: SiteNav,
                    subNav: ServerSetupNav
                }
            },
            {
                path: 'server-setup/language-settings',
                name: 'site_language_settings',
                components: {
                    default: LanguageSettings,
                    nav: SiteNav,
                    subNav: ServerSetupNav
                }
            },
            {
                path: 'server-setup/server-features',
                name: 'site_server_features',
                components: {
                    default: ServerFeatures,
                    nav: SiteNav,
                    subNav: ServerSetupNav
                }
            }
        ]
    }
]
