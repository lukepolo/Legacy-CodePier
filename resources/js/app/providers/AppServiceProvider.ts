import Vue from "vue";
import PortalVue from "portal-vue";
import ServiceProvider from "varie/lib/support/ServiceProvider";
import ConfigInterface from "varie/lib/config/ConfigInterface";

/*
|--------------------------------------------------------------------------
| App Service Provider
|--------------------------------------------------------------------------
| You can bind various items to the app here, or can create other
| custom providers that bind the container
|
*/
export default class AppProviderServiceProvider extends ServiceProvider {
  private apiRouteService;

  public boot() {
    Vue.use(PortalVue);

    this.apiRouteService = this.app.make("ApiRouteService");

    this.app
      .make<ConfigInterface>("ConfigService")
      .set("auth.guards.user.endpoints", {
        user: this.apiRouteService.action("UserUserController@index"),
        login: this.apiRouteService.action("AuthLoginController@login"),
        logout: this.apiRouteService.action("AuthLoginController@logout"),
        register: this.apiRouteService.action(
          "AuthRegisterController@register",
        ),
        resetPassword: this.apiRouteService.action(
          "AuthResetPasswordController@reset",
        ),
        forgotPassword: this.apiRouteService.action(
          "AuthForgotPasswordController@sendResetLinkEmail",
        ),
      });
  }

  public register() {
    this.app.constant("ApiRouteService", require("@app/helpers/routes"));
  }
}
