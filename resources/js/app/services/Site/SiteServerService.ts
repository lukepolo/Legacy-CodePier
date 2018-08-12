import { injectable, inject } from "inversify";
import RestServiceClass from "@app/services/RestServiceClass";

@injectable()
export default class SiteServerService extends RestServiceClass {
  constructor(
    @inject("$http") $http,
    @inject("ApiRouteService") apiRouteService,
  ) {
    super($http, apiRouteService, "SiteSiteServerController");
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
