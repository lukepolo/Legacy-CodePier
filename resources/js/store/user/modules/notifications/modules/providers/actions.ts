import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { ProvidersState } from "./stateInterface";
import OauthService from "@app/services/OauthService";
import UserNotificationProviderService from "@app/services/User/UserNotificationProviderService";

export default function(
  userNotificationProviderService: UserNotificationProviderService,
  oauthService: OauthService,
) {
  return {
    redirectToProvider: (
      context: ActionContext<ProvidersState, RootState>,
      provider,
    ) => {
      return oauthService.redirectToProvider(provider);
    },
  };
}
