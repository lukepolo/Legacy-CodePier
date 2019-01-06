import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { DeploymentsState } from "./stateInterface";
import SiteDeploymentService from "@app/services/Site/SiteDeploymentService";

export default function(siteDeploymentService: SiteDeploymentService) {
  return {};
}
