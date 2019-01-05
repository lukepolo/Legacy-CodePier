import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { AuthState } from "./stateInterface";
import AuthService from "@app/services/AuthService";
import OauthService from "@app/services/OauthService";

export default function(authService: AuthService, oauthService: OauthService) {
  return {
    oAuth: (
      context: ActionContext<AuthState, RootState>,
      { code, state, provider },
    ) => {
      return authService.isLoggedIn().then(() => {
        return authService.oAuth(provider, code, state).then((response) => {
          return response;
        });
      });
    },
    redirectToProvider(context: ActionContext<AuthState, RootState>, provider) {
      oauthService.redirectToProvider(provider);
    },
    login: (context: ActionContext<AuthState, RootState>, data) => {
      return authService.login(data).then((response) => {
        context.commit("RESET_AUTH_AREA_DATA");
        return response;
      });
    },
    register: (context: ActionContext<AuthState, RootState>, data) => {
      return authService.register(data).then((response) => {
        context.commit("RESET_AUTH_AREA_DATA");
        return response;
      });
    },
    forgotPasswordRequest: (
      context: ActionContext<AuthState, RootState>,
      data,
    ) => {
      return authService.forgotPasswordRequest(data);
    },
    resetPassword: (context: ActionContext<AuthState, RootState>, data) => {
      return authService.resetPassword(data).then((response) => {
        context.commit("RESET_AUTH_AREA_DATA");
        return response;
      });
    },
    getUser: (context: ActionContext<AuthState, RootState>) => {
      return authService.getUser().then((response) => {
        context.commit("SET_AUTH_USER", {
          user: response.data,
          guard: "user",
        });
        return response.data;
      });
    },
    logout: (context: ActionContext<AuthState, RootState>) => {
      return authService.logout().then((response) => {
        context.commit("REMOVE_AUTH");
        context.commit("RESET_AUTH_AREA_DATA");
        return response;
      });
    },
  };
}
