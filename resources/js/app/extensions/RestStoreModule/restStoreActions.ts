import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { PilesState } from "@store/user/modules/piles/stateInterface";

export default function($service, stateName) {
  return {
    get(context: ActionContext<PilesState, RootState>) {
      return $service.get().then((response) => {
        context.commit(`SET_${stateName.toUpperCase()}`, response.data);
        return response.data;
      });
    },
  };
}
