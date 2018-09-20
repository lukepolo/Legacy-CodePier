import { injectable, inject } from "inversify";
import HttpServiceInterface from "varie/lib/http/HttpServiceInterface";

@injectable()
export default class AuthService {
  private apiRouteService;
  private $http: HttpServiceInterface;

  constructor(
    @inject("$http") $http,
    @inject("ApiRouteService") ApiRouteService,
  ) {
    this.$http = $http;
    this.apiRouteService = ApiRouteService;
  }

  login(email, password) {
    return this.$http.post(
      this.apiRouteService.action("AuthLoginController@login"),
      {
        email,
        password,
      },
    );
  }

  oAuthLogin(provider, code, state) {
    return this.$http.get(
      this.apiRouteService.action(
        "AuthOauthController@getHandleProviderCallback",
        {
          state,
          code,
          provider,
        },
      ),
    );
  }

  oAuthConnect(provider, code, state) {
    return this.$http.get(
      this.apiRouteService.action(
        "AuthOauthController@getHandleProviderCallback",
        {
          state,
          code,
          provider,
        },
      ),
    );
  }

  createAccount(name, email, password, confirmPassword) {
    return this.$http.post(
      this.apiRouteService.action("AuthRegisterController@register"),
      {
        name,
        email,
        password,
        password_confirmation: confirmPassword,
      },
    );
  }

  forgotPasswordRequest(email) {
    return this.$http.post(
      this.apiRouteService.action(
        "AuthForgotPasswordController@sendResetLinkEmail",
      ),
      {
        email,
      },
    );
  }

  resetPassword(token, email, password, confirmPassword) {
    return this.$http.post(
      this.apiRouteService.action("AuthResetPasswordController@reset"),
      {
        email,
        token,
        password,
        password_confirmation: confirmPassword,
      },
    );
  }
}
