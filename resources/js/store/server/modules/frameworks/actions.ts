import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { FrameworksState } from "./stateInterface";
import ServerService from "@app/services/ServerService";

export default function(serverService: ServerService) {
  return {
    get: (context: ActionContext<FrameworksState, RootState>) => {
      return serverService.getFrameworks().then(({ data }) => {
        context.commit("SET_FRAMEWORKS", data);
        return data;
      });
    },
  };
}
