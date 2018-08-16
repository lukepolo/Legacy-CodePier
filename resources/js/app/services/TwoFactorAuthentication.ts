import { injectable, inject } from "inversify";
import HttpServiceInterface from "varie/lib/http/HttpServiceInterface";

@injectable()
export default class TwoFactorAuthentication {
  private apiRouteService;
  private $http: HttpServiceInterface;

  constructor(
    @inject("$http") $http,
    @inject("ApiRouteService") ApiRouteService,
  ) {
    this.$http = $http;
    this.apiRouteService = ApiRouteService;
  }

  validate(token) {
    return this.$http.post(
      this.apiRouteService.action("AuthSecondAuthController@store", {
        token,
      }),
    );
  }

  generateQr() {
    return this.$http.get(
      this.apiRouteService.action("AuthSecondAuthController@index"),
    );
  }
}
