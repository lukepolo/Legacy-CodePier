import AuthService from "@app/services/AuthService";
import OauthService from "@app/services/OauthService";
import LocalStorage from "@app/services/LocalStorage";
import PileService from "@app/services/PileService";
import ServiceProvider from "varie/lib/support/ServiceProvider";

/*
|--------------------------------------------------------------------------
| App Service Provider
|--------------------------------------------------------------------------
| You can bind various items to the app here, or can create other
| custom providers that bind the container
|
*/
export default class AppProviderServiceProvider extends ServiceProvider {
  public boot() {}

  public register() {
    // AUTH
    this.app.bind("AuthService", AuthService);
    this.app.bind("OauthService", OauthService);

    // SYSTEM
    this.app.bind("LocalStorage", LocalStorage);

    // PILES
    this.app.bind("PileService", PileService);

    // ROUTING
    this.app.$container
      .bind("ApiRouteService")
      .toConstantValue(require("@app/../vendor/laroute"));
  }
}
