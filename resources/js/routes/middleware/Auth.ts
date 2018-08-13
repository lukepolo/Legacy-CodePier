import { injectable, inject } from "inversify";
import CookieStorage from "@app/services/CookieStorage";
import StateServiceInterface from "varie/lib/state/StateServiceInterface";
import RouteMiddlewareInterface from "varie/lib/routing/RouteMiddlewareInterface";

@injectable()
export default class Auth implements RouteMiddlewareInterface {
  private $store;
  private $cookieStorage;

  constructor(
    @inject("$store") $store: StateServiceInterface,
    @inject("CookieStorage") cookieStorage: CookieStorage,
  ) {
    this.$store = $store.getStore();
    this.$cookieStorage = cookieStorage;
  }
  passes(to, from, next) {
    if (!this.$cookieStorage.get("token")) {
      next({
        name: "login",
      });
      return false;
    }

    if (!this.$store.state.auth.user) {
      this.$store.dispatch("auth/me").then(() => {
        return true;
      });
    }
    return true;
  }
}
