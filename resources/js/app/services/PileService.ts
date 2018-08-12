import { injectable, inject } from "inversify";

@injectable()
export default class PileService {
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
      this.$apiRouteService.action("PilePileController@index"),
    );
  }

  changePile(pile) {
    return this.$http.post(
      this.$apiRouteService.action("PilePileController@changePile"),
      {
        pile,
      },
    );
  }
}
