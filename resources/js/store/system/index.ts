import state from "./state";
import actions from "./actions";
import getters from "./getters";
import mutations from "./mutations";
import { injectable, inject } from "inversify";
import StoreModule from "varie/lib/state/StoreModule";

@injectable()
export default class System extends StoreModule {
  private $broadcastService;
  constructor(@inject("BroadcastService") $broadcastService) {
    super();
    this.setName("System")
      .addState(state)
      .addActions(actions)
      .addMutations(mutations)
      .addGetters(getters);
    this.$broadcastService = $broadcastService;
    this.listenForVersionChanges();
  }

  listenForVersionChanges() {
    this.$broadcastService.listen("channel", "ReleasedNewVersion", (data) => {
      this.mutations.SET_VERSION(this.state, data.version);
    });
  }
}
