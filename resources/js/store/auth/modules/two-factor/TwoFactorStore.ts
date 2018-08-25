import state from "./state";
import actions from "./actions";
import getters from "./getters";
import mutations from "./mutations";
import StoreModule from "varie/lib/state/StoreModule";
import { injectable, inject, unmanaged } from "inversify";
import TwoFactorAuthentication from "@app/services/TwoFactorAuthentication";

@injectable()
export default class TwoFactorStore extends StoreModule {
  constructor(
    @inject("TwoFactorAuthentication")
    twoFactorAuthentication: TwoFactorAuthentication,
  ) {
    super();
    this.setName("twoFactor")
      .addState(state)
      .addActions(actions(twoFactorAuthentication))
      .addMutations(mutations)
      .addGetters(getters);
  }
}
