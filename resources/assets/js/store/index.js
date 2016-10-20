import Vue from 'vue'
import Vuex from 'vuex'

import {action} from "./helpers";

Vue.action = action;

import * as UserStore from './modules/user';
import * as SiteStore from './modules/site';
import * as ServerStore from './modules/server';

import PileStore from './modules/PileStore';
import EventsStore from './modules/EventsStore';

Vue.use(Vuex);

export default new Vuex.Store({
    modules: {
        userStore : UserStore.user,
        teamsStore : UserStore.teams,
        userSshKeysStore : UserStore.sshKeys,
        userSubscriptionsStore : UserStore.subscriptions,

        sitesStore : SiteStore.site,

        pilesStore : PileStore,
        eventsStore : EventsStore,

        serverStore : ServerStore.server,
        serverWorkersStore : ServerStore.workers,
        serverSshKeysStore : ServerStore.sshKeys,
        serverCronJobsStore : ServerStore.cronJobs,
        serverFirewallStore : ServerStore.firewall,
        serverServicesStore : ServerStore.services,
        serverProvidersStore : ServerStore.providers,
    }
})