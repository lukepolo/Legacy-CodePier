import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { AuthState } from "./stateInterface";
import getDecorators from "inversify-inject-decorators";
import HttpServiceInterface from "varie/lib/http/HttpServiceInterface";
import OauthService from "@app/services/OauthService";

const { lazyInject } = getDecorators($app.$container);

export default class Actions {
  @lazyInject("$http") private $http: HttpServiceInterface;
  @lazyInject("OauthService") private $oauthService: OauthService;

  redirectToProvider = (context: ActionContext<AuthState, RootState>, data) => {
    console.info(data);
  };
}
