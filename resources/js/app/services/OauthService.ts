import { injectable, inject } from "inversify";

@injectable()
export default class OauthService {
  private apiRouteService;

  constructor(@inject("ApiRouteService") ApiRouteService) {
    this.apiRouteService = ApiRouteService;
  }

  getRedirectUrlForProvider(provider) {
    return this.apiRouteService.action("AuthOauthController@newProvider", {
      provider,
    });
  }

  redirectToProvider(provider) {
    window.location.replace(
      this.apiRouteService.action("AuthOauthController@newProvider", {
        provider: provider,
      }),
    );
  }
}
