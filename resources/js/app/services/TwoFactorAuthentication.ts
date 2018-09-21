import { injectable, inject } from "inversify";
import HttpServiceInterface from "varie/lib/http/HttpServiceInterface";

@injectable()
export default class TwoFactorAuthentication {
  private apiRouteService;
  private httpService: HttpServiceInterface;

  constructor(
    @inject("HttpService") httpService,
    @inject("ApiRouteService") ApiRouteService,
  ) {
    this.httpService = httpService;
    this.apiRouteService = ApiRouteService;
  }

  validate(token) {
    return this.httpService.post(
      this.apiRouteService.action("AuthSecondAuthController@store", {
        token,
      }),
    );
  }

  generateQr() {
    return this.httpService.get(
      this.apiRouteService.action("AuthSecondAuthController@index"),
    );
  }
}
