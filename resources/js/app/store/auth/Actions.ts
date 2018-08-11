import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { AuthState } from "./stateInterface";
import AuthService from "@app/services/AuthService";
import getDecorators from "inversify-inject-decorators";
import HttpServiceInterface from "varie/lib/http/HttpServiceInterface";
import CookieStorage from "@app/services/CookieStorage";

const { lazyInject } = getDecorators($app.$container);

export default class Actions {
  @lazyInject("$http") private $http: HttpServiceInterface;
  @lazyInject("AuthService") private $authService: AuthService;
  @lazyInject("CookieStorage") private $cookieStorage: CookieStorage;

  login = (context: ActionContext<AuthState, RootState>, data) => {
    return this.$authService
      .login(data.email, data.password)
      .then((response) => {
        return response.data;
      });
  };

  oAuthLogin = (
    context: ActionContext<AuthState, RootState>,
    { provider, code, state },
  ) => {
    return this.$authService
      .oAuthLogin(provider, code, state)
      .then((response) => {
        this.$cookieStorage.set("token", response.data);
        return response.data;
      });
  };

  me = (context: ActionContext<AuthState, RootState>) => {
    return this.$http.get("/api/me").then((response) => {
      context.commit("SET_USER", response.data);
      return response.data;
    });
  };
}
