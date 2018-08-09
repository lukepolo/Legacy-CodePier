import AuthService from "@app/services/AuthService";
import OauthService from "@app/services/OauthService";
import LocalStorage from "@app/services/LocalStorage";
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
    this.app.bind("AuthService", AuthService);
    this.app.bind("OauthService", OauthService);
    this.app.bind("LocalStorage", LocalStorage);
    this.app.$container
      .bind("ApiRouteService")
      .toConstantValue(require("@app/../vendor/laroute"));
  }
}
