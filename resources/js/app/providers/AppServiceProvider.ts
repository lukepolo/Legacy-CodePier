import Vue from "vue";
import PortalVue from "portal-vue";
import UserService from "@app/services/UserService";
import PileService from "@app/services/PileService";
import AuthService from "@app/services/AuthService";
import OauthService from "@app/services/OauthService";
import CookieStorage from "@app/services/CookieStorage";
import ServiceProvider from "varie/lib/support/ServiceProvider";
import SiteService from "@app/services/SiteService";

/*
|--------------------------------------------------------------------------
| App Service Provider
|--------------------------------------------------------------------------
| You can bind various items to the app here, or can create other
| custom providers that bind the container
|
*/
export default class AppProviderServiceProvider extends ServiceProvider {
  public boot() {
    Vue.use(PortalVue);
  }

  public register() {
    // SYSTEM
    this.app.singleton("CookieStorage", CookieStorage);

    this.app.bind("AuthService", AuthService);
    this.app.bind("OauthService", OauthService);

    this.app.bind("PileService", PileService);
    this.app.bind("SiteService", SiteService);
    this.app.bind("UserService", UserService);

    // ROUTING
    this.app.constant("ApiRouteService", require("@app/../vendor/laroute"));
  }
}
