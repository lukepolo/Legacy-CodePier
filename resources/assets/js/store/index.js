import Vue from 'vue/dist/vue'
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
        user : UserStore.user,
        userTeams : UserStore.teams,
        userSshKeys : UserStore.sshKeys,
        userSubscriptions : UserStore.subscriptions,

        site : SiteStore.site,

        pile : PileStore,
        events : EventsStore,

        server : ServerStore.server,
        serverWorkers : ServerStore.workers,
        serverSshKeys : ServerStore.sshKeys,
        serverCronJobs : ServerStore.cronJobs,
        serverFirewall : ServerStore.firewall,
        serverServices : ServerStore.services,
        serverProviders : ServerStore.providers,
    }
})