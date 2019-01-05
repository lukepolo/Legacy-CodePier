import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { LifeLinesState } from "./stateInterface";
import UserSiteLifeLineService from "@app/services/User/UserSiteLifeLineService";

export default function(userSiteLifeLineService: UserSiteLifeLineService) {
  return {};
}
