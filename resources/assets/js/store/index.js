import Vue from 'vue'
import Vuex from 'vuex'
import { action } from './helpers'
import * as UserStore from './modules/user'
import * as SiteStore from './modules/site'
import * as ServerStore from './modules/server'

import PileStore from './modules/PileStore'
import BittsStore from './modules/BittsStore'
import EventsStore from './modules/EventsStore'
import SystemsStore from './modules/SystemsStore'
import BuoyAppsStore from './modules/BuoyAppsStore'
import CategoriesStore from './modules/CategoriesStore'
import NotificationStore from './modules/NotificationStore'

Vue.action = action

Vue.use(Vuex)

export default new Vuex.Store({
    modules: {
        notificationsStore: NotificationStore,

        pilesStore: PileStore,
        bittsStore: BittsStore,
        eventsStore: EventsStore,
        systemsStore: SystemsStore,
        buoyAppsStore: BuoyAppsStore,
        categoriesStore: CategoriesStore,

        userStore: UserStore.user,
        teamsStore: UserStore.teams,
        userSshKeysStore: UserStore.sshKeys,
        userSubscriptionsStore: UserStore.subscriptions,
        userNotificationsStore: UserStore.notifications,

        sitesStore: SiteStore.site,
        siteFilesStore: SiteStore.siteFiles,
        siteWorkersStore: SiteStore.siteWorkers,
        siteSchemasStore: SiteStore.siteSchemas,
        siteSshKeysStore: SiteStore.siteSshKeys,
        siteCronJobsStore: SiteStore.siteCronJobs,
        siteFirewallRulesStore: SiteStore.siteFirewallRules,
        siteServersFeaturesStore: SiteStore.siteServersFeatures,
        siteSslCertificatesStore: SiteStore.siteSslCertificates,

        serversStore: ServerStore.server,
        serverFilesStore: ServerStore.files,
        serverBuoysStore: ServerStore.buoys,
        serverWorkersStore: ServerStore.workers,
        serverSshKeysStore: ServerStore.sshKeys,
        serverCronJobsStore: ServerStore.cronJobs,
        serverFirewallStore: ServerStore.firewall,
        serverServicesStore: ServerStore.services,
        serverProvidersStore: ServerStore.providers
    }
})
