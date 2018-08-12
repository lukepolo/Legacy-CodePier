import state from "./state";
import Actions from "./actions";
import Getters from "./getters";
import Mutations from "./mutations";
import { injectable, inject } from "inversify";
import BroadcastService from "@app/services/BroadcastService";
import StateServiceInterface from "../../../../node_modules/varie/lib/state/StateServiceInterface";

@injectable()
export default class System {
  public name;
  public state;
  public actions;
  public getters;
  public mutations;
  public namespaced;
  private $broadcastService: BroadcastService;

  constructor(@inject("BroadcastService") $broadcastService) {
    this.name = "System";
    this.state = state;
    this.namespaced = true;
    this.actions = new Actions();
    this.getters = new Getters();
    this.mutations = new Mutations();
    this.$broadcastService = $broadcastService;
    this.listenForVersionChanges();
  }

  listenForVersionChanges() {
    this.$broadcastService.listen("channel", "ReleasedNewVersion", (data) => {
      this.mutations.SET_VERSION(this.state, data.version);
    });
  }
}
