/*
 |--------------------------------------------------------------------------
 | User Stores
 |--------------------------------------------------------------------------
 |
 */
window.userStore = require( './stores/User/UserStore');
window.userTeamStore = require('./stores/User/UserTeamStore');
window.userSshKeyStore = require('./stores/User/UserSshKeyStore');
window.userSubscriptionStore = require('./stores/User/UserSubscriptionStore');

/*
 |--------------------------------------------------------------------------
 | Server Stores
 |--------------------------------------------------------------------------
 |
 */

window.serverStore = require('./stores/Server/ServerStore');
window.serverSshKeyStore = require('./stores/Server/ServerSshKeyStore');
window.serverProviderStore = require('./stores/Server/ServerProviderStore');
window.serverCronJobStore = require('./stores/Server/ServerCronJobStore');
window.serverWorkerStore = require('./stores/Server/ServerWorkerStore');
window.serverFirewallStore = require('./stores/Server/ServerFirewallStore');
window.serverServicesStore = require('./stores/Server/ServerServicesStore');

/*
 |--------------------------------------------------------------------------
 | Site Stores
 |--------------------------------------------------------------------------
 |
 */
window.siteStore = require('./stores/SiteStore');


/*
 |--------------------------------------------------------------------------
 | Pile Stores
 |--------------------------------------------------------------------------
 |
 */
window.pileStore = require('./stores/PileStore');
window.subscriptionStore = require('./stores/subscriptionStore');
window.eventStore = require('./stores/EventStore');