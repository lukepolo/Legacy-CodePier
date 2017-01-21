import Vue from 'vue'
import Vuex from 'vuex'
import { action } from './helpers'
import * as UserStore from './modules/user'
import * as SiteStore from './modules/site'
import * as ServerStore from './modules/server'
import PileStore from './modules/PileStore'
import EventsStore from './modules/EventsStore'
import BuoyAppsStore from './modules/BuoyAppsStore'
import CategoriesStore from './modules/CategoriesStore'
import NotificationStore from './modules/NotificationStore'

Vue.action = action

Vue.use(Vuex)

export default new Vuex.Store({
    modules: {
        notificationsStore: NotificationStore,

        pilesStore: PileStore,
        eventsStore: EventsStore,
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
        siteSshKeysStore: SiteStore.siteSshKeys,
        siteCronJobsStore: SiteStore.siteCronJobs,
        siteFirewallRulesStore: SiteStore.siteFirewallRules,
        siteServersFeaturesStore: SiteStore.siteServersFeatures,
        siteSslCertificatesStore: SiteStore.siteSslCertificates,

        serversStore: ServerStore.server,
        serverFilesStore: ServerStore.files,
        serverWorkersStore: ServerStore.workers,
        serverSshKeysStore: ServerStore.sshKeys,
        serverCronJobsStore: ServerStore.cronJobs,
        serverFirewallStore: ServerStore.firewall,
        serverServicesStore: ServerStore.services,
        serverProvidersStore: ServerStore.providers
    }
})
