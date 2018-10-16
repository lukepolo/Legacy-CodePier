import { injectable, inject } from "inversify";
import CookieInterface from "varie/lib/cookies/CookieInterface";
import StateServiceInterface from "varie/lib/state/StateServiceInterface";
import RouteMiddlewareInterface from "varie/lib/routing/RouteMiddlewareInterface";

@injectable()
export default class Auth implements RouteMiddlewareInterface {
  private next;
  private storeService;
  private cookieService;

  constructor(
    @inject("StoreService") storeService: StateServiceInterface,
    @inject("CookieService") cookieService: CookieInterface,
  ) {
    this.storeService = storeService.getStore();
    this.cookieService = cookieService;
  }

  handler(to, from, next) {
    this.next = next;

    if (!this.cookieService.get("token")) {
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
