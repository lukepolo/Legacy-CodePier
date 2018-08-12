import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { AuthState } from "./stateInterface";
import AuthService from "@app/services/AuthService";
import getDecorators from "inversify-inject-decorators";
import CookieStorage from "@app/services/CookieStorage";
import RouterInterface from "varie/lib/routing/RouterInterface";
import HttpServiceInterface from "varie/lib/http/HttpServiceInterface";

const { lazyInject } = getDecorators($app.$container);

export default class Actions {
  @lazyInject("$http") private $http: HttpServiceInterface;
  @lazyInject("$router") private $router: RouterInterface;
  @lazyInject("AuthService") private $authService: AuthService;
  @lazyInject("CookieStorage") private $cookieStorage: CookieStorage;

  login = (context: ActionContext<AuthState, RootState>, data) => {
    return this.$authService
      .login(data.email, data.password)
      .then((response) => {
        this.$cookieStorage.set("token", response.data);
        context.dispatch("me");
        context.commit("UPDATE_AUTH_AREA_DATA", {
          name: null,
          email: null,
        });
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
        context.dispatch("me");
        context.commit("UPDATE_AUTH_AREA_DATA", {
          name: null,
          email: null,
        });
        return response.data;
      });
  };

  createAccount = (context: ActionContext<AuthState, RootState>, form) => {
    return this.$authService
      .createAccount(
        form.name,
        form.email,
        form.password,
        form.passwordConfirmed,
      )
      .then((response) => {
        this.$cookieStorage.set("token", response.data);
        context.dispatch("me");
        context.commit("UPDATE_AUTH_AREA_DATA", {
          name: null,
          email: null,
        });
        return response.data;
      });
  };

  forgotPasswordRequest = (
    context: ActionContext<AuthState, RootState>,
    form,
  ) => {
    return this.$authService.forgotPasswordRequest(form.email);
  };

  resetPassword = (
    context: ActionContext<AuthState, RootState>,
    { form, token },
  ) => {
    console.info(token);
    return this.$authService
      .resetPassword(token, form.email, form.password, form.passwordConfirmed)
      .then((response) => {
        this.$cookieStorage.set("token", response.data);
        context.dispatch("me");
        context.commit("UPDATE_AUTH_AREA_DATA", {
          name: null,
          email: null,
        });
      });
  };

  me = (context: ActionContext<AuthState, RootState>) => {
    return this.$http.get("/api/me").then((response) => {
      context.commit("SET_USER", response.data);
      return response.data;
    });
  };

  logout = () => {
    return new Promise((resolve) => {
      resolve(this.$cookieStorage.remove("token"));
    });
  };
}
