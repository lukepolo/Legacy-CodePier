import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { LanguagesState } from "./stateInterface";
import ServerService from "@app/services/ServerService";

export default function(serverService: ServerService) {
  return {
    get: (context: ActionContext<LanguagesState, RootState>) => {
      return serverService.getLanguages().then(({ data }) => {
        context.commit("SET_LANGUAGES", data);
        return data;
      });
    },
  };
}
