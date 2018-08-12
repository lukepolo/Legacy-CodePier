import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { UserState } from "./stateInterface";
import UserService from "@app/services/UserService";
import getDecorators from "inversify-inject-decorators";
import HttpServiceInterface from "varie/lib/http/HttpServiceInterface";

const { lazyInject } = getDecorators($app.$container);

export default class Actions {
  @lazyInject("$http") private $http: HttpServiceInterface;
  @lazyInject("UserService") private $userService: UserService;

  markAnnouncementRead = (
    context: ActionContext<UserState, RootState>,
    data,
  ) => {
    return this.$userService.markAnnouncementRead().then((response) => {
      context.commit("auth/SET_USER", response.data, { root: true });
      return response.data;
    });
  };
}
