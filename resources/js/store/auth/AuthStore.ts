import state from "./state";
import actions from "./actions";
import getters from "./getters";
import mutations from "./mutations";
import { injectable, inject } from "inversify";
import StoreModule from "varie/lib/state/StoreModule";
import TwoFactorStore from "@store/auth/modules/twoFactor/TwoFactorStore";

@injectable()
export default class AuthStore extends StoreModule {
  constructor(
    @inject("AuthService") $authService,
    @inject("CookieStorage") $cookieStorage,
    @inject("OauthService") $oauthService,
    @inject("UserService") $userService,
  ) {
    super();
    this.setName("auth")
      .addState(state)
      .addActions(
        actions($authService, $cookieStorage, $oauthService, $userService),
      )
      .addMutations(mutations)
      .addGetters(getters)
      .addModule(TwoFactorStore);
  }
}
