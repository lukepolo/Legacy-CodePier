(function () {

    var laroute = (function () {

        var routes = {

            absolute: false,
            rootUrl: 'http://codepier.app',
            routes : [{"host":null,"methods":["POST"],"uri":"broadcasting\/auth","name":null,"action":"Illuminate\Broadcasting\BroadcastController@authenticate"},{"host":null,"methods":["POST"],"uri":"broadcasting\/socket","name":null,"action":"Illuminate\Broadcasting\BroadcastController@rememberSocket"},{"host":null,"methods":["GET","HEAD"],"uri":"login","name":"login","action":"Auth\LoginController@showLoginForm"},{"host":null,"methods":["POST"],"uri":"login","name":null,"action":"Auth\LoginController@login"},{"host":null,"methods":["POST"],"uri":"logout","name":null,"action":"Auth\LoginController@logout"},{"host":null,"methods":["GET","HEAD"],"uri":"register","name":null,"action":"Auth\RegisterController@showRegistrationForm"},{"host":null,"methods":["POST"],"uri":"register","name":null,"action":"Auth\RegisterController@register"},{"host":null,"methods":["GET","HEAD"],"uri":"password\/reset","name":null,"action":"Auth\ForgotPasswordController@showLinkRequestForm"},{"host":null,"methods":["POST"],"uri":"password\/email","name":null,"action":"Auth\ForgotPasswordController@sendResetLinkEmail"},{"host":null,"methods":["GET","HEAD"],"uri":"password\/reset\/{token}","name":null,"action":"Auth\ResetPasswordController@showResetForm"},{"host":null,"methods":["POST"],"uri":"password\/reset","name":null,"action":"Auth\ResetPasswordController@reset"},{"host":null,"methods":["GET","HEAD"],"uri":"\/","name":null,"action":"Closure"},{"host":null,"methods":["GET","HEAD"],"uri":"teams\/accept\/{token}","name":"teams.accept_invite","action":"UserTeamController@acceptInvite"},{"host":null,"methods":["GET","HEAD"],"uri":"provider\/{provider}\/link","name":null,"action":"Auth\OauthController@newProvider"},{"host":null,"methods":["GET","HEAD"],"uri":"provider\/{provider}\/callback","name":null,"action":"Auth\OauthController@getHandleProviderCallback"},{"host":null,"methods":["GET","HEAD"],"uri":"webhook\/deploy\/{siteHashID}","name":"webhook\/deploy","action":"Closure"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/me","name":"index","action":"User\UserController@index"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/me\/create","name":"create","action":"User\UserController@create"},{"host":null,"methods":["POST"],"uri":"api\/me","name":"store","action":"User\UserController@store"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/me\/{}","name":"show","action":"User\UserController@show"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/me\/{}\/edit","name":"edit","action":"User\UserController@edit"},{"host":null,"methods":["PUT","PATCH"],"uri":"api\/me\/{}","name":"update","action":"User\UserController@update"},{"host":null,"methods":["DELETE"],"uri":"api\/me\/{}","name":"destroy","action":"User\UserController@destroy"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/subscription","name":"subscription.index","action":"User\Subscription\UserSubscriptionController@index"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/subscription\/create","name":"subscription.create","action":"User\Subscription\UserSubscriptionController@create"},{"host":null,"methods":["POST"],"uri":"api\/my\/subscription","name":"subscription.store","action":"User\Subscription\UserSubscriptionController@store"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/subscription\/{subscription}","name":"subscription.show","action":"User\Subscription\UserSubscriptionController@show"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/subscription\/{subscription}\/edit","name":"subscription.edit","action":"User\Subscription\UserSubscriptionController@edit"},{"host":null,"methods":["PUT","PATCH"],"uri":"api\/my\/subscription\/{subscription}","name":"subscription.update","action":"User\Subscription\UserSubscriptionController@update"},{"host":null,"methods":["DELETE"],"uri":"api\/my\/subscription\/{subscription}","name":"subscription.destroy","action":"User\Subscription\UserSubscriptionController@destroy"},{"host":null,"methods":["POST"],"uri":"api\/my\/subscription\/invoice\/{invoice_id}","name":null,"action":"User\Subscription\UserSubscriptionController@downloadInvoice"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/server\/providers","name":"providers.index","action":"User\Providers\UserServerProviderController@index"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/server\/providers\/create","name":"providers.create","action":"User\Providers\UserServerProviderController@create"},{"host":null,"methods":["POST"],"uri":"api\/my\/server\/providers","name":"providers.store","action":"User\Providers\UserServerProviderController@store"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/server\/providers\/{provider}","name":"providers.show","action":"User\Providers\UserServerProviderController@show"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/server\/providers\/{provider}\/edit","name":"providers.edit","action":"User\Providers\UserServerProviderController@edit"},{"host":null,"methods":["PUT","PATCH"],"uri":"api\/my\/server\/providers\/{provider}","name":"providers.update","action":"User\Providers\UserServerProviderController@update"},{"host":null,"methods":["DELETE"],"uri":"api\/my\/server\/providers\/{provider}","name":"providers.destroy","action":"User\Providers\UserServerProviderController@destroy"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/repository\/providers","name":"providers.index","action":"User\Providers\UserRepositoryProviderController@index"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/repository\/providers\/create","name":"providers.create","action":"User\Providers\UserRepositoryProviderController@create"},{"host":null,"methods":["POST"],"uri":"api\/my\/repository\/providers","name":"providers.store","action":"User\Providers\UserRepositoryProviderController@store"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/repository\/providers\/{provider}","name":"providers.show","action":"User\Providers\UserRepositoryProviderController@show"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/repository\/providers\/{provider}\/edit","name":"providers.edit","action":"User\Providers\UserRepositoryProviderController@edit"},{"host":null,"methods":["PUT","PATCH"],"uri":"api\/my\/repository\/providers\/{provider}","name":"providers.update","action":"User\Providers\UserRepositoryProviderController@update"},{"host":null,"methods":["DELETE"],"uri":"api\/my\/repository\/providers\/{provider}","name":"providers.destroy","action":"User\Providers\UserRepositoryProviderController@destroy"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/notification\/providers","name":"providers.index","action":"User\Providers\UserNotificationProviderController@index"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/notification\/providers\/create","name":"providers.create","action":"User\Providers\UserNotificationProviderController@create"},{"host":null,"methods":["POST"],"uri":"api\/my\/notification\/providers","name":"providers.store","action":"User\Providers\UserNotificationProviderController@store"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/notification\/providers\/{provider}","name":"providers.show","action":"User\Providers\UserNotificationProviderController@show"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/notification\/providers\/{provider}\/edit","name":"providers.edit","action":"User\Providers\UserNotificationProviderController@edit"},{"host":null,"methods":["PUT","PATCH"],"uri":"api\/my\/notification\/providers\/{provider}","name":"providers.update","action":"User\Providers\UserNotificationProviderController@update"},{"host":null,"methods":["DELETE"],"uri":"api\/my\/notification\/providers\/{provider}","name":"providers.destroy","action":"User\Providers\UserNotificationProviderController@destroy"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/ssh-keys","name":"ssh-keys.index","action":"User\Features\UserSshKeyController@index"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/ssh-keys\/create","name":"ssh-keys.create","action":"User\Features\UserSshKeyController@create"},{"host":null,"methods":["POST"],"uri":"api\/my\/ssh-keys","name":"ssh-keys.store","action":"User\Features\UserSshKeyController@store"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/ssh-keys\/{ssh_key}","name":"ssh-keys.show","action":"User\Features\UserSshKeyController@show"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/ssh-keys\/{ssh_key}\/edit","name":"ssh-keys.edit","action":"User\Features\UserSshKeyController@edit"},{"host":null,"methods":["PUT","PATCH"],"uri":"api\/my\/ssh-keys\/{ssh_key}","name":"ssh-keys.update","action":"User\Features\UserSshKeyController@update"},{"host":null,"methods":["DELETE"],"uri":"api\/my\/ssh-keys\/{ssh_key}","name":"ssh-keys.destroy","action":"User\Features\UserSshKeyController@destroy"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/teams","name":"index","action":"User\Team\UserTeamController@index"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/teams\/create","name":"create","action":"User\Team\UserTeamController@create"},{"host":null,"methods":["POST"],"uri":"api\/my\/teams","name":"store","action":"User\Team\UserTeamController@store"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/teams\/{}","name":"show","action":"User\Team\UserTeamController@show"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/teams\/{}\/edit","name":"edit","action":"User\Team\UserTeamController@edit"},{"host":null,"methods":["PUT","PATCH"],"uri":"api\/my\/teams\/{}","name":"update","action":"User\Team\UserTeamController@update"},{"host":null,"methods":["DELETE"],"uri":"api\/my\/teams\/{}","name":"destroy","action":"User\Team\UserTeamController@destroy"},{"host":null,"methods":["POST"],"uri":"api\/my\/teams\/switch\/{id?}","name":"teams.switch","action":"User\Team\UserTeamController@switchTeam"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/team\/members","name":"members.index","action":"User\Team\UserTeamMemberController@index"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/team\/members\/create","name":"members.create","action":"User\Team\UserTeamMemberController@create"},{"host":null,"methods":["POST"],"uri":"api\/my\/team\/members","name":"teams.members.invite","action":"User\Team\UserTeamMemberController@invite"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/team\/members\/{member}","name":"members.show","action":"User\Team\UserTeamMemberController@show"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/team\/members\/{member}\/edit","name":"members.edit","action":"User\Team\UserTeamMemberController@edit"},{"host":null,"methods":["PUT","PATCH"],"uri":"api\/my\/team\/members\/{member}","name":"members.update","action":"User\Team\UserTeamMemberController@update"},{"host":null,"methods":["DELETE"],"uri":"api\/my\/team\/members\/{member}","name":"members.destroy","action":"User\Team\UserTeamMemberController@destroy"},{"host":null,"methods":["POST"],"uri":"api\/my\/team\/members\/resend\/{invite_id}","name":"teams.members.resend_invite","action":"User\Team\UserTeamMemberController@resendInvite"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/piles","name":"index","action":"Pile\PileController@index"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/piles\/create","name":"create","action":"Pile\PileController@create"},{"host":null,"methods":["POST"],"uri":"api\/my\/piles","name":"store","action":"Pile\PileController@store"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/piles\/{}","name":"show","action":"Pile\PileController@show"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/piles\/{}\/edit","name":"edit","action":"Pile\PileController@edit"},{"host":null,"methods":["PUT","PATCH"],"uri":"api\/my\/piles\/{}","name":"update","action":"Pile\PileController@update"},{"host":null,"methods":["DELETE"],"uri":"api\/my\/piles\/{}","name":"destroy","action":"Pile\PileController@destroy"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/servers","name":"index","action":"Server\ServerController@index"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/servers\/create","name":"create","action":"Server\ServerController@create"},{"host":null,"methods":["POST"],"uri":"api\/my\/servers","name":"store","action":"Server\ServerController@store"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/servers\/{}","name":"show","action":"Server\ServerController@show"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/servers\/{}\/edit","name":"edit","action":"Server\ServerController@edit"},{"host":null,"methods":["PUT","PATCH"],"uri":"api\/my\/servers\/{}","name":"update","action":"Server\ServerController@update"},{"host":null,"methods":["DELETE"],"uri":"api\/my\/servers\/{}","name":"destroy","action":"Server\ServerController@destroy"},{"host":null,"methods":["POST"],"uri":"api\/my\/servers\/restore\/{server_id}","name":null,"action":"Server\ServerController@restore"},{"host":null,"methods":["POST"],"uri":"api\/my\/servers\/restart-database\/{server_id}","name":null,"action":"Server\ServerController@restartDatabases"},{"host":null,"methods":["POST"],"uri":"api\/my\/servers\/disk-space\/{server_id}","name":null,"action":"Server\ServerController@getDiskSpace"},{"host":null,"methods":["POST"],"uri":"api\/my\/servers\/save\/file\/{server_id}","name":null,"action":"Server\ServerController@saveFile"},{"host":null,"methods":["POST"],"uri":"api\/my\/servers\/provider\/{server_provider_id}\/option-regions","name":null,"action":"Server\ServerController@getServerOptionsAndRegions"},{"host":null,"methods":["POST"],"uri":"api\/my\/servers\/restart-server\/{server_id}","name":null,"action":"Server\ServerController@restartServer"},{"host":null,"methods":["POST"],"uri":"api\/my\/servers\/restart-web-services\/{server_id}","name":null,"action":"Server\ServerController@restartWebServices"},{"host":null,"methods":["POST"],"uri":"api\/my\/servers\/restart-workers\/{server_id}","name":null,"action":"Server\ServerController@restartWorkerServices"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/servers\/cron-jobs","name":"cron-jobs.index","action":"Server\Features\ServerCronJobController@index"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/servers\/cron-jobs\/create","name":"cron-jobs.create","action":"Server\Features\ServerCronJobController@create"},{"host":null,"methods":["POST"],"uri":"api\/my\/servers\/cron-jobs","name":"cron-jobs.store","action":"Server\Features\ServerCronJobController@store"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/servers\/cron-jobs\/{cron_job}","name":"cron-jobs.show","action":"Server\Features\ServerCronJobController@show"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/servers\/cron-jobs\/{cron_job}\/edit","name":"cron-jobs.edit","action":"Server\Features\ServerCronJobController@edit"},{"host":null,"methods":["PUT","PATCH"],"uri":"api\/my\/servers\/cron-jobs\/{cron_job}","name":"cron-jobs.update","action":"Server\Features\ServerCronJobController@update"},{"host":null,"methods":["DELETE"],"uri":"api\/my\/servers\/cron-jobs\/{cron_job}","name":"cron-jobs.destroy","action":"Server\Features\ServerCronJobController@destroy"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/servers\/daemons","name":"daemons.index","action":"Server\Features\ServerDaemonController@index"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/servers\/daemons\/create","name":"daemons.create","action":"Server\Features\ServerDaemonController@create"},{"host":null,"methods":["POST"],"uri":"api\/my\/servers\/daemons","name":"daemons.store","action":"Server\Features\ServerDaemonController@store"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/servers\/daemons\/{daemon}","name":"daemons.show","action":"Server\Features\ServerDaemonController@show"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/servers\/daemons\/{daemon}\/edit","name":"daemons.edit","action":"Server\Features\ServerDaemonController@edit"},{"host":null,"methods":["PUT","PATCH"],"uri":"api\/my\/servers\/daemons\/{daemon}","name":"daemons.update","action":"Server\Features\ServerDaemonController@update"},{"host":null,"methods":["DELETE"],"uri":"api\/my\/servers\/daemons\/{daemon}","name":"daemons.destroy","action":"Server\Features\ServerDaemonController@destroy"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/servers\/firewall","name":"firewall.index","action":"Server\Features\ServerFirewallController@index"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/servers\/firewall\/create","name":"firewall.create","action":"Server\Features\ServerFirewallController@create"},{"host":null,"methods":["POST"],"uri":"api\/my\/servers\/firewall","name":"firewall.store","action":"Server\Features\ServerFirewallController@store"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/servers\/firewall\/{firewall}","name":"firewall.show","action":"Server\Features\ServerFirewallController@show"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/servers\/firewall\/{firewall}\/edit","name":"firewall.edit","action":"Server\Features\ServerFirewallController@edit"},{"host":null,"methods":["PUT","PATCH"],"uri":"api\/my\/servers\/firewall\/{firewall}","name":"firewall.update","action":"Server\Features\ServerFirewallController@update"},{"host":null,"methods":["DELETE"],"uri":"api\/my\/servers\/firewall\/{firewall}","name":"firewall.destroy","action":"Server\Features\ServerFirewallController@destroy"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/servers\/network","name":"network.index","action":"Server\Features\ServerNetworkController@index"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/servers\/network\/create","name":"network.create","action":"Server\Features\ServerNetworkController@create"},{"host":null,"methods":["POST"],"uri":"api\/my\/servers\/network","name":"network.store","action":"Server\Features\ServerNetworkController@store"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/servers\/network\/{network}","name":"network.show","action":"Server\Features\ServerNetworkController@show"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/servers\/network\/{network}\/edit","name":"network.edit","action":"Server\Features\ServerNetworkController@edit"},{"host":null,"methods":["PUT","PATCH"],"uri":"api\/my\/servers\/network\/{network}","name":"network.update","action":"Server\Features\ServerNetworkController@update"},{"host":null,"methods":["DELETE"],"uri":"api\/my\/servers\/network\/{network}","name":"network.destroy","action":"Server\Features\ServerNetworkController@destroy"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/servers\/ssh-keys","name":"ssh-keys.index","action":"Server\Features\ServerSshKeyController@index"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/servers\/ssh-keys\/create","name":"ssh-keys.create","action":"Server\Features\ServerSshKeyController@create"},{"host":null,"methods":["POST"],"uri":"api\/my\/servers\/ssh-keys","name":"ssh-keys.store","action":"Server\Features\ServerSshKeyController@store"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/servers\/ssh-keys\/{ssh_key}","name":"ssh-keys.show","action":"Server\Features\ServerSshKeyController@show"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/servers\/ssh-keys\/{ssh_key}\/edit","name":"ssh-keys.edit","action":"Server\Features\ServerSshKeyController@edit"},{"host":null,"methods":["PUT","PATCH"],"uri":"api\/my\/servers\/ssh-keys\/{ssh_key}","name":"ssh-keys.update","action":"Server\Features\ServerSshKeyController@update"},{"host":null,"methods":["DELETE"],"uri":"api\/my\/servers\/ssh-keys\/{ssh_key}","name":"ssh-keys.destroy","action":"Server\Features\ServerSshKeyController@destroy"},{"host":null,"methods":["POST"],"uri":"api\/my\/servers\/ssh-connection","name":null,"action":"Server\Features\ServerSshKeyController@testSSHConnection"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/sites","name":"index","action":"Site\SiteController@index"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/sites\/create","name":"create","action":"Site\SiteController@create"},{"host":null,"methods":["POST"],"uri":"api\/my\/sites","name":"store","action":"Site\SiteController@store"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/sites\/{}","name":"show","action":"Site\SiteController@show"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/sites\/{}\/edit","name":"edit","action":"Site\SiteController@edit"},{"host":null,"methods":["PUT","PATCH"],"uri":"api\/my\/sites\/{}","name":"update","action":"Site\SiteController@update"},{"host":null,"methods":["DELETE"],"uri":"api\/my\/sites\/{}","name":"destroy","action":"Site\SiteController@destroy"},{"host":null,"methods":["POST"],"uri":"api\/my\/sites\/deploy","name":null,"action":"Site\SiteController@deploy"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/sites\/ssl","name":"ssl.index","action":"Site\Features\SSL\SiteSSLController@index"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/sites\/ssl\/create","name":"ssl.create","action":"Site\Features\SSL\SiteSSLController@create"},{"host":null,"methods":["POST"],"uri":"api\/my\/sites\/ssl","name":"ssl.store","action":"Site\Features\SSL\SiteSSLController@store"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/sites\/ssl\/{ssl}","name":"ssl.show","action":"Site\Features\SSL\SiteSSLController@show"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/sites\/ssl\/{ssl}\/edit","name":"ssl.edit","action":"Site\Features\SSL\SiteSSLController@edit"},{"host":null,"methods":["PUT","PATCH"],"uri":"api\/my\/sites\/ssl\/{ssl}","name":"ssl.update","action":"Site\Features\SSL\SiteSSLController@update"},{"host":null,"methods":["DELETE"],"uri":"api\/my\/sites\/ssl\/{ssl}","name":"ssl.destroy","action":"Site\Features\SSL\SiteSSLController@destroy"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/sites\/ssl-existing","name":"ssl-existing.index","action":"Site\Features\SSL\SiteSSLExistingController@index"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/sites\/ssl-existing\/create","name":"ssl-existing.create","action":"Site\Features\SSL\SiteSSLExistingController@create"},{"host":null,"methods":["POST"],"uri":"api\/my\/sites\/ssl-existing","name":"ssl-existing.store","action":"Site\Features\SSL\SiteSSLExistingController@store"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/sites\/ssl-existing\/{ssl_existing}","name":"ssl-existing.show","action":"Site\Features\SSL\SiteSSLExistingController@show"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/sites\/ssl-existing\/{ssl_existing}\/edit","name":"ssl-existing.edit","action":"Site\Features\SSL\SiteSSLExistingController@edit"},{"host":null,"methods":["PUT","PATCH"],"uri":"api\/my\/sites\/ssl-existing\/{ssl_existing}","name":"ssl-existing.update","action":"Site\Features\SSL\SiteSSLExistingController@update"},{"host":null,"methods":["DELETE"],"uri":"api\/my\/sites\/ssl-existing\/{ssl_existing}","name":"ssl-existing.destroy","action":"Site\Features\SSL\SiteSSLExistingController@destroy"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/sites\/ssl-lets-encrypt","name":"ssl-lets-encrypt.index","action":"Site\Features\SSL\SiteSSLLetsEncryptController@index"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/sites\/ssl-lets-encrypt\/create","name":"ssl-lets-encrypt.create","action":"Site\Features\SSL\SiteSSLLetsEncryptController@create"},{"host":null,"methods":["POST"],"uri":"api\/my\/sites\/ssl-lets-encrypt","name":"ssl-lets-encrypt.store","action":"Site\Features\SSL\SiteSSLLetsEncryptController@store"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/sites\/ssl-lets-encrypt\/{ssl_lets_encrypt}","name":"ssl-lets-encrypt.show","action":"Site\Features\SSL\SiteSSLLetsEncryptController@show"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/sites\/ssl-lets-encrypt\/{ssl_lets_encrypt}\/edit","name":"ssl-lets-encrypt.edit","action":"Site\Features\SSL\SiteSSLLetsEncryptController@edit"},{"host":null,"methods":["PUT","PATCH"],"uri":"api\/my\/sites\/ssl-lets-encrypt\/{ssl_lets_encrypt}","name":"ssl-lets-encrypt.update","action":"Site\Features\SSL\SiteSSLLetsEncryptController@update"},{"host":null,"methods":["DELETE"],"uri":"api\/my\/sites\/ssl-lets-encrypt\/{ssl_lets_encrypt}","name":"ssl-lets-encrypt.destroy","action":"Site\Features\SSL\SiteSSLLetsEncryptController@destroy"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/sites\/workers","name":"workers.index","action":"Site\Features\SiteWorkerController@index"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/sites\/workers\/create","name":"workers.create","action":"Site\Features\SiteWorkerController@create"},{"host":null,"methods":["POST"],"uri":"api\/my\/sites\/workers","name":"workers.store","action":"Site\Features\SiteWorkerController@store"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/sites\/workers\/{worker}","name":"workers.show","action":"Site\Features\SiteWorkerController@show"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/sites\/workers\/{worker}\/edit","name":"workers.edit","action":"Site\Features\SiteWorkerController@edit"},{"host":null,"methods":["PUT","PATCH"],"uri":"api\/my\/sites\/workers\/{worker}","name":"workers.update","action":"Site\Features\SiteWorkerController@update"},{"host":null,"methods":["DELETE"],"uri":"api\/my\/sites\/workers\/{worker}","name":"workers.destroy","action":"Site\Features\SiteWorkerController@destroy"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/sites\/hooks","name":"hooks.index","action":"Site\Repository\Features\RepositoryHookController@index"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/sites\/hooks\/create","name":"hooks.create","action":"Site\Repository\Features\RepositoryHookController@create"},{"host":null,"methods":["POST"],"uri":"api\/my\/sites\/hooks","name":"hooks.store","action":"Site\Repository\Features\RepositoryHookController@store"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/sites\/hooks\/{hook}","name":"hooks.show","action":"Site\Repository\Features\RepositoryHookController@show"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/sites\/hooks\/{hook}\/edit","name":"hooks.edit","action":"Site\Repository\Features\RepositoryHookController@edit"},{"host":null,"methods":["PUT","PATCH"],"uri":"api\/my\/sites\/hooks\/{hook}","name":"hooks.update","action":"Site\Repository\Features\RepositoryHookController@update"},{"host":null,"methods":["DELETE"],"uri":"api\/my\/sites\/hooks\/{hook}","name":"hooks.destroy","action":"Site\Repository\Features\RepositoryHookController@destroy"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/sites\/repository","name":"repository.index","action":"Site\Repository\SiteRepositoryController@index"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/sites\/repository\/create","name":"repository.create","action":"Site\Repository\SiteRepositoryController@create"},{"host":null,"methods":["POST"],"uri":"api\/my\/sites\/repository","name":"repository.store","action":"Site\Repository\SiteRepositoryController@store"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/sites\/repository\/{repository}","name":"repository.show","action":"Site\Repository\SiteRepositoryController@show"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/my\/sites\/repository\/{repository}\/edit","name":"repository.edit","action":"Site\Repository\SiteRepositoryController@edit"},{"host":null,"methods":["PUT","PATCH"],"uri":"api\/my\/sites\/repository\/{repository}","name":"repository.update","action":"Site\Repository\SiteRepositoryController@update"},{"host":null,"methods":["DELETE"],"uri":"api\/my\/sites\/repository\/{repository}","name":"repository.destroy","action":"Site\Repository\SiteRepositoryController@destroy"},{"host":null,"methods":["GET","HEAD"],"uri":"{any}","name":null,"action":"Closure"},{"host":null,"methods":["POST"],"uri":"stripe\/webhook","name":null,"action":"\Laravel\Cashier\Http\Controllers\WebhookController@handleWebhook"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/user","name":null,"action":"Closure"}],
            prefix: '',

            route : function (name, parameters, route) {
                route = route || this.getByName(name);

                if ( ! route ) {
                    return undefined;
                }

                return this.toRoute(route, parameters);
            },

            url: function (url, parameters) {
                parameters = parameters || [];

                var uri = url + '/' + parameters.join('/');

                return this.getCorrectUrl(uri);
            },

            toRoute : function (route, parameters) {
                var uri = this.replaceNamedParameters(route.uri, parameters);
                var qs  = this.getRouteQueryString(parameters);

                return this.getCorrectUrl(uri + qs);
            },

            replaceNamedParameters : function (uri, parameters) {
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

            getRouteQueryString : function (parameters) {
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

            getByName : function (name) {
                for (var key in this.routes) {
                    if (this.routes.hasOwnProperty(key) && this.routes[key].name === name) {
                        return this.routes[key];
                    }
                }
            },

            getByAction : function(action) {
                for (var key in this.routes) {
                    if (this.routes.hasOwnProperty(key) && this.routes[key].action === action) {
                        return this.routes[key];
                    }
                }
            },

            getCorrectUrl: function (uri) {
                var url = this.prefix + '/' + uri.replace(/^\/?/, '');

                if(!this.absolute)
                    return url;

                return this.rootUrl.replace('/\/?$/', '') + url;
            }
        };

        var getLinkAttributes = function(attributes) {
            if ( ! attributes) {
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
            title      = title || url;
            attributes = getLinkAttributes(attributes);

            return '<a href="' + url + '" ' + attributes + '>' + title + '</a>';
        };

        return {
            // Generate a url for a given controller action.
            // laroute.action('HomeController@getIndex', [params = {}])
            action : function (name, parameters) {
                parameters = parameters || {};

                return routes.route(name, parameters, routes.getByAction(name));
            },

            // Generate a url for a given named route.
            // laroute.route('routeName', [params = {}])
            route : function (route, parameters) {
                parameters = parameters || {};

                return routes.route(route, parameters);
            },

            // Generate a fully qualified URL to the given path.
            // laroute.route('url', [params = {}])
            url : function (route, parameters) {
                parameters = parameters || {};

                return routes.url(route, parameters);
            },

            // Generate a html link to the given url.
            // laroute.link_to('foo/bar', [title = url], [attributes = {}])
            link_to : function (url, title, attributes) {
                url = this.url(url);

                return getHtmlLink(url, title, attributes);
            },

            // Generate a html link to the given route.
            // laroute.link_to_route('route.name', [title=url], [parameters = {}], [attributes = {}])
            link_to_route : function (route, title, parameters, attributes) {
                var url = this.route(route, parameters);

                return getHtmlLink(url, title, attributes);
            },

            // Generate a html link to the given controller action.
            // laroute.link_to_action('HomeController@getIndex', [title=url], [parameters = {}], [attributes = {}])
            link_to_action : function(action, title, parameters, attributes) {
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
    else if (typeof module === 'object' && module.exports){
        module.exports = laroute;
    }
    else {
        window.laroute = laroute;
    }

}).call(this);

