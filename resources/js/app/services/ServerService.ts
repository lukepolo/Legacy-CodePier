import { injectable, inject } from "inversify";

@injectable()
export default class ServerService {
  private httpService;
  private apiRouteService;

  constructor(
    @inject("HttpService") httpService,
    @inject("ApiRouteService") apiRouteService,
  ) {
    this.httpService = httpService;
    this.apiRouteService = apiRouteService;
  }

  getFeatures() {
    return this.httpService.get(
      this.apiRouteService.action("ServerServerFeatureController@getFeatures"),
    );
  }

  getFrameworks() {
    return this.httpService.get(
      this.apiRouteService.action(
        "ServerServerFeatureController@getFrameworks",
      ),
    );
  }

  getLanguages() {
    return this.httpService.get(
      this.apiRouteService.action("ServerServerFeatureController@getLanguages"),
    );
  }

  getSystems() {
    return this.httpService.get(
      this.apiRouteService.action("SystemsController@index"),
    );
  }
}
