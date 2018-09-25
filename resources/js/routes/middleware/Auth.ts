import { injectable, inject } from "inversify";
import CookieStorage from "@app/services/CookieStorage";
import StateServiceInterface from "varie/lib/state/StateServiceInterface";
import RouteMiddlewareInterface from "varie/lib/routing/RouteMiddlewareInterface";

@injectable()
export default class Auth implements RouteMiddlewareInterface {
  private next;
  private storeService;
  private $cookieStorage;

  constructor(
    @inject("StoreService") storeService: StateServiceInterface,
    @inject("CookieStorage") cookieStorage: CookieStorage,
  ) {
    this.storeService = storeService.getStore();
    this.$cookieStorage = cookieStorage;
  }

  handler(to, from, next) {
    this.next = next;

    if (!this.$cookieStorage.get("token")) {
      return this.redirectToLogin();
    }

    if (this.storeService.state.auth.user) {
      return next();
    }

    return this.storeService.dispatch("auth/me").then(
      () => {
        return next();
      },
      () => {
        return this.redirectToLogin();
      },
    );
  }

  redirectToLogin() {
    return this.next({
      name: "login",
    });
  }
}
