import state from "./state";
import actions from "./actions";
import getters from "./getters";
import mutations from "./mutations";
import { injectable, inject } from "inversify";
import StoreModule from "varie/lib/state/StoreModule";
import ServerProvidersStore from "@store/system/modules/server-providers/ServerProvidersStore";

@injectable()
export default class SystemStore extends StoreModule {
  private $broadcastService;
  constructor(
    @inject("$config") $config,
    @inject("BroadcastService") $broadcastService,
  ) {
    super();

    this.$broadcastService = $broadcastService;

    this.setName("system")
      .addState(state($config))
      .addActions(actions)
      .addMutations(mutations)
      .addGetters(getters)
      .addModule(ServerProvidersStore)
      .listenForVersionChanges();
  }

  listenForVersionChanges() {
    this.$broadcastService.listen("channel", "ReleasedNewVersion", (data) => {
      this.mutations.SET_VERSION(this.state, data.version);
    });
  }
}
