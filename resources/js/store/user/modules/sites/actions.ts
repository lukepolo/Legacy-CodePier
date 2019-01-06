import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { SitesState } from "./stateInterface";
import UserSiteService from "@app/services/User/UserSiteService";

export default function(userSiteService: UserSiteService) {
  return {
    changePile: (context: ActionContext<SitesState, RootState>, data) => {},

    getDns: (context: ActionContext<SitesState, RootState>, site) => {
      return userSiteService.getDns(site).then(({ data }) => {
        return data;
      });
    },

    rename: (
      context: ActionContext<SitesState, RootState>,
      { site, domain, wildcardDomain },
    ) => {
      return userSiteService
        .rename(site, domain, wildcardDomain)
        .then(({ data }) => {
          context.commit("user/sites/UPDATED_SITE", data, { root: true });
          return data;
        });
    },

    updateWorkflow: (
      context: ActionContext<SitesState, RootState>,
      { site, workflow },
    ) => {
      return userSiteService.updateWorkflow(site, workflow).then(({ data }) => {
        context.commit("user/sites/UPDATED_SITE", data, { root: true });
        return data;
      });
    },

    createDeployHook: async (
      context: ActionContext<SitesState, RootState>,
      site,
    ) => {
      return userSiteService.createDeployHook(site).then(({ data }) => {
        context.commit("user/sites/UPDATED_SITE", data, { root: true });
        return data;
      });
    },

    removeDeployHook: async (
      context: ActionContext<SitesState, RootState>,
      { site, hook },
    ) => {
      return userSiteService.removeDeployHook(site, hook).then(({ data }) => {
        context.commit("user/sites/UPDATED_SITE", data, { root: true });
        return data;
      });
    },
  };
}
