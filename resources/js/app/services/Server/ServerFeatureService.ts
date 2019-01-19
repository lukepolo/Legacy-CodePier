import { injectable, inject } from "inversify";
import RestServiceClass from "@app/services/RestServiceClass";

@injectable()
export default class ServerFeatureService extends RestServiceClass {
  constructor(
    @inject("HttpService") httpService,
    @inject("ApiRouteService") apiRouteService,
  ) {
    super(httpService, apiRouteService, "ServerServerFeatureController");
  }

  public getFeatures(parameters: { site: number }) {
    return this.httpService.get(this.getUrl("getFeatures", parameters));
  }
}
