(function () {

    var laroute = (function () {

        var routes = {

            absolute: false,
            rootUrl: 'http://codepier.app',
            routes: [{
                "host": null,
                "methods": ["POST"],
                "uri": "broadcasting\/auth",
                "name": null,
                "action": "Illuminate\Broadcasting\BroadcastController@authenticate"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "oauth\/authorize",
                "name": null,
                "action": "\Laravel\Passport\Http\Controllers\AuthorizationController@authorize"
            }, {
                "host": null,
                "methods": ["POST"],
                "uri": "oauth\/authorize",
                "name": null,
                "action": "\Laravel\Passport\Http\Controllers\ApproveAuthorizationController@approve"
            }, {
                "host": null,
                "methods": ["DELETE"],
                "uri": "oauth\/authorize",
                "name": null,
                "action": "\Laravel\Passport\Http\Controllers\DenyAuthorizationController@deny"
            }, {
                "host": null,
                "methods": ["POST"],
                "uri": "oauth\/token",
                "name": null,
                "action": "\Laravel\Passport\Http\Controllers\AccessTokenController@issueToken"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "oauth\/tokens",
                "name": null,
                "action": "\Laravel\Passport\Http\Controllers\AuthorizedAccessTokenController@forUser"
            }, {
                "host": null,
                "methods": ["DELETE"],
                "uri": "oauth\/tokens\/{token_id}",
                "name": null,
                "action": "\Laravel\Passport\Http\Controllers\AuthorizedAccessTokenController@destroy"
            }, {
                "host": null,
                "methods": ["POST"],
                "uri": "oauth\/token\/refresh",
                "name": null,
                "action": "\Laravel\Passport\Http\Controllers\TransientTokenController@refresh"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "oauth\/clients",
                "name": null,
                "action": "\Laravel\Passport\Http\Controllers\ClientController@forUser"
            }, {
                "host": null,
                "methods": ["POST"],
                "uri": "oauth\/clients",
                "name": null,
                "action": "\Laravel\Passport\Http\Controllers\ClientController@store"
            }, {
                "host": null,
                "methods": ["PUT"],
                "uri": "oauth\/clients\/{client_id}",
                "name": null,
                "action": "\Laravel\Passport\Http\Controllers\ClientController@update"
            }, {
                "host": null,
                "methods": ["DELETE"],
                "uri": "oauth\/clients\/{client_id}",
                "name": null,
                "action": "\Laravel\Passport\Http\Controllers\ClientController@destroy"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "oauth\/scopes",
                "name": null,
                "action": "\Laravel\Passport\Http\Controllers\ScopeController@all"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "oauth\/personal-access-tokens",
                "name": null,
                "action": "\Laravel\Passport\Http\Controllers\PersonalAccessTokenController@forUser"
            }, {
                "host": null,
                "methods": ["POST"],
                "uri": "oauth\/personal-access-tokens",
                "name": null,
                "action": "\Laravel\Passport\Http\Controllers\PersonalAccessTokenController@store"
            }, {
                "host": null,
                "methods": ["DELETE"],
                "uri": "oauth\/personal-access-tokens\/{token_id}",
                "name": null,
                "action": "\Laravel\Passport\Http\Controllers\PersonalAccessTokenController@destroy"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/user",
                "name": null,
                "action": "Closure"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "login",
                "name": "login",
                "action": "Auth\LoginController@showLoginForm"
            }, {
                "host": null,
                "methods": ["POST"],
                "uri": "login",
                "name": null,
                "action": "Auth\LoginController@login"
            }, {
                "host": null,
                "methods": ["POST"],
                "uri": "logout",
                "name": null,
                "action": "Auth\LoginController@logout"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "register",
                "name": null,
                "action": "Auth\RegisterController@showRegistrationForm"
            }, {
                "host": null,
                "methods": ["POST"],
                "uri": "register",
                "name": null,
                "action": "Auth\RegisterController@register"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "password\/reset",
                "name": null,
                "action": "Auth\ForgotPasswordController@showLinkRequestForm"
            }, {
                "host": null,
                "methods": ["POST"],
                "uri": "password\/email",
                "name": null,
                "action": "Auth\ForgotPasswordController@sendResetLinkEmail"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "password\/reset\/{token}",
                "name": null,
                "action": "Auth\ResetPasswordController@showResetForm"
            }, {
                "host": null,
                "methods": ["POST"],
                "uri": "password\/reset",
                "name": null,
                "action": "Auth\ResetPasswordController@reset"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "provider\/{provider}\/link",
                "name": null,
                "action": "Auth\OauthController@newProvider"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "provider\/{provider}\/callback",
                "name": null,
                "action": "Auth\OauthController@getHandleProviderCallback"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/me",
                "name": "me.index",
                "action": "User\UserController@index"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/user\/create",
                "name": "user.create",
                "action": "User\UserController@create"
            }, {
                "host": null,
                "methods": ["POST"],
                "uri": "api\/user",
                "name": "user.store",
                "action": "User\UserController@store"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/user\/{user}",
                "name": "user.show",
                "action": "User\UserController@show"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/user\/{user}\/edit",
                "name": "user.edit",
                "action": "User\UserController@edit"
            }, {
                "host": null,
                "methods": ["PUT", "PATCH"],
                "uri": "api\/user\/{user}",
                "name": "user.update",
                "action": "User\UserController@update"
            }, {
                "host": null,
                "methods": ["DELETE"],
                "uri": "api\/user\/{user}",
                "name": "user.destroy",
                "action": "User\UserController@destroy"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/subscription\/invoices",
                "name": "invoices.index",
                "action": "User\Subscription\UserSubscriptionInvoiceController@index"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/subscription\/invoices\/create",
                "name": "invoices.create",
                "action": "User\Subscription\UserSubscriptionInvoiceController@create"
            }, {
                "host": null,
                "methods": ["POST"],
                "uri": "api\/my\/subscription\/invoices",
                "name": "invoices.store",
                "action": "User\Subscription\UserSubscriptionInvoiceController@store"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/subscription\/invoices\/{invoice}",
                "name": "invoices.show",
                "action": "User\Subscription\UserSubscriptionInvoiceController@show"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/subscription\/invoices\/{invoice}\/edit",
                "name": "invoices.edit",
                "action": "User\Subscription\UserSubscriptionInvoiceController@edit"
            }, {
                "host": null,
                "methods": ["PUT", "PATCH"],
                "uri": "api\/my\/subscription\/invoices\/{invoice}",
                "name": "invoices.update",
                "action": "User\Subscription\UserSubscriptionInvoiceController@update"
            }, {
                "host": null,
                "methods": ["DELETE"],
                "uri": "api\/my\/subscription\/invoices\/{invoice}",
                "name": "invoices.destroy",
                "action": "User\Subscription\UserSubscriptionInvoiceController@destroy"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/subscription",
                "name": "subscription.index",
                "action": "User\Subscription\UserSubscriptionController@index"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/subscription\/create",
                "name": "subscription.create",
                "action": "User\Subscription\UserSubscriptionController@create"
            }, {
                "host": null,
                "methods": ["POST"],
                "uri": "api\/my\/subscription",
                "name": "subscription.store",
                "action": "User\Subscription\UserSubscriptionController@store"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/subscription\/{subscription}",
                "name": "subscription.show",
                "action": "User\Subscription\UserSubscriptionController@show"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/subscription\/{subscription}\/edit",
                "name": "subscription.edit",
                "action": "User\Subscription\UserSubscriptionController@edit"
            }, {
                "host": null,
                "methods": ["PUT", "PATCH"],
                "uri": "api\/my\/subscription\/{subscription}",
                "name": "subscription.update",
                "action": "User\Subscription\UserSubscriptionController@update"
            }, {
                "host": null,
                "methods": ["DELETE"],
                "uri": "api\/my\/subscription\/{subscription}",
                "name": "subscription.destroy",
                "action": "User\Subscription\UserSubscriptionController@destroy"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/subscription\/invoice\/next",
                "name": "next.index",
                "action": "User\Subscription\UserSubscriptionUpcomingInvoiceController@index"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/subscription\/invoice\/next\/create",
                "name": "next.create",
                "action": "User\Subscription\UserSubscriptionUpcomingInvoiceController@create"
            }, {
                "host": null,
                "methods": ["POST"],
                "uri": "api\/my\/subscription\/invoice\/next",
                "name": "next.store",
                "action": "User\Subscription\UserSubscriptionUpcomingInvoiceController@store"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/subscription\/invoice\/next\/{next}",
                "name": "next.show",
                "action": "User\Subscription\UserSubscriptionUpcomingInvoiceController@show"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/subscription\/invoice\/next\/{next}\/edit",
                "name": "next.edit",
                "action": "User\Subscription\UserSubscriptionUpcomingInvoiceController@edit"
            }, {
                "host": null,
                "methods": ["PUT", "PATCH"],
                "uri": "api\/my\/subscription\/invoice\/next\/{next}",
                "name": "next.update",
                "action": "User\Subscription\UserSubscriptionUpcomingInvoiceController@update"
            }, {
                "host": null,
                "methods": ["DELETE"],
                "uri": "api\/my\/subscription\/invoice\/next\/{next}",
                "name": "next.destroy",
                "action": "User\Subscription\UserSubscriptionUpcomingInvoiceController@destroy"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/ssh-keys",
                "name": "ssh-keys.index",
                "action": "User\UserSshKeyController@index"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/ssh-keys\/create",
                "name": "ssh-keys.create",
                "action": "User\UserSshKeyController@create"
            }, {
                "host": null,
                "methods": ["POST"],
                "uri": "api\/my\/ssh-keys",
                "name": "ssh-keys.store",
                "action": "User\UserSshKeyController@store"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/ssh-keys\/{ssh_key}",
                "name": "ssh-keys.show",
                "action": "User\UserSshKeyController@show"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/ssh-keys\/{ssh_key}\/edit",
                "name": "ssh-keys.edit",
                "action": "User\UserSshKeyController@edit"
            }, {
                "host": null,
                "methods": ["PUT", "PATCH"],
                "uri": "api\/my\/ssh-keys\/{ssh_key}",
                "name": "ssh-keys.update",
                "action": "User\UserSshKeyController@update"
            }, {
                "host": null,
                "methods": ["DELETE"],
                "uri": "api\/my\/ssh-keys\/{ssh_key}",
                "name": "ssh-keys.destroy",
                "action": "User\UserSshKeyController@destroy"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/server-providers",
                "name": "server-providers.index",
                "action": "User\Providers\UserServerProviderController@index"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/server-providers\/create",
                "name": "server-providers.create",
                "action": "User\Providers\UserServerProviderController@create"
            }, {
                "host": null,
                "methods": ["POST"],
                "uri": "api\/my\/server-providers",
                "name": "server-providers.store",
                "action": "User\Providers\UserServerProviderController@store"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/server-providers\/{server_provider}",
                "name": "server-providers.show",
                "action": "User\Providers\UserServerProviderController@show"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/server-providers\/{server_provider}\/edit",
                "name": "server-providers.edit",
                "action": "User\Providers\UserServerProviderController@edit"
            }, {
                "host": null,
                "methods": ["PUT", "PATCH"],
                "uri": "api\/my\/server-providers\/{server_provider}",
                "name": "server-providers.update",
                "action": "User\Providers\UserServerProviderController@update"
            }, {
                "host": null,
                "methods": ["DELETE"],
                "uri": "api\/my\/server-providers\/{server_provider}",
                "name": "server-providers.destroy",
                "action": "User\Providers\UserServerProviderController@destroy"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/repository-providers",
                "name": "repository-providers.index",
                "action": "User\Providers\UserRepositoryProviderController@index"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/repository-providers\/create",
                "name": "repository-providers.create",
                "action": "User\Providers\UserRepositoryProviderController@create"
            }, {
                "host": null,
                "methods": ["POST"],
                "uri": "api\/my\/repository-providers",
                "name": "repository-providers.store",
                "action": "User\Providers\UserRepositoryProviderController@store"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/repository-providers\/{repository_provider}",
                "name": "repository-providers.show",
                "action": "User\Providers\UserRepositoryProviderController@show"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/repository-providers\/{repository_provider}\/edit",
                "name": "repository-providers.edit",
                "action": "User\Providers\UserRepositoryProviderController@edit"
            }, {
                "host": null,
                "methods": ["PUT", "PATCH"],
                "uri": "api\/my\/repository-providers\/{repository_provider}",
                "name": "repository-providers.update",
                "action": "User\Providers\UserRepositoryProviderController@update"
            }, {
                "host": null,
                "methods": ["DELETE"],
                "uri": "api\/my\/repository-providers\/{repository_provider}",
                "name": "repository-providers.destroy",
                "action": "User\Providers\UserRepositoryProviderController@destroy"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/notification-providers",
                "name": "notification-providers.index",
                "action": "User\Providers\UserNotificationProviderController@index"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/notification-providers\/create",
                "name": "notification-providers.create",
                "action": "User\Providers\UserNotificationProviderController@create"
            }, {
                "host": null,
                "methods": ["POST"],
                "uri": "api\/my\/notification-providers",
                "name": "notification-providers.store",
                "action": "User\Providers\UserNotificationProviderController@store"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/notification-providers\/{notification_provider}",
                "name": "notification-providers.show",
                "action": "User\Providers\UserNotificationProviderController@show"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/notification-providers\/{notification_provider}\/edit",
                "name": "notification-providers.edit",
                "action": "User\Providers\UserNotificationProviderController@edit"
            }, {
                "host": null,
                "methods": ["PUT", "PATCH"],
                "uri": "api\/my\/notification-providers\/{notification_provider}",
                "name": "notification-providers.update",
                "action": "User\Providers\UserNotificationProviderController@update"
            }, {
                "host": null,
                "methods": ["DELETE"],
                "uri": "api\/my\/notification-providers\/{notification_provider}",
                "name": "notification-providers.destroy",
                "action": "User\Providers\UserNotificationProviderController@destroy"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/events",
                "name": "events.index",
                "action": "EventController@index"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/events\/create",
                "name": "events.create",
                "action": "EventController@create"
            }, {
                "host": null,
                "methods": ["POST"],
                "uri": "api\/my\/events",
                "name": "events.store",
                "action": "EventController@store"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/events\/{event}",
                "name": "events.show",
                "action": "EventController@show"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/events\/{event}\/edit",
                "name": "events.edit",
                "action": "EventController@edit"
            }, {
                "host": null,
                "methods": ["PUT", "PATCH"],
                "uri": "api\/my\/events\/{event}",
                "name": "events.update",
                "action": "EventController@update"
            }, {
                "host": null,
                "methods": ["DELETE"],
                "uri": "api\/my\/events\/{event}",
                "name": "events.destroy",
                "action": "EventController@destroy"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/team",
                "name": "team.index",
                "action": "User\Team\UserTeamController@index"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/team\/create",
                "name": "team.create",
                "action": "User\Team\UserTeamController@create"
            }, {
                "host": null,
                "methods": ["POST"],
                "uri": "api\/my\/team",
                "name": "team.store",
                "action": "User\Team\UserTeamController@store"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/team\/{team}",
                "name": "team.show",
                "action": "User\Team\UserTeamController@show"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/team\/{team}\/edit",
                "name": "team.edit",
                "action": "User\Team\UserTeamController@edit"
            }, {
                "host": null,
                "methods": ["PUT", "PATCH"],
                "uri": "api\/my\/team\/{team}",
                "name": "team.update",
                "action": "User\Team\UserTeamController@update"
            }, {
                "host": null,
                "methods": ["DELETE"],
                "uri": "api\/my\/team\/{team}",
                "name": "team.destroy",
                "action": "User\Team\UserTeamController@destroy"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/team\/team\/{team}\/members",
                "name": "team.members.index",
                "action": "User\Team\UserTeamMemberController@index"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/team\/team\/{team}\/members\/create",
                "name": "team.members.create",
                "action": "User\Team\UserTeamMemberController@create"
            }, {
                "host": null,
                "methods": ["POST"],
                "uri": "api\/my\/team\/team\/{team}\/members",
                "name": "team.members.store",
                "action": "User\Team\UserTeamMemberController@store"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/team\/team\/{team}\/members\/{member}",
                "name": "team.members.show",
                "action": "User\Team\UserTeamMemberController@show"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/team\/team\/{team}\/members\/{member}\/edit",
                "name": "team.members.edit",
                "action": "User\Team\UserTeamMemberController@edit"
            }, {
                "host": null,
                "methods": ["PUT", "PATCH"],
                "uri": "api\/my\/team\/team\/{team}\/members\/{member}",
                "name": "team.members.update",
                "action": "User\Team\UserTeamMemberController@update"
            }, {
                "host": null,
                "methods": ["DELETE"],
                "uri": "api\/my\/team\/team\/{team}\/members\/{member}",
                "name": "team.members.destroy",
                "action": "User\Team\UserTeamMemberController@destroy"
            }, {
                "host": null,
                "methods": ["POST"],
                "uri": "api\/my\/team\/switch\/{id?}",
                "name": "teams.switch",
                "action": "User\Team\UserTeamController@switchTeam"
            }, {
                "host": null,
                "methods": ["POST"],
                "uri": "api\/my\/team\/members",
                "name": "teams.members.invite",
                "action": "User\Team\UserTeamMemberController@invite"
            }, {
                "host": null,
                "methods": ["POST"],
                "uri": "api\/my\/team\/members\/resend\/{invite_id}",
                "name": "teams.members.resend_invite",
                "action": "User\Team\UserTeamMemberController@resendInvite"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/piles",
                "name": "piles.index",
                "action": "Pile\PileController@index"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/piles\/create",
                "name": "piles.create",
                "action": "Pile\PileController@create"
            }, {
                "host": null,
                "methods": ["POST"],
                "uri": "api\/my\/piles",
                "name": "piles.store",
                "action": "Pile\PileController@store"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/piles\/{pile}",
                "name": "piles.show",
                "action": "Pile\PileController@show"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/piles\/{pile}\/edit",
                "name": "piles.edit",
                "action": "Pile\PileController@edit"
            }, {
                "host": null,
                "methods": ["PUT", "PATCH"],
                "uri": "api\/my\/piles\/{pile}",
                "name": "piles.update",
                "action": "Pile\PileController@update"
            }, {
                "host": null,
                "methods": ["DELETE"],
                "uri": "api\/my\/piles\/{pile}",
                "name": "piles.destroy",
                "action": "Pile\PileController@destroy"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/pile\/{pile}\/sites",
                "name": "pile.sites.index",
                "action": "Pile\PileSitesController@index"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/pile\/{pile}\/sites\/create",
                "name": "pile.sites.create",
                "action": "Pile\PileSitesController@create"
            }, {
                "host": null,
                "methods": ["POST"],
                "uri": "api\/my\/pile\/{pile}\/sites",
                "name": "pile.sites.store",
                "action": "Pile\PileSitesController@store"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/pile\/{pile}\/sites\/{site}",
                "name": "pile.sites.show",
                "action": "Pile\PileSitesController@show"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/pile\/{pile}\/sites\/{site}\/edit",
                "name": "pile.sites.edit",
                "action": "Pile\PileSitesController@edit"
            }, {
                "host": null,
                "methods": ["PUT", "PATCH"],
                "uri": "api\/my\/pile\/{pile}\/sites\/{site}",
                "name": "pile.sites.update",
                "action": "Pile\PileSitesController@update"
            }, {
                "host": null,
                "methods": ["DELETE"],
                "uri": "api\/my\/pile\/{pile}\/sites\/{site}",
                "name": "pile.sites.destroy",
                "action": "Pile\PileSitesController@destroy"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/servers",
                "name": "servers.index",
                "action": "Server\ServerController@index"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/servers\/create",
                "name": "servers.create",
                "action": "Server\ServerController@create"
            }, {
                "host": null,
                "methods": ["POST"],
                "uri": "api\/my\/servers",
                "name": "servers.store",
                "action": "Server\ServerController@store"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/servers\/{server}",
                "name": "servers.show",
                "action": "Server\ServerController@show"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/servers\/{server}\/edit",
                "name": "servers.edit",
                "action": "Server\ServerController@edit"
            }, {
                "host": null,
                "methods": ["PUT", "PATCH"],
                "uri": "api\/my\/servers\/{server}",
                "name": "servers.update",
                "action": "Server\ServerController@update"
            }, {
                "host": null,
                "methods": ["DELETE"],
                "uri": "api\/my\/servers\/{server}",
                "name": "servers.destroy",
                "action": "Server\ServerController@destroy"
            }, {
                "host": null,
                "methods": ["POST"],
                "uri": "api\/my\/server\/restore\/{server}",
                "name": null,
                "action": "Server\ServerController@restore"
            }, {
                "host": null,
                "methods": ["POST"],
                "uri": "api\/my\/server\/{server}\/file",
                "name": null,
                "action": "Server\ServerController@getFile"
            }, {
                "host": null,
                "methods": ["POST"],
                "uri": "api\/my\/server\/{server}\/file\/save",
                "name": null,
                "action": "Server\ServerController@saveFile"
            }, {
                "host": null,
                "methods": ["POST"],
                "uri": "api\/my\/server\/disk-space\/{server}",
                "name": null,
                "action": "Server\ServerController@getDiskSpace"
            }, {
                "host": null,
                "methods": ["POST"],
                "uri": "api\/my\/server\/restart-server\/{server}",
                "name": null,
                "action": "Server\ServerController@restartServer"
            }, {
                "host": null,
                "methods": ["POST"],
                "uri": "api\/my\/server\/restart-database\/{server}",
                "name": null,
                "action": "Server\ServerController@restartDatabases"
            }, {
                "host": null,
                "methods": ["POST"],
                "uri": "api\/my\/server\/restart-workers\/{server}",
                "name": null,
                "action": "Server\ServerController@restartWorkerServices"
            }, {
                "host": null,
                "methods": ["POST"],
                "uri": "api\/my\/server\/ssh-connection\/{server}",
                "name": null,
                "action": "Server\ServerSshKeyController@testSSHConnection"
            }, {
                "host": null,
                "methods": ["POST"],
                "uri": "api\/my\/server\/restart-web-services\/{server}",
                "name": null,
                "action": "Server\ServerController@restartWebServices"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/servers\/{server}\/features",
                "name": "servers.features.index",
                "action": "Server\ServerFeatureController@index"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/servers\/{server}\/features\/create",
                "name": "servers.features.create",
                "action": "Server\ServerFeatureController@create"
            }, {
                "host": null,
                "methods": ["POST"],
                "uri": "api\/my\/servers\/{server}\/features",
                "name": "servers.features.store",
                "action": "Server\ServerFeatureController@store"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/servers\/{server}\/features\/{feature}",
                "name": "servers.features.show",
                "action": "Server\ServerFeatureController@show"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/servers\/{server}\/features\/{feature}\/edit",
                "name": "servers.features.edit",
                "action": "Server\ServerFeatureController@edit"
            }, {
                "host": null,
                "methods": ["PUT", "PATCH"],
                "uri": "api\/my\/servers\/{server}\/features\/{feature}",
                "name": "servers.features.update",
                "action": "Server\ServerFeatureController@update"
            }, {
                "host": null,
                "methods": ["DELETE"],
                "uri": "api\/my\/servers\/{server}\/features\/{feature}",
                "name": "servers.features.destroy",
                "action": "Server\ServerFeatureController@destroy"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/servers\/{server}\/cron-jobs",
                "name": "servers.cron-jobs.index",
                "action": "Server\ServerCronJobController@index"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/servers\/{server}\/cron-jobs\/create",
                "name": "servers.cron-jobs.create",
                "action": "Server\ServerCronJobController@create"
            }, {
                "host": null,
                "methods": ["POST"],
                "uri": "api\/my\/servers\/{server}\/cron-jobs",
                "name": "servers.cron-jobs.store",
                "action": "Server\ServerCronJobController@store"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/servers\/{server}\/cron-jobs\/{cron_job}",
                "name": "servers.cron-jobs.show",
                "action": "Server\ServerCronJobController@show"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/servers\/{server}\/cron-jobs\/{cron_job}\/edit",
                "name": "servers.cron-jobs.edit",
                "action": "Server\ServerCronJobController@edit"
            }, {
                "host": null,
                "methods": ["PUT", "PATCH"],
                "uri": "api\/my\/servers\/{server}\/cron-jobs\/{cron_job}",
                "name": "servers.cron-jobs.update",
                "action": "Server\ServerCronJobController@update"
            }, {
                "host": null,
                "methods": ["DELETE"],
                "uri": "api\/my\/servers\/{server}\/cron-jobs\/{cron_job}",
                "name": "servers.cron-jobs.destroy",
                "action": "Server\ServerCronJobController@destroy"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/servers\/{server}\/workers",
                "name": "servers.workers.index",
                "action": "Server\ServerWorkerController@index"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/servers\/{server}\/workers\/create",
                "name": "servers.workers.create",
                "action": "Server\ServerWorkerController@create"
            }, {
                "host": null,
                "methods": ["POST"],
                "uri": "api\/my\/servers\/{server}\/workers",
                "name": "servers.workers.store",
                "action": "Server\ServerWorkerController@store"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/servers\/{server}\/workers\/{worker}",
                "name": "servers.workers.show",
                "action": "Server\ServerWorkerController@show"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/servers\/{server}\/workers\/{worker}\/edit",
                "name": "servers.workers.edit",
                "action": "Server\ServerWorkerController@edit"
            }, {
                "host": null,
                "methods": ["PUT", "PATCH"],
                "uri": "api\/my\/servers\/{server}\/workers\/{worker}",
                "name": "servers.workers.update",
                "action": "Server\ServerWorkerController@update"
            }, {
                "host": null,
                "methods": ["DELETE"],
                "uri": "api\/my\/servers\/{server}\/workers\/{worker}",
                "name": "servers.workers.destroy",
                "action": "Server\ServerWorkerController@destroy"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/servers\/{server}\/firewall",
                "name": "servers.firewall.index",
                "action": "Server\ServerFirewallController@index"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/servers\/{server}\/firewall\/create",
                "name": "servers.firewall.create",
                "action": "Server\ServerFirewallController@create"
            }, {
                "host": null,
                "methods": ["POST"],
                "uri": "api\/my\/servers\/{server}\/firewall",
                "name": "servers.firewall.store",
                "action": "Server\ServerFirewallController@store"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/servers\/{server}\/firewall\/{firewall}",
                "name": "servers.firewall.show",
                "action": "Server\ServerFirewallController@show"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/servers\/{server}\/firewall\/{firewall}\/edit",
                "name": "servers.firewall.edit",
                "action": "Server\ServerFirewallController@edit"
            }, {
                "host": null,
                "methods": ["PUT", "PATCH"],
                "uri": "api\/my\/servers\/{server}\/firewall\/{firewall}",
                "name": "servers.firewall.update",
                "action": "Server\ServerFirewallController@update"
            }, {
                "host": null,
                "methods": ["DELETE"],
                "uri": "api\/my\/servers\/{server}\/firewall\/{firewall}",
                "name": "servers.firewall.destroy",
                "action": "Server\ServerFirewallController@destroy"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/servers\/{server}\/network",
                "name": "servers.network.index",
                "action": "Server\ServerNetworkController@index"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/servers\/{server}\/network\/create",
                "name": "servers.network.create",
                "action": "Server\ServerNetworkController@create"
            }, {
                "host": null,
                "methods": ["POST"],
                "uri": "api\/my\/servers\/{server}\/network",
                "name": "servers.network.store",
                "action": "Server\ServerNetworkController@store"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/servers\/{server}\/network\/{network}",
                "name": "servers.network.show",
                "action": "Server\ServerNetworkController@show"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/servers\/{server}\/network\/{network}\/edit",
                "name": "servers.network.edit",
                "action": "Server\ServerNetworkController@edit"
            }, {
                "host": null,
                "methods": ["PUT", "PATCH"],
                "uri": "api\/my\/servers\/{server}\/network\/{network}",
                "name": "servers.network.update",
                "action": "Server\ServerNetworkController@update"
            }, {
                "host": null,
                "methods": ["DELETE"],
                "uri": "api\/my\/servers\/{server}\/network\/{network}",
                "name": "servers.network.destroy",
                "action": "Server\ServerNetworkController@destroy"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/servers\/{server}\/ssh-keys",
                "name": "servers.ssh-keys.index",
                "action": "Server\ServerSshKeyController@index"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/servers\/{server}\/ssh-keys\/create",
                "name": "servers.ssh-keys.create",
                "action": "Server\ServerSshKeyController@create"
            }, {
                "host": null,
                "methods": ["POST"],
                "uri": "api\/my\/servers\/{server}\/ssh-keys",
                "name": "servers.ssh-keys.store",
                "action": "Server\ServerSshKeyController@store"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/servers\/{server}\/ssh-keys\/{ssh_key}",
                "name": "servers.ssh-keys.show",
                "action": "Server\ServerSshKeyController@show"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/servers\/{server}\/ssh-keys\/{ssh_key}\/edit",
                "name": "servers.ssh-keys.edit",
                "action": "Server\ServerSshKeyController@edit"
            }, {
                "host": null,
                "methods": ["PUT", "PATCH"],
                "uri": "api\/my\/servers\/{server}\/ssh-keys\/{ssh_key}",
                "name": "servers.ssh-keys.update",
                "action": "Server\ServerSshKeyController@update"
            }, {
                "host": null,
                "methods": ["DELETE"],
                "uri": "api\/my\/servers\/{server}\/ssh-keys\/{ssh_key}",
                "name": "servers.ssh-keys.destroy",
                "action": "Server\ServerSshKeyController@destroy"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/servers\/{server}\/sites",
                "name": "servers.sites.index",
                "action": "Server\ServerSiteController@index"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/servers\/{server}\/sites\/create",
                "name": "servers.sites.create",
                "action": "Server\ServerSiteController@create"
            }, {
                "host": null,
                "methods": ["POST"],
                "uri": "api\/my\/servers\/{server}\/sites",
                "name": "servers.sites.store",
                "action": "Server\ServerSiteController@store"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/servers\/{server}\/sites\/{site}",
                "name": "servers.sites.show",
                "action": "Server\ServerSiteController@show"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/servers\/{server}\/sites\/{site}\/edit",
                "name": "servers.sites.edit",
                "action": "Server\ServerSiteController@edit"
            }, {
                "host": null,
                "methods": ["PUT", "PATCH"],
                "uri": "api\/my\/servers\/{server}\/sites\/{site}",
                "name": "servers.sites.update",
                "action": "Server\ServerSiteController@update"
            }, {
                "host": null,
                "methods": ["DELETE"],
                "uri": "api\/my\/servers\/{server}\/sites\/{site}",
                "name": "servers.sites.destroy",
                "action": "Server\ServerSiteController@destroy"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/sites",
                "name": "sites.index",
                "action": "Site\SiteController@index"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/sites\/create",
                "name": "sites.create",
                "action": "Site\SiteController@create"
            }, {
                "host": null,
                "methods": ["POST"],
                "uri": "api\/my\/sites",
                "name": "sites.store",
                "action": "Site\SiteController@store"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/sites\/{site}",
                "name": "sites.show",
                "action": "Site\SiteController@show"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/sites\/{site}\/edit",
                "name": "sites.edit",
                "action": "Site\SiteController@edit"
            }, {
                "host": null,
                "methods": ["PUT", "PATCH"],
                "uri": "api\/my\/sites\/{site}",
                "name": "sites.update",
                "action": "Site\SiteController@update"
            }, {
                "host": null,
                "methods": ["DELETE"],
                "uri": "api\/my\/sites\/{site}",
                "name": "sites.destroy",
                "action": "Site\SiteController@destroy"
            }, {
                "host": null,
                "methods": ["POST"],
                "uri": "api\/my\/site\/{site}\/find-file",
                "name": null,
                "action": "Site\SiteFileController@find"
            }, {
                "host": null,
                "methods": ["PUT"],
                "uri": "api\/my\/site\/{site}\/update\/server-features",
                "name": null,
                "action": "Site\SiteController@updateSiteServerFeatures"
            }, {
                "host": null,
                "methods": ["POST"],
                "uri": "api\/my\/sites\/deploy",
                "name": null,
                "action": "Site\SiteController@deploy"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/site\/{site}\/file",
                "name": "site.file.index",
                "action": "Site\SiteFileController@index"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/site\/{site}\/file\/create",
                "name": "site.file.create",
                "action": "Site\SiteFileController@create"
            }, {
                "host": null,
                "methods": ["POST"],
                "uri": "api\/my\/site\/{site}\/file",
                "name": "site.file.store",
                "action": "Site\SiteFileController@store"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/site\/{site}\/file\/{file}",
                "name": "site.file.show",
                "action": "Site\SiteFileController@show"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/site\/{site}\/file\/{file}\/edit",
                "name": "site.file.edit",
                "action": "Site\SiteFileController@edit"
            }, {
                "host": null,
                "methods": ["PUT", "PATCH"],
                "uri": "api\/my\/site\/{site}\/file\/{file}",
                "name": "site.file.update",
                "action": "Site\SiteFileController@update"
            }, {
                "host": null,
                "methods": ["DELETE"],
                "uri": "api\/my\/site\/{site}\/file\/{file}",
                "name": "site.file.destroy",
                "action": "Site\SiteFileController@destroy"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/site\/{site}\/servers",
                "name": "site.servers.index",
                "action": "Site\SiteServerController@index"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/site\/{site}\/servers\/create",
                "name": "site.servers.create",
                "action": "Site\SiteServerController@create"
            }, {
                "host": null,
                "methods": ["POST"],
                "uri": "api\/my\/site\/{site}\/servers",
                "name": "site.servers.store",
                "action": "Site\SiteServerController@store"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/site\/{site}\/servers\/{server}",
                "name": "site.servers.show",
                "action": "Site\SiteServerController@show"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/site\/{site}\/servers\/{server}\/edit",
                "name": "site.servers.edit",
                "action": "Site\SiteServerController@edit"
            }, {
                "host": null,
                "methods": ["PUT", "PATCH"],
                "uri": "api\/my\/site\/{site}\/servers\/{server}",
                "name": "site.servers.update",
                "action": "Site\SiteServerController@update"
            }, {
                "host": null,
                "methods": ["DELETE"],
                "uri": "api\/my\/site\/{site}\/servers\/{server}",
                "name": "site.servers.destroy",
                "action": "Site\SiteServerController@destroy"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/site\/{site}\/workers",
                "name": "site.workers.index",
                "action": "Site\SiteWorkerController@index"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/site\/{site}\/workers\/create",
                "name": "site.workers.create",
                "action": "Site\SiteWorkerController@create"
            }, {
                "host": null,
                "methods": ["POST"],
                "uri": "api\/my\/site\/{site}\/workers",
                "name": "site.workers.store",
                "action": "Site\SiteWorkerController@store"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/site\/{site}\/workers\/{worker}",
                "name": "site.workers.show",
                "action": "Site\SiteWorkerController@show"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/site\/{site}\/workers\/{worker}\/edit",
                "name": "site.workers.edit",
                "action": "Site\SiteWorkerController@edit"
            }, {
                "host": null,
                "methods": ["PUT", "PATCH"],
                "uri": "api\/my\/site\/{site}\/workers\/{worker}",
                "name": "site.workers.update",
                "action": "Site\SiteWorkerController@update"
            }, {
                "host": null,
                "methods": ["DELETE"],
                "uri": "api\/my\/site\/{site}\/workers\/{worker}",
                "name": "site.workers.destroy",
                "action": "Site\SiteWorkerController@destroy"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/site\/{site}\/hooks",
                "name": "site.hooks.index",
                "action": "Site\Repository\RepositoryHookController@index"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/site\/{site}\/hooks\/create",
                "name": "site.hooks.create",
                "action": "Site\Repository\RepositoryHookController@create"
            }, {
                "host": null,
                "methods": ["POST"],
                "uri": "api\/my\/site\/{site}\/hooks",
                "name": "site.hooks.store",
                "action": "Site\Repository\RepositoryHookController@store"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/site\/{site}\/hooks\/{hook}",
                "name": "site.hooks.show",
                "action": "Site\Repository\RepositoryHookController@show"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/site\/{site}\/hooks\/{hook}\/edit",
                "name": "site.hooks.edit",
                "action": "Site\Repository\RepositoryHookController@edit"
            }, {
                "host": null,
                "methods": ["PUT", "PATCH"],
                "uri": "api\/my\/site\/{site}\/hooks\/{hook}",
                "name": "site.hooks.update",
                "action": "Site\Repository\RepositoryHookController@update"
            }, {
                "host": null,
                "methods": ["DELETE"],
                "uri": "api\/my\/site\/{site}\/hooks\/{hook}",
                "name": "site.hooks.destroy",
                "action": "Site\Repository\RepositoryHookController@destroy"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/site\/{site}\/certificate",
                "name": "site.certificate.index",
                "action": "Site\Certificate\SiteSSLController@index"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/site\/{site}\/certificate\/create",
                "name": "site.certificate.create",
                "action": "Site\Certificate\SiteSSLController@create"
            }, {
                "host": null,
                "methods": ["POST"],
                "uri": "api\/my\/site\/{site}\/certificate",
                "name": "site.certificate.store",
                "action": "Site\Certificate\SiteSSLController@store"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/site\/{site}\/certificate\/{certificate}",
                "name": "site.certificate.show",
                "action": "Site\Certificate\SiteSSLController@show"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/site\/{site}\/certificate\/{certificate}\/edit",
                "name": "site.certificate.edit",
                "action": "Site\Certificate\SiteSSLController@edit"
            }, {
                "host": null,
                "methods": ["PUT", "PATCH"],
                "uri": "api\/my\/site\/{site}\/certificate\/{certificate}",
                "name": "site.certificate.update",
                "action": "Site\Certificate\SiteSSLController@update"
            }, {
                "host": null,
                "methods": ["DELETE"],
                "uri": "api\/my\/site\/{site}\/certificate\/{certificate}",
                "name": "site.certificate.destroy",
                "action": "Site\Certificate\SiteSSLController@destroy"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/site\/{site}\/repository",
                "name": "site.repository.index",
                "action": "Site\Repository\SiteRepositoryController@index"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/site\/{site}\/repository\/create",
                "name": "site.repository.create",
                "action": "Site\Repository\SiteRepositoryController@create"
            }, {
                "host": null,
                "methods": ["POST"],
                "uri": "api\/my\/site\/{site}\/repository",
                "name": "site.repository.store",
                "action": "Site\Repository\SiteRepositoryController@store"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/site\/{site}\/repository\/{repository}",
                "name": "site.repository.show",
                "action": "Site\Repository\SiteRepositoryController@show"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/site\/{site}\/repository\/{repository}\/edit",
                "name": "site.repository.edit",
                "action": "Site\Repository\SiteRepositoryController@edit"
            }, {
                "host": null,
                "methods": ["PUT", "PATCH"],
                "uri": "api\/my\/site\/{site}\/repository\/{repository}",
                "name": "site.repository.update",
                "action": "Site\Repository\SiteRepositoryController@update"
            }, {
                "host": null,
                "methods": ["DELETE"],
                "uri": "api\/my\/site\/{site}\/repository\/{repository}",
                "name": "site.repository.destroy",
                "action": "Site\Repository\SiteRepositoryController@destroy"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/site\/{site}\/certificate-existing",
                "name": "site.certificate-existing.index",
                "action": "Site\Certificate\SiteSSLExistingController@index"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/site\/{site}\/certificate-existing\/create",
                "name": "site.certificate-existing.create",
                "action": "Site\Certificate\SiteSSLExistingController@create"
            }, {
                "host": null,
                "methods": ["POST"],
                "uri": "api\/my\/site\/{site}\/certificate-existing",
                "name": "site.certificate-existing.store",
                "action": "Site\Certificate\SiteSSLExistingController@store"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/site\/{site}\/certificate-existing\/{certificate_existing}",
                "name": "site.certificate-existing.show",
                "action": "Site\Certificate\SiteSSLExistingController@show"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/site\/{site}\/certificate-existing\/{certificate_existing}\/edit",
                "name": "site.certificate-existing.edit",
                "action": "Site\Certificate\SiteSSLExistingController@edit"
            }, {
                "host": null,
                "methods": ["PUT", "PATCH"],
                "uri": "api\/my\/site\/{site}\/certificate-existing\/{certificate_existing}",
                "name": "site.certificate-existing.update",
                "action": "Site\Certificate\SiteSSLExistingController@update"
            }, {
                "host": null,
                "methods": ["DELETE"],
                "uri": "api\/my\/site\/{site}\/certificate-existing\/{certificate_existing}",
                "name": "site.certificate-existing.destroy",
                "action": "Site\Certificate\SiteSSLExistingController@destroy"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/site\/{site}\/certificate-lets-encrypt",
                "name": "site.certificate-lets-encrypt.index",
                "action": "Site\Certificate\SiteSSLLetsEncryptController@index"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/site\/{site}\/certificate-lets-encrypt\/create",
                "name": "site.certificate-lets-encrypt.create",
                "action": "Site\Certificate\SiteSSLLetsEncryptController@create"
            }, {
                "host": null,
                "methods": ["POST"],
                "uri": "api\/my\/site\/{site}\/certificate-lets-encrypt",
                "name": "site.certificate-lets-encrypt.store",
                "action": "Site\Certificate\SiteSSLLetsEncryptController@store"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/site\/{site}\/certificate-lets-encrypt\/{certificate_lets_encrypt}",
                "name": "site.certificate-lets-encrypt.show",
                "action": "Site\Certificate\SiteSSLLetsEncryptController@show"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/my\/site\/{site}\/certificate-lets-encrypt\/{certificate_lets_encrypt}\/edit",
                "name": "site.certificate-lets-encrypt.edit",
                "action": "Site\Certificate\SiteSSLLetsEncryptController@edit"
            }, {
                "host": null,
                "methods": ["PUT", "PATCH"],
                "uri": "api\/my\/site\/{site}\/certificate-lets-encrypt\/{certificate_lets_encrypt}",
                "name": "site.certificate-lets-encrypt.update",
                "action": "Site\Certificate\SiteSSLLetsEncryptController@update"
            }, {
                "host": null,
                "methods": ["DELETE"],
                "uri": "api\/my\/site\/{site}\/certificate-lets-encrypt\/{certificate_lets_encrypt}",
                "name": "site.certificate-lets-encrypt.destroy",
                "action": "Site\Certificate\SiteSSLLetsEncryptController@destroy"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/server\/languages",
                "name": null,
                "action": "Server\ServerFeatureController@getLanguages"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/server\/frameworks",
                "name": null,
                "action": "Server\ServerFeatureController@getFrameworks"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/server\/features",
                "name": null,
                "action": "Server\ServerFeatureController@getServerFeatures"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/server\/{server}\/editable-files",
                "name": null,
                "action": "Server\ServerFeatureController@getEditableServerFiles"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/site\/{site}\/framework\/editable-files",
                "name": null,
                "action": "Server\ServerFeatureController@getEditableFrameworkFiles"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/auth\/providers\/server-providers",
                "name": "server-providers.index",
                "action": "Auth\Providers\ServerProvidersController@index"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/auth\/providers\/server-providers\/create",
                "name": "server-providers.create",
                "action": "Auth\Providers\ServerProvidersController@create"
            }, {
                "host": null,
                "methods": ["POST"],
                "uri": "api\/auth\/providers\/server-providers",
                "name": "server-providers.store",
                "action": "Auth\Providers\ServerProvidersController@store"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/auth\/providers\/server-providers\/{server_provider}",
                "name": "server-providers.show",
                "action": "Auth\Providers\ServerProvidersController@show"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/auth\/providers\/server-providers\/{server_provider}\/edit",
                "name": "server-providers.edit",
                "action": "Auth\Providers\ServerProvidersController@edit"
            }, {
                "host": null,
                "methods": ["PUT", "PATCH"],
                "uri": "api\/auth\/providers\/server-providers\/{server_provider}",
                "name": "server-providers.update",
                "action": "Auth\Providers\ServerProvidersController@update"
            }, {
                "host": null,
                "methods": ["DELETE"],
                "uri": "api\/auth\/providers\/server-providers\/{server_provider}",
                "name": "server-providers.destroy",
                "action": "Auth\Providers\ServerProvidersController@destroy"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/auth\/providers\/repository-providers",
                "name": "repository-providers.index",
                "action": "Auth\Providers\RepositoryProvidersController@index"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/auth\/providers\/repository-providers\/create",
                "name": "repository-providers.create",
                "action": "Auth\Providers\RepositoryProvidersController@create"
            }, {
                "host": null,
                "methods": ["POST"],
                "uri": "api\/auth\/providers\/repository-providers",
                "name": "repository-providers.store",
                "action": "Auth\Providers\RepositoryProvidersController@store"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/auth\/providers\/repository-providers\/{repository_provider}",
                "name": "repository-providers.show",
                "action": "Auth\Providers\RepositoryProvidersController@show"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/auth\/providers\/repository-providers\/{repository_provider}\/edit",
                "name": "repository-providers.edit",
                "action": "Auth\Providers\RepositoryProvidersController@edit"
            }, {
                "host": null,
                "methods": ["PUT", "PATCH"],
                "uri": "api\/auth\/providers\/repository-providers\/{repository_provider}",
                "name": "repository-providers.update",
                "action": "Auth\Providers\RepositoryProvidersController@update"
            }, {
                "host": null,
                "methods": ["DELETE"],
                "uri": "api\/auth\/providers\/repository-providers\/{repository_provider}",
                "name": "repository-providers.destroy",
                "action": "Auth\Providers\RepositoryProvidersController@destroy"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/auth\/providers\/notification-providers",
                "name": "notification-providers.index",
                "action": "Auth\Providers\NotificationProvidersController@index"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/auth\/providers\/notification-providers\/create",
                "name": "notification-providers.create",
                "action": "Auth\Providers\NotificationProvidersController@create"
            }, {
                "host": null,
                "methods": ["POST"],
                "uri": "api\/auth\/providers\/notification-providers",
                "name": "notification-providers.store",
                "action": "Auth\Providers\NotificationProvidersController@store"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/auth\/providers\/notification-providers\/{notification_provider}",
                "name": "notification-providers.show",
                "action": "Auth\Providers\NotificationProvidersController@show"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/auth\/providers\/notification-providers\/{notification_provider}\/edit",
                "name": "notification-providers.edit",
                "action": "Auth\Providers\NotificationProvidersController@edit"
            }, {
                "host": null,
                "methods": ["PUT", "PATCH"],
                "uri": "api\/auth\/providers\/notification-providers\/{notification_provider}",
                "name": "notification-providers.update",
                "action": "Auth\Providers\NotificationProvidersController@update"
            }, {
                "host": null,
                "methods": ["DELETE"],
                "uri": "api\/auth\/providers\/notification-providers\/{notification_provider}",
                "name": "notification-providers.destroy",
                "action": "Auth\Providers\NotificationProvidersController@destroy"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/server\/providers\/digitalocean\/options",
                "name": "options.index",
                "action": "Server\Providers\DigitalOcean\DigitalOceanServerOptionsController@index"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/server\/providers\/digitalocean\/options\/create",
                "name": "options.create",
                "action": "Server\Providers\DigitalOcean\DigitalOceanServerOptionsController@create"
            }, {
                "host": null,
                "methods": ["POST"],
                "uri": "api\/server\/providers\/digitalocean\/options",
                "name": "options.store",
                "action": "Server\Providers\DigitalOcean\DigitalOceanServerOptionsController@store"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/server\/providers\/digitalocean\/options\/{option}",
                "name": "options.show",
                "action": "Server\Providers\DigitalOcean\DigitalOceanServerOptionsController@show"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/server\/providers\/digitalocean\/options\/{option}\/edit",
                "name": "options.edit",
                "action": "Server\Providers\DigitalOcean\DigitalOceanServerOptionsController@edit"
            }, {
                "host": null,
                "methods": ["PUT", "PATCH"],
                "uri": "api\/server\/providers\/digitalocean\/options\/{option}",
                "name": "options.update",
                "action": "Server\Providers\DigitalOcean\DigitalOceanServerOptionsController@update"
            }, {
                "host": null,
                "methods": ["DELETE"],
                "uri": "api\/server\/providers\/digitalocean\/options\/{option}",
                "name": "options.destroy",
                "action": "Server\Providers\DigitalOcean\DigitalOceanServerOptionsController@destroy"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/server\/providers\/digitalocean\/regions",
                "name": "regions.index",
                "action": "Server\Providers\DigitalOcean\DigitalOceanServerRegionsController@index"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/server\/providers\/digitalocean\/regions\/create",
                "name": "regions.create",
                "action": "Server\Providers\DigitalOcean\DigitalOceanServerRegionsController@create"
            }, {
                "host": null,
                "methods": ["POST"],
                "uri": "api\/server\/providers\/digitalocean\/regions",
                "name": "regions.store",
                "action": "Server\Providers\DigitalOcean\DigitalOceanServerRegionsController@store"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/server\/providers\/digitalocean\/regions\/{region}",
                "name": "regions.show",
                "action": "Server\Providers\DigitalOcean\DigitalOceanServerRegionsController@show"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/server\/providers\/digitalocean\/regions\/{region}\/edit",
                "name": "regions.edit",
                "action": "Server\Providers\DigitalOcean\DigitalOceanServerRegionsController@edit"
            }, {
                "host": null,
                "methods": ["PUT", "PATCH"],
                "uri": "api\/server\/providers\/digitalocean\/regions\/{region}",
                "name": "regions.update",
                "action": "Server\Providers\DigitalOcean\DigitalOceanServerRegionsController@update"
            }, {
                "host": null,
                "methods": ["DELETE"],
                "uri": "api\/server\/providers\/digitalocean\/regions\/{region}",
                "name": "regions.destroy",
                "action": "Server\Providers\DigitalOcean\DigitalOceanServerRegionsController@destroy"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/server\/providers\/digitalocean\/features",
                "name": "features.index",
                "action": "Server\Providers\DigitalOcean\DigitalOceanServerFeaturesController@index"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/server\/providers\/digitalocean\/features\/create",
                "name": "features.create",
                "action": "Server\Providers\DigitalOcean\DigitalOceanServerFeaturesController@create"
            }, {
                "host": null,
                "methods": ["POST"],
                "uri": "api\/server\/providers\/digitalocean\/features",
                "name": "features.store",
                "action": "Server\Providers\DigitalOcean\DigitalOceanServerFeaturesController@store"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/server\/providers\/digitalocean\/features\/{feature}",
                "name": "features.show",
                "action": "Server\Providers\DigitalOcean\DigitalOceanServerFeaturesController@show"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "api\/server\/providers\/digitalocean\/features\/{feature}\/edit",
                "name": "features.edit",
                "action": "Server\Providers\DigitalOcean\DigitalOceanServerFeaturesController@edit"
            }, {
                "host": null,
                "methods": ["PUT", "PATCH"],
                "uri": "api\/server\/providers\/digitalocean\/features\/{feature}",
                "name": "features.update",
                "action": "Server\Providers\DigitalOcean\DigitalOceanServerFeaturesController@update"
            }, {
                "host": null,
                "methods": ["DELETE"],
                "uri": "api\/server\/providers\/digitalocean\/features\/{feature}",
                "name": "features.destroy",
                "action": "Server\Providers\DigitalOcean\DigitalOceanServerFeaturesController@destroy"
            }, {
                "host": null,
                "methods": ["POST"],
                "uri": "stripe\/webhook",
                "name": null,
                "action": "\Laravel\Cashier\Http\Controllers\WebhookController@handleWebhook"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "subscription\/plans",
                "name": "plans.index",
                "action": "SubscriptionController@index"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "subscription\/plans\/create",
                "name": "plans.create",
                "action": "SubscriptionController@create"
            }, {
                "host": null,
                "methods": ["POST"],
                "uri": "subscription\/plans",
                "name": "plans.store",
                "action": "SubscriptionController@store"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "subscription\/plans\/{plan}",
                "name": "plans.show",
                "action": "SubscriptionController@show"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "subscription\/plans\/{plan}\/edit",
                "name": "plans.edit",
                "action": "SubscriptionController@edit"
            }, {
                "host": null,
                "methods": ["PUT", "PATCH"],
                "uri": "subscription\/plans\/{plan}",
                "name": "plans.update",
                "action": "SubscriptionController@update"
            }, {
                "host": null,
                "methods": ["DELETE"],
                "uri": "subscription\/plans\/{plan}",
                "name": "plans.destroy",
                "action": "SubscriptionController@destroy"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "webhook\/deploy\/{siteHashID}",
                "name": "webhook\/deploy",
                "action": "Closure"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "teams\/accept\/{token}",
                "name": "teams.accept_invite",
                "action": "User\Team\UserTeamController@acceptInvite"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "\/",
                "name": null,
                "action": "Closure"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "{any}",
                "name": null,
                "action": "Closure"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "_debugbar\/open",
                "name": "debugbar.openhandler",
                "action": "Barryvdh\Debugbar\Controllers\OpenHandlerController@handle"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "_debugbar\/clockwork\/{id}",
                "name": "debugbar.clockwork",
                "action": "Barryvdh\Debugbar\Controllers\OpenHandlerController@clockwork"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "_debugbar\/assets\/stylesheets",
                "name": "debugbar.assets.css",
                "action": "Barryvdh\Debugbar\Controllers\AssetController@css"
            }, {
                "host": null,
                "methods": ["GET", "HEAD"],
                "uri": "_debugbar\/assets\/javascript",
                "name": "debugbar.assets.js",
                "action": "Barryvdh\Debugbar\Controllers\AssetController@js"
            }],
            prefix: '',

            route: function (name, parameters, route) {
                route = route || this.getByName(name);

                if (!route) {
                    return undefined;
                }

                return this.toRoute(route, parameters);
            },

            url: function (url, parameters) {
                parameters = parameters || [];

                var uri = url + '/' + parameters.join('/');

                return this.getCorrectUrl(uri);
            },

            toRoute: function (route, parameters) {
                var uri = this.replaceNamedParameters(route.uri, parameters);
                var qs = this.getRouteQueryString(parameters);

                return this.getCorrectUrl(uri + qs);
            },

            replaceNamedParameters: function (uri, parameters) {
                uri = uri.replace(/\{(.*?)\??\}/g, function (match, key) {
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

            getRouteQueryString: function (parameters) {
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

            getByName: function (name) {
                for (var key in this.routes) {
                    if (this.routes.hasOwnProperty(key) && this.routes[key].name === name) {
                        return this.routes[key];
                    }
                }
            },

            getByAction: function (action) {
                for (var key in this.routes) {
                    if (this.routes.hasOwnProperty(key) && this.routes[key].action === action) {
                        return this.routes[key];
                    }
                }
            },

            getCorrectUrl: function (uri) {
                var url = this.prefix + '/' + uri.replace(/^\/?/, '');

                if (!this.absolute)
                    return url;

                return this.rootUrl.replace('/\/?$/', '') + url;
            }
        };

        var getLinkAttributes = function (attributes) {
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

        var getHtmlLink = function (url, title, attributes) {
            title = title || url;
            attributes = getLinkAttributes(attributes);

            return '<a href="' + url + '" ' + attributes + '>' + title + '</a>';
        };

        return {
            // Generate a url for a given controller action.
            // laroute.action('HomeController@getIndex', [params = {}])
            action: function (name, parameters) {
                parameters = parameters || {};

                return routes.route(name, parameters, routes.getByAction(name));
            },

            // Generate a url for a given named route.
            // laroute.route('routeName', [params = {}])
            route: function (route, parameters) {
                parameters = parameters || {};

                return routes.route(route, parameters);
            },

            // Generate a fully qualified URL to the given path.
            // laroute.route('url', [params = {}])
            url: function (route, parameters) {
                parameters = parameters || {};

                return routes.url(route, parameters);
            },

            // Generate a html link to the given url.
            // laroute.link_to('foo/bar', [title = url], [attributes = {}])
            link_to: function (url, title, attributes) {
                url = this.url(url);

                return getHtmlLink(url, title, attributes);
            },

            // Generate a html link to the given route.
            // laroute.link_to_route('route.name', [title=url], [parameters = {}], [attributes = {}])
            link_to_route: function (route, title, parameters, attributes) {
                var url = this.route(route, parameters);

                return getHtmlLink(url, title, attributes);
            },

            // Generate a html link to the given controller action.
            // laroute.link_to_action('HomeController@getIndex', [title=url], [parameters = {}], [attributes = {}])
            link_to_action: function (action, title, parameters, attributes) {
                var url = this.action(action, parameters);

                return getHtmlLink(url, title, attributes);
            }

        };

    }).call(this);

    /**
     * Expose the class either via AMD, CommonJS or the global object
     */
    if (typeof define === 'function' && define.amd) {
        define(function () {
            return laroute;
        });
    }
    else if (typeof module === 'object' && module.exports) {
        module.exports = laroute;
    }
    else {
        window.laroute = laroute;
    }

}).call(this);

