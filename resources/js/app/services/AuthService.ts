import { injectable, inject } from "inversify";
import VarieAuthService from "varie-auth-plugin/lib/AuthService";

@injectable()
export default class AuthService extends VarieAuthService {
  protected apiRouteService;

  constructor(
    @inject("app") app,
    @inject("HttpService") httpService,
    @inject("ConfigService") configService,
    @inject("ApiRouteService") apiRouteService,
  ) {
    super(app, configService, httpService);
    this.apiRouteService = apiRouteService;
  }

  public oAuth(provider, code, state, guard = "user") {
    return this.httpService
      .get(
        this.apiRouteService.action(
          "AuthOauthController@getHandleProviderCallback",
          {
            state,
            code,
            provider,
          },
        ),
      )
      .then((response) => {
        return this.getDriver(guard).loginResponse(response);
      });
  }
}
