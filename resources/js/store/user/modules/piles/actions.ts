import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { PilesState } from "@store/user/modules/piles/stateInterface";
import PileService from "@app/services/PileService";

export default function($pileService: PileService) {
  return {
    changePile(context: ActionContext<PilesState, RootState>, pile) {
      return $pileService.changePile(pile).then((response) => {
        context.commit("auth/SET_USER", response.data, { root: true });
        return response.data;
      });
    },
  };
}
