import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { ServerFeatureState } from "./stateInterface";
import ServerFeatureService from "@app/services/Server/ServerFeatureService";

export default function(serverFeatureService: ServerFeatureService) {
  return {
    get: (
      context: ActionContext<ServerFeatureState, RootState>,
      data: {
        site: number;
      },
    ) => {
      return serverFeatureService.getFeatures(data).then(({ data }) => {
        context.commit("SET_FEATURES", data);
        return data;
      });
    },
  };
}
