import { injectable, inject } from "inversify";
import RestServiceClass from "../RestServiceClass";

@injectable()
export default class SiteDeploymentStepService extends RestServiceClass {
  constructor(
    @inject("HttpService") httpService,
    @inject("ApiRouteService") apiRouteService,
  ) {
    super(httpService, apiRouteService, "SiteSiteDeploymentStepsController");
  }

  public getAvailableDeploymentSteps(parameters = null) {
    return this.httpService.get(this.getUrl("getDeploymentSteps", parameters));
  }
}
