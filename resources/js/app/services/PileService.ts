import { injectable, inject } from "inversify";
import RestServiceClass from "@app/services/RestServiceClass";

@injectable()
export default class PileService extends RestServiceClass {
  constructor(
    @inject("HttpService") httpService,
    @inject("ApiRouteService") apiRouteService,
  ) {
    super(httpService, apiRouteService, "PilePileController");
  }

  changePile(pile) {
    return this.httpService.post(
      this.$apiRouteService.action("PilePileController@changePile"),
      {
        pile,
      },
    );
  }
}
