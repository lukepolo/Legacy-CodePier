import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { SourceControlProvidersState } from "./stateInterface";
import UserSourceControlProviderService from "@app/services/User/UserSourceControlProviderService";
import OauthService from "@app/services/OauthService";

export default function(
  userSourceControlProviderService: UserSourceControlProviderService,
  oauthService: OauthService,
) {
  return {
    redirectToProvider: (
      context: ActionContext<SourceControlProvidersState, RootState>,
      provider,
    ) => {
      oauthService.redirectToProvider(provider);
    },
  };
}
