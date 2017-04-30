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
        siteFilesStore: SiteStore.files,
        siteWorkersStore: SiteStore.workers,
        siteSchemasStore: SiteStore.schemas,
        siteSshKeysStore: SiteStore.sshKeys,
        siteCronJobsStore: SiteStore.cronJobs,
        siteFirewallRulesStore: SiteStore.firewallRules,
        siteServersFeaturesStore: SiteStore.serversFeatures,
        siteSslCertificatesStore: SiteStore.sslCertificates,
        siteEnvironmentVariablesStore : SiteStore.environmentVariables,

        serversStore: ServerStore.server,
        serverFilesStore: ServerStore.files,
        serverBuoysStore: ServerStore.buoys,
        serverWorkersStore: ServerStore.workers,
        serverSshKeysStore: ServerStore.sshKeys,
        serverSchemasStore :ServerStore.schemas,
        serverCronJobsStore: ServerStore.cronJobs,
        serverFirewallStore: ServerStore.firewall,
        serverServicesStore: ServerStore.services,
        serverProvidersStore: ServerStore.providers,
        serverSslCertificatesStore: ServerStore.sslCertificates,
        serverEnvironmentVariablesStore : ServerStore.environmentVariables,

    }
})
