import Echo from "laravel-echo";
import PileService from "@app/services/PileService";
import AuthService from "@app/services/AuthService";
import OauthService from "@app/services/OauthService";
import CookieStorage from "@app/services/CookieStorage";
import ServiceProvider from "varie/lib/support/ServiceProvider";
import ConfigInterface from "../../../../node_modules/varie/lib/config/ConfigInterface";

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
    // SYSTEM
    this.app.singleton("CookieStorage", CookieStorage);

    // AUTH
    this.app.bind("AuthService", AuthService);
    this.app.bind("OauthService", OauthService);

    // PILES
    this.app.bind("PileService", PileService);

    // ROUTING
    this.app.constant("ApiRouteService", require("@app/../vendor/laroute"));
  }
}
