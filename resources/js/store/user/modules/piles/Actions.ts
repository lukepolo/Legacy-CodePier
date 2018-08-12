import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { PilesState } from "./stateInterface";
import PileService from "@app/services/PileService";
import getDecorators from "inversify-inject-decorators";

const { lazyInject } = getDecorators($app.$container);

export default class Actions {
  @lazyInject("PileService") private $pileService: PileService;

  get = (context: ActionContext<PilesState, RootState>, data) => {
    return this.$pileService.get().then((response) => {
      context.commit("SET_PILES", response.data);
      return response.data;
    });
  };

  changePile = (context: ActionContext<PilesState, RootState>, pile) => {
    return this.$pileService.changePile(pile).then((response) => {
      context.commit("auth/SET_USER", response.data, { root: true });
      return response.data;
    });
  };
}
