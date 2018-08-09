import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { OauthState } from "./stateInterface";
import OauthService from "@app/services/OauthService";
import getDecorators from "inversify-inject-decorators";

const { lazyInject } = getDecorators($app.$container);

export default class Actions {
  @lazyInject("OauthService") private $oauthService: OauthService;

  redirectToProvider = (
    context: ActionContext<OauthState, RootState>,
    provider,
  ) => {
    window.location = this.$oauthService.getRedirectUrlForProvider(provider);
  };
}
