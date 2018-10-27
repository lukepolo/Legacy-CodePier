import AuthService from "@app/services/AuthService";
import OauthService from "@app/services/OauthService";
import ConfigInterface from "varie/lib/config/ConfigInterface";
import ServiceProvider from "varie/lib/support/ServiceProvider";
import TwoFactorAuthentication from "@app/services/TwoFactorAuthentication";
import JwtDriver from "varie-authentication-plugin/lib/drivers/jwt/JwtDriver";

export default class AuthenticationServiceProvider extends ServiceProvider {
  private apiRouteService;

  public register() {
    this.apiRouteService = this.app.make("ApiRouteService");
    this.app
      .make<ConfigInterface>("ConfigService")
      .set("auth.guards.user.endpoints", {
        user: this.apiRouteService.action("UserUserController@index"),
        login: this.apiRouteService.action("AuthAuthController@login"),
        logout: this.apiRouteService.action("AuthAuthController@logout"),
        refresh: this.apiRouteService.action("AuthAuthController@refresh"),
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

    this.app.singleton("JwtDriver", JwtDriver);
    this.app.bind("AuthService", AuthService);
    this.app.bind("OauthService", OauthService);
    this.app.bind("TwoFactorAuthentication", TwoFactorAuthentication);
  }
}
