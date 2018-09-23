import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { UserState } from "./stateInterface";
import UserService from "@app/services/UserService";

export default function(userService: UserService) {
  return {
    update: (context: ActionContext<UserState, RootState>, form) => {
      return userService.update(form).then(({ data }) => {
        context.commit("auth/SET_USER", data, { root: true });
        return data;
      });
    },
    markAnnouncementRead: (context: ActionContext<UserState, RootState>) => {
      return userService.markAnnouncementRead().then(({ data }) => {
        context.commit("auth/SET_USER", data, { root: true });
        return data;
      });
    },
    requestData: (context: ActionContext<UserState, RootState>) => {
      return userService.requestData();
    },
  };
}
