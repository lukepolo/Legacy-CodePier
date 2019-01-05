import state from "./state";
import actions from "./actions";
import getters from "./getters";
import mutations from "./mutations";
import { injectable, inject } from "inversify";
import StoreModule from "varie/lib/state/StoreModule";
import AuthService from "@app/services/AuthService";
import OauthService from "@app/services/OauthService";

@injectable()
export default class AuthStore extends StoreModule {
  constructor(
    @inject("AuthService") authService: AuthService,
    @inject("OauthService") oauthService: OauthService,
  ) {
    super();
    this.setName("auth")
      .addState(state)
      .addActions(actions(authService, oauthService))
      .addMutations(mutations())
      .addGetters(getters(authService));
  }
}
