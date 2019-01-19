import AuthStore from "@store/auth/AuthStore";
import AuthService from "@app/services/AuthService";
import OauthService from "@app/services/OauthService";
import ConfigInterface from "varie/lib/config/ConfigInterface";
import AxiosHttpService from "varie/lib/http/AxiosHttpService";
import { ServiceProvider } from "varie";
import JwtDriver from "varie-auth-plugin/lib/drivers/JwtDriver";
import AuthMiddleware from "varie-auth-plugin/lib/AuthMiddleware";
import StateServiceInterface from "varie/lib/state/StateServiceInterface";
import TwoFactorAuthentication from "@app/services/TwoFactorAuthentication";

export default class AuthServiceProvider extends ServiceProvider {
  private apiRouteService;

  public boot() {
    let $httpService = this.app.make<AxiosHttpService>("HttpService");
    let stateService = this.app.make<StateServiceInterface>("StateService");

    $httpService.registerMiddleware(AuthMiddleware);
    stateService.registerStore(AuthStore);
  }

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
