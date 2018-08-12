import state from "./state";
import actions from "./actions";
import getters from "./getters";
import mutations from "./mutations";
import StoreModule from "varie/lib/state/StoreModule";
import { injectable } from "inversify";

@injectable()
export default class SshKeys extends StoreModule {
  constructor() {
    super();
    this.setName("SshKeys")
      .addState(state)
      .addActions(actions)
      .addMutations(mutations)
      .addGetters(getters);
  }
}