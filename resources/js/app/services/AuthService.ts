import { injectable, inject } from "inversify";
import HttpServiceInterface from "varie/lib/http/HttpServiceInterface";

@injectable()
export default class AuthService {
  private apiRouteService;
  private httpService: HttpServiceInterface;

  constructor(
    @inject("HttpService") httpService,
    @inject("ApiRouteService") ApiRouteService,
  ) {
    this.httpService = httpService;
    this.apiRouteService = ApiRouteService;
  }

  login(email, password) {
    return this.httpService.post(
      this.apiRouteService.action("AuthLoginController@login"),
      {
        email,
        password,
      },
    );
  }

  oAuthLogin(provider, code, state) {
    return this.httpService.get(
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
    return this.httpService.get(
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
    return this.httpService.post(
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
    return this.httpService.post(
      this.apiRouteService.action(
        "AuthForgotPasswordController@sendResetLinkEmail",
      ),
      {
        email,
      },
    );
  }

  resetPassword(token, email, password, confirmPassword) {
    return this.httpService.post(
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
