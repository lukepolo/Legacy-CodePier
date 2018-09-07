import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { ServerProvidersState } from "./stateInterface";
import UserServerProviderService from "@app/services/User/UserServerProviderService";

export default function(userServerProviderService: UserServerProviderService) {
  return {
    connectProvider: (
      context: ActionContext<ServerProvidersState, RootState>,
      { provider, data },
    ) => {
      return userServerProviderService
        .connectProvider(provider, data)
        .then(({ data }) => {
          context.commit("CREATED_PROVIDER", data);
        });
    },
  };
}
