import Vue from "vue";
import PortalVue from "portal-vue";

import { ServiceProvider } from "varie";
// TODO - make it module exports
import routes from "@app/helpers/routes";

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
    this.app.constant("ApiRouteService", routes());
  }
}
