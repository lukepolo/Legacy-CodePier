import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { DeploymentsState } from "./stateInterface";
import SiteDeploymentService from "@app/services/Site/SiteDeploymentService";
import SiteDeploymentStepService from "@app/services/Site/SiteDeploymentStepService";

export default function(
  siteDeploymentService: SiteDeploymentService,
  siteDeploymentStepService: SiteDeploymentStepService,
) {
  return {
    getDeploymentSteps(
      context: ActionContext<DeploymentsState, RootState>,
      data,
    ) {
      return siteDeploymentStepService.get(data).then(({ data }) => {
        context.commit("SET_DEPLOYMENT_STEPS", data);
        return data;
      });
    },
    getAvailableDeploymentSteps(
      context: ActionContext<DeploymentsState, RootState>,
      data,
    ) {
      return siteDeploymentStepService
        .getAvailableDeploymentSteps(data)
        .then(({ data }) => {
          context.commit("SET_AVAILABLE_DEPLOYMENT_STEPS", data);
          return data;
        });
    },
  };
}
