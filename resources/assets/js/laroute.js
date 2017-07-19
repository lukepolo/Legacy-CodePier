(function() {
    var laroute = function() {
        var routes = {
            absolute: false,
            rootUrl: 'http://codepier.dev',
            routes: [
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'oauth/authorize',
                    name: null,
                    action:
                        'LaravelPassportHttpControllersAuthorizationController@authorize',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'oauth/authorize',
                    name: null,
                    action:
                        'LaravelPassportHttpControllersApproveAuthorizationController@approve',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri: 'oauth/authorize',
                    name: null,
                    action:
                        'LaravelPassportHttpControllersDenyAuthorizationController@deny',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'oauth/token',
                    name: null,
                    action:
                        'LaravelPassportHttpControllersAccessTokenController@issueToken',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'oauth/tokens',
                    name: null,
                    action:
                        'LaravelPassportHttpControllersAuthorizedAccessTokenController@forUser',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri: 'oauth/tokens/{token_id}',
                    name: null,
                    action:
                        'LaravelPassportHttpControllersAuthorizedAccessTokenController@destroy',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'oauth/token/refresh',
                    name: null,
                    action:
                        'LaravelPassportHttpControllersTransientTokenController@refresh',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'oauth/clients',
                    name: null,
                    action:
                        'LaravelPassportHttpControllersClientController@forUser',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'oauth/clients',
                    name: null,
                    action:
                        'LaravelPassportHttpControllersClientController@store',
                },
                {
                    host: null,
                    methods: ['PUT'],
                    uri: 'oauth/clients/{client_id}',
                    name: null,
                    action:
                        'LaravelPassportHttpControllersClientController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri: 'oauth/clients/{client_id}',
                    name: null,
                    action:
                        'LaravelPassportHttpControllersClientController@destroy',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'oauth/scopes',
                    name: null,
                    action: 'LaravelPassportHttpControllersScopeController@all',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'oauth/personal-access-tokens',
                    name: null,
                    action:
                        'LaravelPassportHttpControllersPersonalAccessTokenController@forUser',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'oauth/personal-access-tokens',
                    name: null,
                    action:
                        'LaravelPassportHttpControllersPersonalAccessTokenController@store',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri: 'oauth/personal-access-tokens/{token_id}',
                    name: null,
                    action:
                        'LaravelPassportHttpControllersPersonalAccessTokenController@destroy',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'broadcasting/auth',
                    name: null,
                    action:
                        'IlluminateBroadcastingBroadcastController@authenticate',
                },
                {
                    host: 'provision.codepier.dev',
                    methods: ['GET', 'HEAD'],
                    uri: '/',
                    name: null,
                    action:
                        'ServerCustomServerProvisioningController@provision',
                },
                {
                    host: 'provision.codepier.dev',
                    methods: ['POST'],
                    uri: 'start/{provisioning_key}',
                    name: null,
                    action: 'ServerCustomServerProvisioningController@start',
                },
                {
                    host: 'provision.codepier.dev',
                    methods: ['GET', 'HEAD'],
                    uri: 'keys/{provisioning_key}/public',
                    name: null,
                    action:
                        'ServerCustomServerProvisioningController@returnPublicKey',
                },
                {
                    host: 'provision.codepier.dev',
                    methods: ['GET', 'HEAD'],
                    uri: 'keys/{provisioning_key/private}',
                    name: null,
                    action:
                        'ServerCustomServerProvisioningController@returnPrivateKey',
                },
                {
                    host: 'provision.codepier.dev',
                    methods: ['GET', 'HEAD'],
                    uri: 'end/{provisioning_key}',
                    name: null,
                    action: 'ServerCustomServerProvisioningController@end',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/2fa',
                    name: '2fa.index',
                    action: 'AuthSecondAuthController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/2fa',
                    name: '2fa.store',
                    action: 'AuthSecondAuthController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/2fa/{fa}',
                    name: '2fa.show',
                    action: 'AuthSecondAuthController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri: 'api/2fa/{fa}',
                    name: '2fa.update',
                    action: 'AuthSecondAuthController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri: 'api/2fa/{fa}',
                    name: '2fa.destroy',
                    action: 'AuthSecondAuthController@destroy',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/me',
                    name: 'me.index',
                    action: 'UserUserController@index',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/user',
                    name: 'user.index',
                    action: 'UserUserController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/user',
                    name: 'user.store',
                    action: 'UserUserController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/user/{user}',
                    name: 'user.show',
                    action: 'UserUserController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri: 'api/user/{user}',
                    name: 'user.update',
                    action: 'UserUserController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri: 'api/user/{user}',
                    name: 'user.destroy',
                    action: 'UserUserController@destroy',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/categories',
                    name: 'categories.index',
                    action: 'CategoriesController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/categories',
                    name: 'categories.store',
                    action: 'CategoriesController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/categories/{category}',
                    name: 'categories.show',
                    action: 'CategoriesController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri: 'api/categories/{category}',
                    name: 'categories.update',
                    action: 'CategoriesController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri: 'api/categories/{category}',
                    name: 'categories.destroy',
                    action: 'CategoriesController@destroy',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/buoy-apps/buoyClasses',
                    name: null,
                    action: 'BuoyAppController@getBuoyClasses',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/buoy-apps/{buoy_app}/update',
                    name: null,
                    action: 'BuoyAppController@update',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/buoy-apps',
                    name: 'buoy-apps.index',
                    action: 'BuoyAppController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/buoy-apps',
                    name: 'buoy-apps.store',
                    action: 'BuoyAppController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/buoy-apps/{buoy_app}',
                    name: 'buoy-apps.show',
                    action: 'BuoyAppController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri: 'api/buoy-apps/{buoy_app}',
                    name: 'buoy-apps.update',
                    action: 'BuoyAppController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri: 'api/buoy-apps/{buoy_app}',
                    name: 'buoy-apps.destroy',
                    action: 'BuoyAppController@destroy',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/bitts',
                    name: 'bitts.index',
                    action: 'BittsController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/bitts',
                    name: 'bitts.store',
                    action: 'BittsController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/bitts/{bitt}',
                    name: 'bitts.show',
                    action: 'BittsController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri: 'api/bitts/{bitt}',
                    name: 'bitts.update',
                    action: 'BittsController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri: 'api/bitts/{bitt}',
                    name: 'bitts.destroy',
                    action: 'BittsController@destroy',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/bitt/{bitt}/run',
                    name: null,
                    action: 'BittsController@runOnServers',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/systems',
                    name: null,
                    action: 'SystemsController@index',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/subscription/invoices',
                    name: 'invoices.index',
                    action:
                        'UserSubscriptionUserSubscriptionInvoiceController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/subscription/invoices',
                    name: 'invoices.store',
                    action:
                        'UserSubscriptionUserSubscriptionInvoiceController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/subscription/invoices/{invoice}',
                    name: 'invoices.show',
                    action:
                        'UserSubscriptionUserSubscriptionInvoiceController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri: 'api/my/subscription/invoices/{invoice}',
                    name: 'invoices.update',
                    action:
                        'UserSubscriptionUserSubscriptionInvoiceController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri: 'api/my/subscription/invoices/{invoice}',
                    name: 'invoices.destroy',
                    action:
                        'UserSubscriptionUserSubscriptionInvoiceController@destroy',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/running-commands',
                    name: null,
                    action: 'UserUserController@getRunningCommands',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/running-deployments',
                    name: null,
                    action: 'UserUserController@getRunningDeployments',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/ssh-keys',
                    name: 'ssh-keys.index',
                    action: 'UserUserSshKeyController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/ssh-keys',
                    name: 'ssh-keys.store',
                    action: 'UserUserSshKeyController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/ssh-keys/{ssh_key}',
                    name: 'ssh-keys.show',
                    action: 'UserUserSshKeyController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri: 'api/my/ssh-keys/{ssh_key}',
                    name: 'ssh-keys.update',
                    action: 'UserUserSshKeyController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri: 'api/my/ssh-keys/{ssh_key}',
                    name: 'ssh-keys.destroy',
                    action: 'UserUserSshKeyController@destroy',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/subscription',
                    name: 'subscription.index',
                    action: 'UserSubscriptionUserSubscriptionController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/subscription',
                    name: 'subscription.store',
                    action: 'UserSubscriptionUserSubscriptionController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/subscription/{subscription}',
                    name: 'subscription.show',
                    action: 'UserSubscriptionUserSubscriptionController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri: 'api/my/subscription/{subscription}',
                    name: 'subscription.update',
                    action: 'UserSubscriptionUserSubscriptionController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri: 'api/my/subscription/{subscription}',
                    name: 'subscription.destroy',
                    action:
                        'UserSubscriptionUserSubscriptionController@destroy',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/server-providers',
                    name: 'server-providers.index',
                    action: 'UserProvidersUserServerProviderController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/server-providers',
                    name: 'server-providers.store',
                    action: 'UserProvidersUserServerProviderController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/server-providers/{server_provider}',
                    name: 'server-providers.show',
                    action: 'UserProvidersUserServerProviderController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri: 'api/my/server-providers/{server_provider}',
                    name: 'server-providers.update',
                    action: 'UserProvidersUserServerProviderController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri: 'api/my/server-providers/{server_provider}',
                    name: 'server-providers.destroy',
                    action: 'UserProvidersUserServerProviderController@destroy',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/notification-settings',
                    name: 'notification-settings.index',
                    action: 'UserUserNotificationSettingsController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/notification-settings',
                    name: 'notification-settings.store',
                    action: 'UserUserNotificationSettingsController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/notification-settings/{notification_setting}',
                    name: 'notification-settings.show',
                    action: 'UserUserNotificationSettingsController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri: 'api/my/notification-settings/{notification_setting}',
                    name: 'notification-settings.update',
                    action: 'UserUserNotificationSettingsController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri: 'api/my/notification-settings/{notification_setting}',
                    name: 'notification-settings.destroy',
                    action: 'UserUserNotificationSettingsController@destroy',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/repository-providers',
                    name: 'repository-providers.index',
                    action:
                        'UserProvidersUserRepositoryProviderController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/repository-providers',
                    name: 'repository-providers.store',
                    action:
                        'UserProvidersUserRepositoryProviderController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/repository-providers/{repository_provider}',
                    name: 'repository-providers.show',
                    action:
                        'UserProvidersUserRepositoryProviderController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri: 'api/my/repository-providers/{repository_provider}',
                    name: 'repository-providers.update',
                    action:
                        'UserProvidersUserRepositoryProviderController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri: 'api/my/repository-providers/{repository_provider}',
                    name: 'repository-providers.destroy',
                    action:
                        'UserProvidersUserRepositoryProviderController@destroy',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/notification-providers',
                    name: 'notification-providers.index',
                    action:
                        'UserProvidersUserNotificationProviderController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/notification-providers',
                    name: 'notification-providers.store',
                    action:
                        'UserProvidersUserNotificationProviderController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri:
                        'api/my/notification-providers/{notification_provider}',
                    name: 'notification-providers.show',
                    action:
                        'UserProvidersUserNotificationProviderController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri:
                        'api/my/notification-providers/{notification_provider}',
                    name: 'notification-providers.update',
                    action:
                        'UserProvidersUserNotificationProviderController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri:
                        'api/my/notification-providers/{notification_provider}',
                    name: 'notification-providers.destroy',
                    action:
                        'UserProvidersUserNotificationProviderController@destroy',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/subscription/invoice/next',
                    name: 'next.index',
                    action:
                        'UserSubscriptionUserSubscriptionUpcomingInvoiceController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/subscription/invoice/next',
                    name: 'next.store',
                    action:
                        'UserSubscriptionUserSubscriptionUpcomingInvoiceController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/subscription/invoice/next/{next}',
                    name: 'next.show',
                    action:
                        'UserSubscriptionUserSubscriptionUpcomingInvoiceController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri: 'api/my/subscription/invoice/next/{next}',
                    name: 'next.update',
                    action:
                        'UserSubscriptionUserSubscriptionUpcomingInvoiceController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri: 'api/my/subscription/invoice/next/{next}',
                    name: 'next.destroy',
                    action:
                        'UserSubscriptionUserSubscriptionUpcomingInvoiceController@destroy',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/events',
                    name: 'events.index',
                    action: 'EventController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/events',
                    name: 'events.store',
                    action: 'EventController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/events/{event}',
                    name: 'events.show',
                    action: 'EventController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri: 'api/my/events/{event}',
                    name: 'events.update',
                    action: 'EventController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri: 'api/my/events/{event}',
                    name: 'events.destroy',
                    action: 'EventController@destroy',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/team',
                    name: 'team.index',
                    action: 'UserTeamUserTeamController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/team',
                    name: 'team.store',
                    action: 'UserTeamUserTeamController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/team/{team}',
                    name: 'team.show',
                    action: 'UserTeamUserTeamController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri: 'api/my/team/{team}',
                    name: 'team.update',
                    action: 'UserTeamUserTeamController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri: 'api/my/team/{team}',
                    name: 'team.destroy',
                    action: 'UserTeamUserTeamController@destroy',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/team/team/{team}/members',
                    name: 'team.members.index',
                    action: 'UserTeamUserTeamMemberController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/team/team/{team}/members',
                    name: 'team.members.store',
                    action: 'UserTeamUserTeamMemberController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/team/team/{team}/members/{member}',
                    name: 'team.members.show',
                    action: 'UserTeamUserTeamMemberController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri: 'api/my/team/team/{team}/members/{member}',
                    name: 'team.members.update',
                    action: 'UserTeamUserTeamMemberController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri: 'api/my/team/team/{team}/members/{member}',
                    name: 'team.members.destroy',
                    action: 'UserTeamUserTeamMemberController@destroy',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/team/switch/{id?}',
                    name: 'teams.switch',
                    action: 'UserTeamUserTeamController@switchTeam',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/team/members',
                    name: 'teams.members.invite',
                    action: 'UserTeamUserTeamMemberController@invite',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/team/members/resend/{invite_id}',
                    name: 'teams.members.resend_invite',
                    action: 'UserTeamUserTeamMemberController@resendInvite',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/piles/all',
                    name: null,
                    action: 'PilePileController@allPiles',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/piles',
                    name: 'piles.index',
                    action: 'PilePileController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/piles',
                    name: 'piles.store',
                    action: 'PilePileController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/piles/{pile}',
                    name: 'piles.show',
                    action: 'PilePileController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri: 'api/my/piles/{pile}',
                    name: 'piles.update',
                    action: 'PilePileController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri: 'api/my/piles/{pile}',
                    name: 'piles.destroy',
                    action: 'PilePileController@destroy',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/pile/{pile}/sites',
                    name: 'pile.sites.index',
                    action: 'PilePileSitesController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/pile/{pile}/sites',
                    name: 'pile.sites.store',
                    action: 'PilePileSitesController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/pile/{pile}/sites/{site}',
                    name: 'pile.sites.show',
                    action: 'PilePileSitesController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri: 'api/my/pile/{pile}/sites/{site}',
                    name: 'pile.sites.update',
                    action: 'PilePileSitesController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri: 'api/my/pile/{pile}/sites/{site}',
                    name: 'pile.sites.destroy',
                    action: 'PilePileSitesController@destroy',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/change-pile',
                    name: null,
                    action: 'PilePileController@changePile',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/servers',
                    name: 'servers.index',
                    action: 'ServerServerController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/servers',
                    name: 'servers.store',
                    action: 'ServerServerController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/servers/{server}',
                    name: 'servers.show',
                    action: 'ServerServerController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri: 'api/my/servers/{server}',
                    name: 'servers.update',
                    action: 'ServerServerController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri: 'api/my/servers/{server}',
                    name: 'servers.destroy',
                    action: 'ServerServerController@destroy',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/server/{server}/find-file',
                    name: null,
                    action: 'ServerServerFileController@find',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/server/{server}/reload-file/{file}',
                    name: null,
                    action: 'ServerServerFileController@reloadFile',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/server/{server}/custom',
                    name: null,
                    action: 'ServerServerController@generateCustomServerSh',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/all_servers/buoys',
                    name: null,
                    action: 'ServerServerBuoyController@myServerBuoys',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/server/{server}/file',
                    name: null,
                    action: 'ServerServerController@getFile',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/server/restore/{server}',
                    name: null,
                    action: 'ServerServerController@restore',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/server/{server}/file/save',
                    name: null,
                    action: 'ServerServerController@saveFile',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/server/disk-space/{server}',
                    name: null,
                    action: 'ServerServerController@getDiskSpace',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/server/restart-server/{server}',
                    name: null,
                    action: 'ServerServerController@restartServer',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/server/restart-database/{server}',
                    name: null,
                    action: 'ServerServerController@restartDatabases',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/server/restart-workers/{server}',
                    name: null,
                    action: 'ServerServerController@restartWorkerServices',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/server/ssh-connection/{server}',
                    name: null,
                    action: 'ServerServerSshKeyController@testSSHConnection',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/server/restart-web-services/{server}',
                    name: null,
                    action: 'ServerServerController@restartWebServices',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/servers/{server}/file',
                    name: 'servers.file.index',
                    action: 'ServerServerFileController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/servers/{server}/file',
                    name: 'servers.file.store',
                    action: 'ServerServerFileController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/servers/{server}/file/{file}',
                    name: 'servers.file.show',
                    action: 'ServerServerFileController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri: 'api/my/servers/{server}/file/{file}',
                    name: 'servers.file.update',
                    action: 'ServerServerFileController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri: 'api/my/servers/{server}/file/{file}',
                    name: 'servers.file.destroy',
                    action: 'ServerServerFileController@destroy',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/servers/{server}/buoys',
                    name: 'servers.buoys.index',
                    action: 'ServerServerBuoyController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/servers/{server}/buoys',
                    name: 'servers.buoys.store',
                    action: 'ServerServerBuoyController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/servers/{server}/buoys/{buoy}',
                    name: 'servers.buoys.show',
                    action: 'ServerServerBuoyController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri: 'api/my/servers/{server}/buoys/{buoy}',
                    name: 'servers.buoys.update',
                    action: 'ServerServerBuoyController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri: 'api/my/servers/{server}/buoys/{buoy}',
                    name: 'servers.buoys.destroy',
                    action: 'ServerServerBuoyController@destroy',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/servers/{server}/sites',
                    name: 'servers.sites.index',
                    action: 'ServerServerSiteController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/servers/{server}/sites',
                    name: 'servers.sites.store',
                    action: 'ServerServerSiteController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/servers/{server}/sites/{site}',
                    name: 'servers.sites.show',
                    action: 'ServerServerSiteController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri: 'api/my/servers/{server}/sites/{site}',
                    name: 'servers.sites.update',
                    action: 'ServerServerSiteController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri: 'api/my/servers/{server}/sites/{site}',
                    name: 'servers.sites.destroy',
                    action: 'ServerServerSiteController@destroy',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/servers/{server}/workers',
                    name: 'servers.workers.index',
                    action: 'ServerServerWorkerController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/servers/{server}/workers',
                    name: 'servers.workers.store',
                    action: 'ServerServerWorkerController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/servers/{server}/workers/{worker}',
                    name: 'servers.workers.show',
                    action: 'ServerServerWorkerController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri: 'api/my/servers/{server}/workers/{worker}',
                    name: 'servers.workers.update',
                    action: 'ServerServerWorkerController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri: 'api/my/servers/{server}/workers/{worker}',
                    name: 'servers.workers.destroy',
                    action: 'ServerServerWorkerController@destroy',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/servers/{server}/schemas',
                    name: 'servers.schemas.index',
                    action: 'ServerServerSchemaController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/servers/{server}/schemas',
                    name: 'servers.schemas.store',
                    action: 'ServerServerSchemaController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/servers/{server}/schemas/{schema}',
                    name: 'servers.schemas.show',
                    action: 'ServerServerSchemaController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri: 'api/my/servers/{server}/schemas/{schema}',
                    name: 'servers.schemas.update',
                    action: 'ServerServerSchemaController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri: 'api/my/servers/{server}/schemas/{schema}',
                    name: 'servers.schemas.destroy',
                    action: 'ServerServerSchemaController@destroy',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/servers/{server}/ssh-keys',
                    name: 'servers.ssh-keys.index',
                    action: 'ServerServerSshKeyController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/servers/{server}/ssh-keys',
                    name: 'servers.ssh-keys.store',
                    action: 'ServerServerSshKeyController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/servers/{server}/ssh-keys/{ssh_key}',
                    name: 'servers.ssh-keys.show',
                    action: 'ServerServerSshKeyController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri: 'api/my/servers/{server}/ssh-keys/{ssh_key}',
                    name: 'servers.ssh-keys.update',
                    action: 'ServerServerSshKeyController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri: 'api/my/servers/{server}/ssh-keys/{ssh_key}',
                    name: 'servers.ssh-keys.destroy',
                    action: 'ServerServerSshKeyController@destroy',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/servers/{server}/features',
                    name: 'servers.features.index',
                    action: 'ServerServerFeatureController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/servers/{server}/features',
                    name: 'servers.features.store',
                    action: 'ServerServerFeatureController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/servers/{server}/features/{feature}',
                    name: 'servers.features.show',
                    action: 'ServerServerFeatureController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri: 'api/my/servers/{server}/features/{feature}',
                    name: 'servers.features.update',
                    action: 'ServerServerFeatureController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri: 'api/my/servers/{server}/features/{feature}',
                    name: 'servers.features.destroy',
                    action: 'ServerServerFeatureController@destroy',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/servers/{server}/cron-jobs',
                    name: 'servers.cron-jobs.index',
                    action: 'ServerServerCronJobController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/servers/{server}/cron-jobs',
                    name: 'servers.cron-jobs.store',
                    action: 'ServerServerCronJobController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/servers/{server}/cron-jobs/{cron_job}',
                    name: 'servers.cron-jobs.show',
                    action: 'ServerServerCronJobController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri: 'api/my/servers/{server}/cron-jobs/{cron_job}',
                    name: 'servers.cron-jobs.update',
                    action: 'ServerServerCronJobController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri: 'api/my/servers/{server}/cron-jobs/{cron_job}',
                    name: 'servers.cron-jobs.destroy',
                    action: 'ServerServerCronJobController@destroy',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/servers/{server}/ssl-certificate',
                    name: 'servers.ssl-certificate.index',
                    action: 'ServerServerSslController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/servers/{server}/ssl-certificate',
                    name: 'servers.ssl-certificate.store',
                    action: 'ServerServerSslController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri:
                        'api/my/servers/{server}/ssl-certificate/{ssl_certificate}',
                    name: 'servers.ssl-certificate.show',
                    action: 'ServerServerSslController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri:
                        'api/my/servers/{server}/ssl-certificate/{ssl_certificate}',
                    name: 'servers.ssl-certificate.update',
                    action: 'ServerServerSslController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri:
                        'api/my/servers/{server}/ssl-certificate/{ssl_certificate}',
                    name: 'servers.ssl-certificate.destroy',
                    action: 'ServerServerSslController@destroy',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/servers/{server}/schema-users',
                    name: 'servers.schema-users.index',
                    action: 'ServerServerSchemaUserController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/servers/{server}/schema-users',
                    name: 'servers.schema-users.store',
                    action: 'ServerServerSchemaUserController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/servers/{server}/schema-users/{schema_user}',
                    name: 'servers.schema-users.show',
                    action: 'ServerServerSchemaUserController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri: 'api/my/servers/{server}/schema-users/{schema_user}',
                    name: 'servers.schema-users.update',
                    action: 'ServerServerSchemaUserController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri: 'api/my/servers/{server}/schema-users/{schema_user}',
                    name: 'servers.schema-users.destroy',
                    action: 'ServerServerSchemaUserController@destroy',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/servers/{server}/firewall-rules',
                    name: 'servers.firewall-rules.index',
                    action: 'ServerServerFirewallRuleController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/servers/{server}/firewall-rules',
                    name: 'servers.firewall-rules.store',
                    action: 'ServerServerFirewallRuleController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri:
                        'api/my/servers/{server}/firewall-rules/{firewall_rule}',
                    name: 'servers.firewall-rules.show',
                    action: 'ServerServerFirewallRuleController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri:
                        'api/my/servers/{server}/firewall-rules/{firewall_rule}',
                    name: 'servers.firewall-rules.update',
                    action: 'ServerServerFirewallRuleController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri:
                        'api/my/servers/{server}/firewall-rules/{firewall_rule}',
                    name: 'servers.firewall-rules.destroy',
                    action: 'ServerServerFirewallRuleController@destroy',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/servers/{server}/provision-steps',
                    name: 'servers.provision-steps.index',
                    action: 'ServerServerProvisionStepsController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/servers/{server}/provision-steps',
                    name: 'servers.provision-steps.store',
                    action: 'ServerServerProvisionStepsController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri:
                        'api/my/servers/{server}/provision-steps/{provision_step}',
                    name: 'servers.provision-steps.show',
                    action: 'ServerServerProvisionStepsController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri:
                        'api/my/servers/{server}/provision-steps/{provision_step}',
                    name: 'servers.provision-steps.update',
                    action: 'ServerServerProvisionStepsController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri:
                        'api/my/servers/{server}/provision-steps/{provision_step}',
                    name: 'servers.provision-steps.destroy',
                    action: 'ServerServerProvisionStepsController@destroy',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/servers/{server}/language-settings',
                    name: 'servers.language-settings.index',
                    action: 'ServerServerLanguageSettingsController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/servers/{server}/language-settings',
                    name: 'servers.language-settings.store',
                    action: 'ServerServerLanguageSettingsController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri:
                        'api/my/servers/{server}/language-settings/{language_setting}',
                    name: 'servers.language-settings.show',
                    action: 'ServerServerLanguageSettingsController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri:
                        'api/my/servers/{server}/language-settings/{language_setting}',
                    name: 'servers.language-settings.update',
                    action: 'ServerServerLanguageSettingsController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri:
                        'api/my/servers/{server}/language-settings/{language_setting}',
                    name: 'servers.language-settings.destroy',
                    action: 'ServerServerLanguageSettingsController@destroy',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/servers/{server}/environment-variables',
                    name: 'servers.environment-variables.index',
                    action: 'ServerServerEnvironmentVariablesController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/servers/{server}/environment-variables',
                    name: 'servers.environment-variables.store',
                    action: 'ServerServerEnvironmentVariablesController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri:
                        'api/my/servers/{server}/environment-variables/{environment_variable}',
                    name: 'servers.environment-variables.show',
                    action: 'ServerServerEnvironmentVariablesController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri:
                        'api/my/servers/{server}/environment-variables/{environment_variable}',
                    name: 'servers.environment-variables.update',
                    action: 'ServerServerEnvironmentVariablesController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri:
                        'api/my/servers/{server}/environment-variables/{environment_variable}',
                    name: 'servers.environment-variables.destroy',
                    action:
                        'ServerServerEnvironmentVariablesController@destroy',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/server/{server}/language-settings',
                    name: null,
                    action:
                        'ServerServerLanguageSettingsController@getLanguageSettings',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/sites',
                    name: 'sites.index',
                    action: 'SiteSiteController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/sites',
                    name: 'sites.store',
                    action: 'SiteSiteController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/sites/{site}',
                    name: 'sites.show',
                    action: 'SiteSiteController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri: 'api/my/sites/{site}',
                    name: 'sites.update',
                    action: 'SiteSiteController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri: 'api/my/sites/{site}',
                    name: 'sites.destroy',
                    action: 'SiteSiteController@destroy',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/site/{site}/find-file',
                    name: null,
                    action: 'SiteSiteFileController@find',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/site/{site}/workflow',
                    name: null,
                    action: 'SiteSiteWorkflowController@store',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/site/{site}/refresh-ssh-keys',
                    name: null,
                    action: 'SiteSiteController@refreshPublicKey',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/site/{site}/refresh-deploy-key',
                    name: null,
                    action: 'SiteSiteController@refreshDeployKey',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri:
                        'api/my/site/{site}/reload-file/{file}/server/{server}',
                    name: null,
                    action: 'SiteSiteFileController@reloadFile',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/deploy/{site}',
                    name: null,
                    action: 'SiteSiteController@deploy',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/rollback/{site}',
                    name: null,
                    action: 'SiteSiteController@rollback',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/site/{site}/deployment-steps',
                    name: null,
                    action:
                        'SiteSiteDeploymentStepsController@getDeploymentSteps',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/restart-server/{site}',
                    name: null,
                    action: 'SiteSiteController@restartServer',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/restart-database/{site}',
                    name: null,
                    action: 'SiteSiteController@restartDatabases',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/restart-workers/{site}',
                    name: null,
                    action: 'SiteSiteController@restartWorkerServices',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/restart-web-services/{site}',
                    name: null,
                    action: 'SiteSiteController@restartWebServices',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/sites/{site}/dns',
                    name: 'sites.dns.index',
                    action: 'SiteSiteDnsController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/sites/{site}/dns',
                    name: 'sites.dns.store',
                    action: 'SiteSiteDnsController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/sites/{site}/dns/{dn}',
                    name: 'sites.dns.show',
                    action: 'SiteSiteDnsController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri: 'api/my/sites/{site}/dns/{dn}',
                    name: 'sites.dns.update',
                    action: 'SiteSiteDnsController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri: 'api/my/sites/{site}/dns/{dn}',
                    name: 'sites.dns.destroy',
                    action: 'SiteSiteDnsController@destroy',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/sites/{site}/file',
                    name: 'sites.file.index',
                    action: 'SiteSiteFileController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/sites/{site}/file',
                    name: 'sites.file.store',
                    action: 'SiteSiteFileController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/sites/{site}/file/{file}',
                    name: 'sites.file.show',
                    action: 'SiteSiteFileController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri: 'api/my/sites/{site}/file/{file}',
                    name: 'sites.file.update',
                    action: 'SiteSiteFileController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri: 'api/my/sites/{site}/file/{file}',
                    name: 'sites.file.destroy',
                    action: 'SiteSiteFileController@destroy',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/sites/{site}/buoys',
                    name: 'sites.buoys.index',
                    action: 'SiteSiteBuoyController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/sites/{site}/buoys',
                    name: 'sites.buoys.store',
                    action: 'SiteSiteBuoyController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/sites/{site}/buoys/{buoy}',
                    name: 'sites.buoys.show',
                    action: 'SiteSiteBuoyController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri: 'api/my/sites/{site}/buoys/{buoy}',
                    name: 'sites.buoys.update',
                    action: 'SiteSiteBuoyController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri: 'api/my/sites/{site}/buoys/{buoy}',
                    name: 'sites.buoys.destroy',
                    action: 'SiteSiteBuoyController@destroy',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/sites/{site}/servers',
                    name: 'sites.servers.index',
                    action: 'SiteSiteServerController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/sites/{site}/servers',
                    name: 'sites.servers.store',
                    action: 'SiteSiteServerController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/sites/{site}/servers/{server}',
                    name: 'sites.servers.show',
                    action: 'SiteSiteServerController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri: 'api/my/sites/{site}/servers/{server}',
                    name: 'sites.servers.update',
                    action: 'SiteSiteServerController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri: 'api/my/sites/{site}/servers/{server}',
                    name: 'sites.servers.destroy',
                    action: 'SiteSiteServerController@destroy',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/sites/{site}/workers',
                    name: 'sites.workers.index',
                    action: 'SiteSiteWorkerController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/sites/{site}/workers',
                    name: 'sites.workers.store',
                    action: 'SiteSiteWorkerController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/sites/{site}/workers/{worker}',
                    name: 'sites.workers.show',
                    action: 'SiteSiteWorkerController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri: 'api/my/sites/{site}/workers/{worker}',
                    name: 'sites.workers.update',
                    action: 'SiteSiteWorkerController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri: 'api/my/sites/{site}/workers/{worker}',
                    name: 'sites.workers.destroy',
                    action: 'SiteSiteWorkerController@destroy',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/sites/{site}/schemas',
                    name: 'sites.schemas.index',
                    action: 'SiteSiteSchemaController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/sites/{site}/schemas',
                    name: 'sites.schemas.store',
                    action: 'SiteSiteSchemaController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/sites/{site}/schemas/{schema}',
                    name: 'sites.schemas.show',
                    action: 'SiteSiteSchemaController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri: 'api/my/sites/{site}/schemas/{schema}',
                    name: 'sites.schemas.update',
                    action: 'SiteSiteSchemaController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri: 'api/my/sites/{site}/schemas/{schema}',
                    name: 'sites.schemas.destroy',
                    action: 'SiteSiteSchemaController@destroy',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/sites/{site}/ssh-keys',
                    name: 'sites.ssh-keys.index',
                    action: 'SiteSiteSshKeyController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/sites/{site}/ssh-keys',
                    name: 'sites.ssh-keys.store',
                    action: 'SiteSiteSshKeyController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/sites/{site}/ssh-keys/{ssh_key}',
                    name: 'sites.ssh-keys.show',
                    action: 'SiteSiteSshKeyController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri: 'api/my/sites/{site}/ssh-keys/{ssh_key}',
                    name: 'sites.ssh-keys.update',
                    action: 'SiteSiteSshKeyController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri: 'api/my/sites/{site}/ssh-keys/{ssh_key}',
                    name: 'sites.ssh-keys.destroy',
                    action: 'SiteSiteSshKeyController@destroy',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/sites/{site}/cron-jobs',
                    name: 'sites.cron-jobs.index',
                    action: 'SiteSiteCronJobController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/sites/{site}/cron-jobs',
                    name: 'sites.cron-jobs.store',
                    action: 'SiteSiteCronJobController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/sites/{site}/cron-jobs/{cron_job}',
                    name: 'sites.cron-jobs.show',
                    action: 'SiteSiteCronJobController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri: 'api/my/sites/{site}/cron-jobs/{cron_job}',
                    name: 'sites.cron-jobs.update',
                    action: 'SiteSiteCronJobController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri: 'api/my/sites/{site}/cron-jobs/{cron_job}',
                    name: 'sites.cron-jobs.destroy',
                    action: 'SiteSiteCronJobController@destroy',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/sites/{site}/ssl-certificate',
                    name: 'sites.ssl-certificate.index',
                    action: 'SiteSiteSslController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/sites/{site}/ssl-certificate',
                    name: 'sites.ssl-certificate.store',
                    action: 'SiteSiteSslController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri:
                        'api/my/sites/{site}/ssl-certificate/{ssl_certificate}',
                    name: 'sites.ssl-certificate.show',
                    action: 'SiteSiteSslController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri:
                        'api/my/sites/{site}/ssl-certificate/{ssl_certificate}',
                    name: 'sites.ssl-certificate.update',
                    action: 'SiteSiteSslController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri:
                        'api/my/sites/{site}/ssl-certificate/{ssl_certificate}',
                    name: 'sites.ssl-certificate.destroy',
                    action: 'SiteSiteSslController@destroy',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/sites/{site}/life-lines',
                    name: 'sites.life-lines.index',
                    action: 'SiteSiteLifelinesController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/sites/{site}/life-lines',
                    name: 'sites.life-lines.store',
                    action: 'SiteSiteLifelinesController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/sites/{site}/life-lines/{life_line}',
                    name: 'sites.life-lines.show',
                    action: 'SiteSiteLifelinesController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri: 'api/my/sites/{site}/life-lines/{life_line}',
                    name: 'sites.life-lines.update',
                    action: 'SiteSiteLifelinesController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri: 'api/my/sites/{site}/life-lines/{life_line}',
                    name: 'sites.life-lines.destroy',
                    action: 'SiteSiteLifelinesController@destroy',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/sites/{site}/deployments',
                    name: 'sites.deployments.index',
                    action: 'SiteSiteDeploymentsController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/sites/{site}/deployments',
                    name: 'sites.deployments.store',
                    action: 'SiteSiteDeploymentsController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/sites/{site}/deployments/{deployment}',
                    name: 'sites.deployments.show',
                    action: 'SiteSiteDeploymentsController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri: 'api/my/sites/{site}/deployments/{deployment}',
                    name: 'sites.deployments.update',
                    action: 'SiteSiteDeploymentsController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri: 'api/my/sites/{site}/deployments/{deployment}',
                    name: 'sites.deployments.destroy',
                    action: 'SiteSiteDeploymentsController@destroy',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/sites/{site}/schema-users',
                    name: 'sites.schema-users.index',
                    action: 'SiteSiteSchemaUserController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/sites/{site}/schema-users',
                    name: 'sites.schema-users.store',
                    action: 'SiteSiteSchemaUserController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/sites/{site}/schema-users/{schema_user}',
                    name: 'sites.schema-users.show',
                    action: 'SiteSiteSchemaUserController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri: 'api/my/sites/{site}/schema-users/{schema_user}',
                    name: 'sites.schema-users.update',
                    action: 'SiteSiteSchemaUserController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri: 'api/my/sites/{site}/schema-users/{schema_user}',
                    name: 'sites.schema-users.destroy',
                    action: 'SiteSiteSchemaUserController@destroy',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/sites/{site}/hooks',
                    name: 'sites.hooks.index',
                    action: 'SiteRepositoryRepositoryHookController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/sites/{site}/hooks',
                    name: 'sites.hooks.store',
                    action: 'SiteRepositoryRepositoryHookController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/sites/{site}/hooks/{hook}',
                    name: 'sites.hooks.show',
                    action: 'SiteRepositoryRepositoryHookController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri: 'api/my/sites/{site}/hooks/{hook}',
                    name: 'sites.hooks.update',
                    action: 'SiteRepositoryRepositoryHookController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri: 'api/my/sites/{site}/hooks/{hook}',
                    name: 'sites.hooks.destroy',
                    action: 'SiteRepositoryRepositoryHookController@destroy',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/sites/{site}/firewall-rules',
                    name: 'sites.firewall-rules.index',
                    action: 'SiteSiteFirewallRuleController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/sites/{site}/firewall-rules',
                    name: 'sites.firewall-rules.store',
                    action: 'SiteSiteFirewallRuleController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/sites/{site}/firewall-rules/{firewall_rule}',
                    name: 'sites.firewall-rules.show',
                    action: 'SiteSiteFirewallRuleController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri: 'api/my/sites/{site}/firewall-rules/{firewall_rule}',
                    name: 'sites.firewall-rules.update',
                    action: 'SiteSiteFirewallRuleController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri: 'api/my/sites/{site}/firewall-rules/{firewall_rule}',
                    name: 'sites.firewall-rules.destroy',
                    action: 'SiteSiteFirewallRuleController@destroy',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/sites/{site}/server-features',
                    name: 'sites.server-features.index',
                    action: 'SiteSiteServerFeaturesController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/sites/{site}/server-features',
                    name: 'sites.server-features.store',
                    action: 'SiteSiteServerFeaturesController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/sites/{site}/server-features/{server_type}',
                    name: 'sites.server-features.show',
                    action: 'SiteSiteServerFeaturesController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri: 'api/my/sites/{site}/server-features/{server_type}',
                    name: 'sites.server-features.update',
                    action: 'SiteSiteServerFeaturesController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri: 'api/my/sites/{site}/server-features/{server_type}',
                    name: 'sites.server-features.destroy',
                    action: 'SiteSiteServerFeaturesController@destroy',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/sites/{site}/deployment-steps',
                    name: 'sites.deployment-steps.index',
                    action: 'SiteSiteDeploymentStepsController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/sites/{site}/deployment-steps',
                    name: 'sites.deployment-steps.store',
                    action: 'SiteSiteDeploymentStepsController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri:
                        'api/my/sites/{site}/deployment-steps/{deployment_step}',
                    name: 'sites.deployment-steps.show',
                    action: 'SiteSiteDeploymentStepsController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri:
                        'api/my/sites/{site}/deployment-steps/{deployment_step}',
                    name: 'sites.deployment-steps.update',
                    action: 'SiteSiteDeploymentStepsController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri:
                        'api/my/sites/{site}/deployment-steps/{deployment_step}',
                    name: 'sites.deployment-steps.destroy',
                    action: 'SiteSiteDeploymentStepsController@destroy',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/sites/{site}/language-settings',
                    name: 'sites.language-settings.index',
                    action: 'SiteSiteLanguageSettingsController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/sites/{site}/language-settings',
                    name: 'sites.language-settings.store',
                    action: 'SiteSiteLanguageSettingsController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri:
                        'api/my/sites/{site}/language-settings/{language_setting}',
                    name: 'sites.language-settings.show',
                    action: 'SiteSiteLanguageSettingsController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri:
                        'api/my/sites/{site}/language-settings/{language_setting}',
                    name: 'sites.language-settings.update',
                    action: 'SiteSiteLanguageSettingsController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri:
                        'api/my/sites/{site}/language-settings/{language_setting}',
                    name: 'sites.language-settings.destroy',
                    action: 'SiteSiteLanguageSettingsController@destroy',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/sites/{site}/environment-variables',
                    name: 'sites.environment-variables.index',
                    action: 'SiteSiteEnvironmentVariablesController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/my/sites/{site}/environment-variables',
                    name: 'sites.environment-variables.store',
                    action: 'SiteSiteEnvironmentVariablesController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri:
                        'api/my/sites/{site}/environment-variables/{environment_variable}',
                    name: 'sites.environment-variables.show',
                    action: 'SiteSiteEnvironmentVariablesController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri:
                        'api/my/sites/{site}/environment-variables/{environment_variable}',
                    name: 'sites.environment-variables.update',
                    action: 'SiteSiteEnvironmentVariablesController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri:
                        'api/my/sites/{site}/environment-variables/{environment_variable}',
                    name: 'sites.environment-variables.destroy',
                    action: 'SiteSiteEnvironmentVariablesController@destroy',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/my/site/{site}/language-settings',
                    name: null,
                    action:
                        'SiteSiteLanguageSettingsController@getLanguageSettings',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/notification-settings',
                    name: 'notification-settings.index',
                    action: 'NotificationSettingsController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/notification-settings',
                    name: 'notification-settings.store',
                    action: 'NotificationSettingsController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/notification-settings/{notification_setting}',
                    name: 'notification-settings.show',
                    action: 'NotificationSettingsController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri: 'api/notification-settings/{notification_setting}',
                    name: 'notification-settings.update',
                    action: 'NotificationSettingsController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri: 'api/notification-settings/{notification_setting}',
                    name: 'notification-settings.destroy',
                    action: 'NotificationSettingsController@destroy',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/server/types',
                    name: 'types.index',
                    action: 'ServerServerTypesController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/server/types',
                    name: 'types.store',
                    action: 'ServerServerTypesController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/server/types/{type}',
                    name: 'types.show',
                    action: 'ServerServerTypesController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri: 'api/server/types/{type}',
                    name: 'types.update',
                    action: 'ServerServerTypesController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri: 'api/server/types/{type}',
                    name: 'types.destroy',
                    action: 'ServerServerTypesController@destroy',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/server/features',
                    name: null,
                    action: 'ServerServerFeatureController@getFeatures',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/server/languages',
                    name: null,
                    action: 'ServerServerFeatureController@getLanguages',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/server/frameworks',
                    name: null,
                    action: 'ServerServerFeatureController@getFrameworks',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/site/{site}/editable-files',
                    name: null,
                    action: 'SiteSiteFeatureController@getEditableFiles',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/server/{server}/editable-files',
                    name: null,
                    action: 'ServerServerFeatureController@getEditableFiles',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/site/{site}/framework/editable-files',
                    name: null,
                    action:
                        'SiteSiteFeatureController@getEditableFrameworkFiles',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/auth/providers/server-providers',
                    name: 'server-providers.index',
                    action: 'AuthProvidersServerProvidersController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/auth/providers/server-providers',
                    name: 'server-providers.store',
                    action: 'AuthProvidersServerProvidersController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri:
                        'api/auth/providers/server-providers/{server_provider}',
                    name: 'server-providers.show',
                    action: 'AuthProvidersServerProvidersController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri:
                        'api/auth/providers/server-providers/{server_provider}',
                    name: 'server-providers.update',
                    action: 'AuthProvidersServerProvidersController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri:
                        'api/auth/providers/server-providers/{server_provider}',
                    name: 'server-providers.destroy',
                    action: 'AuthProvidersServerProvidersController@destroy',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/auth/providers/repository-providers',
                    name: 'repository-providers.index',
                    action: 'AuthProvidersRepositoryProvidersController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/auth/providers/repository-providers',
                    name: 'repository-providers.store',
                    action: 'AuthProvidersRepositoryProvidersController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri:
                        'api/auth/providers/repository-providers/{repository_provider}',
                    name: 'repository-providers.show',
                    action: 'AuthProvidersRepositoryProvidersController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri:
                        'api/auth/providers/repository-providers/{repository_provider}',
                    name: 'repository-providers.update',
                    action: 'AuthProvidersRepositoryProvidersController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri:
                        'api/auth/providers/repository-providers/{repository_provider}',
                    name: 'repository-providers.destroy',
                    action:
                        'AuthProvidersRepositoryProvidersController@destroy',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/auth/providers/notification-providers',
                    name: 'notification-providers.index',
                    action:
                        'AuthProvidersNotificationProvidersController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/auth/providers/notification-providers',
                    name: 'notification-providers.store',
                    action:
                        'AuthProvidersNotificationProvidersController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri:
                        'api/auth/providers/notification-providers/{notification_provider}',
                    name: 'notification-providers.show',
                    action: 'AuthProvidersNotificationProvidersController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri:
                        'api/auth/providers/notification-providers/{notification_provider}',
                    name: 'notification-providers.update',
                    action:
                        'AuthProvidersNotificationProvidersController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri:
                        'api/auth/providers/notification-providers/{notification_provider}',
                    name: 'notification-providers.destroy',
                    action:
                        'AuthProvidersNotificationProvidersController@destroy',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/server/providers/digitalocean/options',
                    name: 'options.index',
                    action:
                        'ServerProvidersDigitalOceanDigitalOceanServerOptionsController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/server/providers/digitalocean/options',
                    name: 'options.store',
                    action:
                        'ServerProvidersDigitalOceanDigitalOceanServerOptionsController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/server/providers/digitalocean/options/{option}',
                    name: 'options.show',
                    action:
                        'ServerProvidersDigitalOceanDigitalOceanServerOptionsController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri: 'api/server/providers/digitalocean/options/{option}',
                    name: 'options.update',
                    action:
                        'ServerProvidersDigitalOceanDigitalOceanServerOptionsController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri: 'api/server/providers/digitalocean/options/{option}',
                    name: 'options.destroy',
                    action:
                        'ServerProvidersDigitalOceanDigitalOceanServerOptionsController@destroy',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/server/providers/digitalocean/regions',
                    name: 'regions.index',
                    action:
                        'ServerProvidersDigitalOceanDigitalOceanServerRegionsController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/server/providers/digitalocean/regions',
                    name: 'regions.store',
                    action:
                        'ServerProvidersDigitalOceanDigitalOceanServerRegionsController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/server/providers/digitalocean/regions/{region}',
                    name: 'regions.show',
                    action:
                        'ServerProvidersDigitalOceanDigitalOceanServerRegionsController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri: 'api/server/providers/digitalocean/regions/{region}',
                    name: 'regions.update',
                    action:
                        'ServerProvidersDigitalOceanDigitalOceanServerRegionsController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri: 'api/server/providers/digitalocean/regions/{region}',
                    name: 'regions.destroy',
                    action:
                        'ServerProvidersDigitalOceanDigitalOceanServerRegionsController@destroy',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/server/providers/digitalocean/features',
                    name: 'features.index',
                    action:
                        'ServerProvidersDigitalOceanDigitalOceanServerFeaturesController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/server/providers/digitalocean/features',
                    name: 'features.store',
                    action:
                        'ServerProvidersDigitalOceanDigitalOceanServerFeaturesController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/server/providers/digitalocean/features/{feature}',
                    name: 'features.show',
                    action:
                        'ServerProvidersDigitalOceanDigitalOceanServerFeaturesController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri: 'api/server/providers/digitalocean/features/{feature}',
                    name: 'features.update',
                    action:
                        'ServerProvidersDigitalOceanDigitalOceanServerFeaturesController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri: 'api/server/providers/digitalocean/features/{feature}',
                    name: 'features.destroy',
                    action:
                        'ServerProvidersDigitalOceanDigitalOceanServerFeaturesController@destroy',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/server/providers/linode/provider',
                    name: 'provider.index',
                    action: 'ServerProvidersLinodeLinodeController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/server/providers/linode/provider',
                    name: 'provider.store',
                    action: 'ServerProvidersLinodeLinodeController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/server/providers/linode/provider/{provider}',
                    name: 'provider.show',
                    action: 'ServerProvidersLinodeLinodeController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri: 'api/server/providers/linode/provider/{provider}',
                    name: 'provider.update',
                    action: 'ServerProvidersLinodeLinodeController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri: 'api/server/providers/linode/provider/{provider}',
                    name: 'provider.destroy',
                    action: 'ServerProvidersLinodeLinodeController@destroy',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/server/providers/linode/options',
                    name: 'options.index',
                    action:
                        'ServerProvidersLinodeLinodeServerOptionsController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/server/providers/linode/options',
                    name: 'options.store',
                    action:
                        'ServerProvidersLinodeLinodeServerOptionsController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/server/providers/linode/options/{option}',
                    name: 'options.show',
                    action:
                        'ServerProvidersLinodeLinodeServerOptionsController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri: 'api/server/providers/linode/options/{option}',
                    name: 'options.update',
                    action:
                        'ServerProvidersLinodeLinodeServerOptionsController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri: 'api/server/providers/linode/options/{option}',
                    name: 'options.destroy',
                    action:
                        'ServerProvidersLinodeLinodeServerOptionsController@destroy',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/server/providers/linode/regions',
                    name: 'regions.index',
                    action:
                        'ServerProvidersLinodeLinodeServerRegionsController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/server/providers/linode/regions',
                    name: 'regions.store',
                    action:
                        'ServerProvidersLinodeLinodeServerRegionsController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/server/providers/linode/regions/{region}',
                    name: 'regions.show',
                    action:
                        'ServerProvidersLinodeLinodeServerRegionsController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri: 'api/server/providers/linode/regions/{region}',
                    name: 'regions.update',
                    action:
                        'ServerProvidersLinodeLinodeServerRegionsController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri: 'api/server/providers/linode/regions/{region}',
                    name: 'regions.destroy',
                    action:
                        'ServerProvidersLinodeLinodeServerRegionsController@destroy',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/server/providers/linode/features',
                    name: 'features.index',
                    action:
                        'ServerProvidersLinodeLinodeServerFeaturesController@index',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'api/server/providers/linode/features',
                    name: 'features.store',
                    action:
                        'ServerProvidersLinodeLinodeServerFeaturesController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'api/server/providers/linode/features/{feature}',
                    name: 'features.show',
                    action:
                        'ServerProvidersLinodeLinodeServerFeaturesController@show',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri: 'api/server/providers/linode/features/{feature}',
                    name: 'features.update',
                    action:
                        'ServerProvidersLinodeLinodeServerFeaturesController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri: 'api/server/providers/linode/features/{feature}',
                    name: 'features.destroy',
                    action:
                        'ServerProvidersLinodeLinodeServerFeaturesController@destroy',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'login',
                    name: 'login',
                    action: 'AuthLoginController@showLoginForm',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'login',
                    name: null,
                    action: 'AuthLoginController@login',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'logout',
                    name: 'logout',
                    action: 'AuthLoginController@logout',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'register',
                    name: null,
                    action: 'AuthRegisterController@register',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'password/email',
                    name: 'password.email',
                    action: 'AuthForgotPasswordController@sendResetLinkEmail',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'password/reset/{token}',
                    name: 'password.reset',
                    action: 'AuthResetPasswordController@showResetForm',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'password/reset',
                    name: null,
                    action: 'AuthResetPasswordController@reset',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'provider/{provider}/link',
                    name: null,
                    action: 'AuthOauthController@newProvider',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'provider/{provider}/callback',
                    name: null,
                    action: 'AuthOauthController@getHandleProviderCallback',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'stripe/webhook',
                    name: null,
                    action:
                        'LaravelCashierHttpControllersWebhookController@handleWebhook',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'subscription/plans',
                    name: 'plans.index',
                    action: 'SubscriptionController@index',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'subscription/plans/create',
                    name: 'plans.create',
                    action: 'SubscriptionController@create',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'subscription/plans',
                    name: 'plans.store',
                    action: 'SubscriptionController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'subscription/plans/{plan}',
                    name: 'plans.show',
                    action: 'SubscriptionController@show',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'subscription/plans/{plan}/edit',
                    name: 'plans.edit',
                    action: 'SubscriptionController@edit',
                },
                {
                    host: null,
                    methods: ['PUT', 'PATCH'],
                    uri: 'subscription/plans/{plan}',
                    name: 'plans.update',
                    action: 'SubscriptionController@update',
                },
                {
                    host: null,
                    methods: ['DELETE'],
                    uri: 'subscription/plans/{plan}',
                    name: 'plans.destroy',
                    action: 'SubscriptionController@destroy',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE'],
                    uri: 'webhook/deploy/{siteHashID}',
                    name: null,
                    action: 'WebHookController@deploy',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'webhook/loads/{serverHashID}',
                    name: null,
                    action: 'WebHookController@loadMonitor',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'webhook/memory/{serverHashID}',
                    name: null,
                    action: 'WebHookController@memoryMonitor',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'webhook/diskusage/{serverHashID}',
                    name: null,
                    action: 'WebHookController@diskUsageMonitor',
                },
                {
                    host: 'lifeline.codepier.dev',
                    methods: ['GET', 'HEAD'],
                    uri: '{lifelineHashId}',
                    name: null,
                    action: 'LifeLineController@update',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'teams/accept/{token}',
                    name: 'teams.accept_invite',
                    action: 'UserTeamUserTeamController@acceptInvite',
                },
                {
                    host: 'style-guide.codepier.dev',
                    methods: ['GET', 'HEAD'],
                    uri: '/',
                    name: null,
                    action: 'PublicController@styleGuide',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'privacy',
                    name: null,
                    action: 'PublicController@privacy',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'subscribe',
                    name: null,
                    action: 'PublicController@subscribe',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'terms-of-service',
                    name: null,
                    action: 'PublicController@termsOfService',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: '/',
                    name: null,
                    action: 'Controller@app',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'second-auth',
                    name: null,
                    action: 'AuthSecondAuthController@show',
                },
                {
                    host: null,
                    methods: ['POST'],
                    uri: 'second-auth',
                    name: null,
                    action: 'AuthSecondAuthController@store',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'slack-invite',
                    name: null,
                    action: 'UserUserController@slackInvite',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: 'subscription/invoice/{invoice}',
                    name: null,
                    action:
                        'UserSubscriptionUserSubscriptionInvoiceController@show',
                },
                {
                    host: null,
                    methods: ['GET', 'HEAD'],
                    uri: '{any}',
                    name: null,
                    action: 'Controller@app',
                },
            ],
            prefix: '',

            route: function(name, parameters, route) {
                route = route || this.getByName(name);

                if (!route) {
                    return undefined;
                }

                return this.toRoute(route, parameters);
            },

            url: function(url, parameters) {
                parameters = parameters || [];

                var uri = url + '/' + parameters.join('/');

                return this.getCorrectUrl(uri);
            },

            toRoute: function(route, parameters) {
                var uri = this.replaceNamedParameters(route.uri, parameters);
                var qs = this.getRouteQueryString(parameters);

                if (this.absolute && this.isOtherHost(route)) {
                    return '//' + route.host + '/' + uri + qs;
                }

                return this.getCorrectUrl(uri + qs);
            },

            isOtherHost: function(route) {
                return route.host && route.host != window.location.hostname;
            },

            replaceNamedParameters: function(uri, parameters) {
                uri = uri.replace(/\{(.*?)\??\}/g, function(match, key) {
                    if (parameters.hasOwnProperty(key)) {
                        var value = parameters[key];
                        delete parameters[key];
                        return value;
                    } else {
                        return match;
                    }
                });

                // Strip out any optional parameters that were not given
                uri = uri.replace(/\/\{.*?\?\}/g, '');

                return uri;
            },

            getRouteQueryString: function(parameters) {
                var qs = [];
                for (var key in parameters) {
                    if (parameters.hasOwnProperty(key)) {
                        qs.push(key + '=' + parameters[key]);
                    }
                }

                if (qs.length < 1) {
                    return '';
                }

                return '?' + qs.join('&');
            },

            getByName: function(name) {
                for (var key in this.routes) {
                    if (
                        this.routes.hasOwnProperty(key) &&
                        this.routes[key].name === name
                    ) {
                        return this.routes[key];
                    }
                }
            },

            getByAction: function(action) {
                for (var key in this.routes) {
                    if (
                        this.routes.hasOwnProperty(key) &&
                        this.routes[key].action === action
                    ) {
                        return this.routes[key];
                    }
                }
            },

            getCorrectUrl: function(uri) {
                var url = this.prefix + '/' + uri.replace(/^\/?/, '');

                if (!this.absolute) {
                    return url;
                }

                return this.rootUrl.replace('//?$/', '') + url;
            },
        };

        var getLinkAttributes = function(attributes) {
            if (!attributes) {
                return '';
            }

            var attrs = [];
            for (var key in attributes) {
                if (attributes.hasOwnProperty(key)) {
                    attrs.push(key + '="' + attributes[key] + '"');
                }
            }

            return attrs.join(' ');
        };

        var getHtmlLink = function(url, title, attributes) {
            title = title || url;
            attributes = getLinkAttributes(attributes);

            return '<a href="' + url + '" ' + attributes + '>' + title + '</a>';
        };

        return {
            // Generate a url for a given controller action.
            // laroute.action('HomeController@getIndex', [params = {}])
            action: function(name, parameters) {
                parameters = parameters || {};

                return routes.route(name, parameters, routes.getByAction(name));
            },

            // Generate a url for a given named route.
            // laroute.route('routeName', [params = {}])
            route: function(route, parameters) {
                parameters = parameters || {};

                return routes.route(route, parameters);
            },

            // Generate a fully qualified URL to the given path.
            // laroute.route('url', [params = {}])
            url: function(route, parameters) {
                parameters = parameters || {};

                return routes.url(route, parameters);
            },

            // Generate a html link to the given url.
            // laroute.link_to('foo/bar', [title = url], [attributes = {}])
            link_to: function(url, title, attributes) {
                url = this.url(url);

                return getHtmlLink(url, title, attributes);
            },

            // Generate a html link to the given route.
            // laroute.link_to_route('route.name', [title=url], [parameters = {}], [attributes = {}])
            link_to_route: function(route, title, parameters, attributes) {
                var url = this.route(route, parameters);

                return getHtmlLink(url, title, attributes);
            },

            // Generate a html link to the given controller action.
            // laroute.link_to_action('HomeController@getIndex', [title=url], [parameters = {}], [attributes = {}])
            link_to_action: function(action, title, parameters, attributes) {
                var url = this.action(action, parameters);

                return getHtmlLink(url, title, attributes);
            },
        };
    }.call(this);

    /**
     * Expose the class either via AMD, CommonJS or the global object
     */
    if (typeof define === 'function' && define.amd) {
        define(function() {
            return laroute;
        });
    } else if (typeof module === 'object' && module.exports) {
        module.exports = laroute;
    } else {
        window.laroute = laroute;
    }
}.call(this));
