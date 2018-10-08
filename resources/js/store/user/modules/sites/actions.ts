import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { SitesState } from "./stateInterface";
import UserSiteService from "@app/services/User/UserSiteService";

export default function(userSiteService: UserSiteService) {
  return {
    changePile: (context: ActionContext<SitesState, RootState>, data) => {},
  };
}
