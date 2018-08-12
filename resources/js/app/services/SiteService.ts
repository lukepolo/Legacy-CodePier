import { injectable, inject } from "inversify";

@injectable()
export default class SiteService {
  private $http;
  private $apiRouteService;

  constructor(
    @inject("$http") $http,
    @inject("ApiRouteService") apiRouteService,
  ) {
    this.$http = $http;
    this.$apiRouteService = apiRouteService;
  }

  get() {
    return this.$http.get(
      this.$apiRouteService.action("SiteSiteController@index"),
    );
  }
}