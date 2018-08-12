import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { AuthState } from "./stateInterface";
import UserService from "@app/services/UserService";
import AuthService from "@app/services/AuthService";
import OauthService from "@app/services/OauthService";
import CookieStorage from "@app/services/CookieStorage";

export default function(
  $authService: AuthService,
  $cookieStorage: CookieStorage,
  $oauthService: OauthService,
  $userService: UserService,
) {
  return {
    login: (context: ActionContext<AuthState, RootState>, data) => {
      return $authService.login(data.email, data.password).then((response) => {
        $cookieStorage.set("token", response.data);
        context.dispatch("me");
        context.commit("UPDATE_AUTH_AREA_DATA", {
          name: null,
          email: null,
        });
        return response.data;
      });
    },
    oAuthLogin: (
      context: ActionContext<AuthState, RootState>,
      { provider, code, state },
    ) => {
      return $authService.oAuthLogin(provider, code, state).then((response) => {
        $cookieStorage.set("token", response.data);
        context.dispatch("me");
        context.commit("UPDATE_AUTH_AREA_DATA", {
          name: null,
          email: null,
        });
        return response.data;
      });
    },
    createAccount: (context: ActionContext<AuthState, RootState>, form) => {
      return $authService
        .createAccount(
          form.name,
          form.email,
          form.password,
          form.passwordConfirmed,
        )
        .then((response) => {
          $cookieStorage.set("token", response.data);
          context.dispatch("me");
          context.commit("UPDATE_AUTH_AREA_DATA", {
            name: null,
            email: null,
          });
          return response.data;
        });
    },
    forgotPasswordRequest: (
      context: ActionContext<AuthState, RootState>,
      form,
    ) => {
      return $authService.forgotPasswordRequest(form.email);
    },
    resetPassword: (
      context: ActionContext<AuthState, RootState>,
      { token, form },
    ) => {
      return $authService
        .resetPassword(token, form.email, form.password, form.passwordConfirmed)
        .then((response) => {
          $cookieStorage.set("token", response.data);
          context.dispatch("me");
          context.commit("UPDATE_AUTH_AREA_DATA", {
            name: null,
            email: null,
          });
        });
    },
    me: (context: ActionContext<AuthState, RootState>, data) => {
      return $userService.me().then((response) => {
        context.commit("SET_USER", response.data);
        return response.data;
      });
    },
    logout: (context: ActionContext<AuthState, RootState>, data) => {
      return new Promise((resolve) => {
        resolve($cookieStorage.remove("token"));
      });
    },
    redirectToProvider: (
      context: ActionContext<AuthState, RootState>,
      provider,
    ) => {
      window.location = $oauthService.getRedirectUrlForProvider(provider);
    },
  };
}
