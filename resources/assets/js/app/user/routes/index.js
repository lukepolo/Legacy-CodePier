import {
    UserInfo,
    UserOauth,
    UserSshKeys,
    UserSubscription,
    UserServerProviders,
    UserRepositoryProviders,
    UserNotificationProviders
} from '../pages'

import {
    UserNav,
    UserArea
} from '../components'

export default [
    {
        path: '/my', component: UserArea,
        children: [
            {
                path: 'account',
                name: 'my_account',
                components: {
                    default: UserInfo,
                    nav: UserNav
                }
            },
            {
                path: 'account/oauth',
                name: 'oauth',
                components: {
                    default: UserOauth,
                    nav: UserNav
                }
            },
            {
                path: 'account/ssh-keys',
                name: 'user_ssh_keys',
                components: {
                    default: UserSshKeys,
                    nav: UserNav
                }
            },
            {
                path: 'account/subscription',
                name: 'subscription',
                components: {
                    default: UserSubscription,
                    nav: UserNav
                }
            },
            {
                path: 'server-providers',
                name: 'user_server_providers',
                components: {
                    default: UserServerProviders,
                    nav: UserNav
                }
            },
            {
                path: 'repository-providers',
                name: 'user_repository_providers',
                components: {
                    default: UserRepositoryProviders,
                    nav: UserNav
                }
            },
            {
                path: 'notification-providers',
                name: 'user_notification_providers',
                components: {
                    default: UserNotificationProviders,
                    nav: UserNav
                }
            }
        ]
    }
]
