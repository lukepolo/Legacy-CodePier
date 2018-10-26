import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { AuthState } from "./stateInterface";
import AuthService from "@app/services/AuthService";

export default function(authService: AuthService) {
  return {
    login: (context: ActionContext<AuthState, RootState>, data) => {
      return authService.login(data.email, data.password).then((response) => {
        return response;
      });
    },
    register: (context: ActionContext<AuthState, RootState>, form) => {
      return authService
        .register(form.name, form.email, form.password, form.passwordConfirmed)
        .then((response) => {
          return response;
        });
    },
    forgotPasswordRequest: (
      context: ActionContext<AuthState, RootState>,
      form,
    ) => {
      return authService.forgotPasswordRequest(form.email);
    },
    resetPassword: (
      context: ActionContext<AuthState, RootState>,
      { token, form },
    ) => {
      return authService
        .resetPassword(token, form.email, form.password, form.passwordConfirmed)
        .then((response) => {
          return response;
        });
    },
    getUser: (context: ActionContext<AuthState, RootState>) => {
      return authService.getUser().then((response) => {
        context.commit("SET_AUTH_USER", response.data);
        return response.data;
      });
    },
    logout: (context: ActionContext<AuthState, RootState>) => {
      return new Promise((resolve) => {
        context.commit("REMOVE_AUTH");
        resolve();
      });
    },
  };
}
