import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { UserState } from "./stateInterface";
import getDecorators from "inversify-inject-decorators";
import HttpServiceInterface from "varie/lib/http/HttpServiceInterface";

const { lazyInject } = getDecorators($app.$container);

export default class Actions {
  @lazyInject("$http") private $http: HttpServiceInterface;

  sampleTest = (context: ActionContext<UserState, RootState>, data) => {};
}
