import Vue from 'vue/dist/vue'
import Vuex from 'vuex'

import * as Getters from './getters'

import * as UserStore from './modules/user';
import * as SiteStore from './modules/site';
import * as ServerStore from './modules/server';

Vue.use(Vuex);

export default new Vuex.Store({
    Getters,
    modules: {
        user : UserStore.user,
        userTeams : UserStore.teams,
        userSshKeys : UserStore.sshKeys,
        userSubscriptions : UserStore.subscriptions,

        site : SiteStore.site,

        server : ServerStore.server,
        serverWorkers : ServerStore.workers,
        serverSshKeys : ServerStore.sshKeys,
        serverCronJobs : ServerStore.cronJobs,
        serverFirewall : ServerStore.firewall,
        serverServices : ServerStore.services,
        serverProviders : ServerStore.providers,
    }
})