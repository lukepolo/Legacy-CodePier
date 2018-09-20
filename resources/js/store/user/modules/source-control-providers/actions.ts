import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { SourceControlProvidersState } from "./stateInterface";
import UserSourceControlProviderService from "@app/services/User/UserSourceControlProviderService";

export default function(
  userSourceControlProviderService: UserSourceControlProviderService,
) {
  return {
    redirectToProvider: (
      context: ActionContext<SourceControlProvidersState, RootState>,
      provider,
    ) => {
      userSourceControlProviderService.redirectToProvider(provider);
    },
  };
}
