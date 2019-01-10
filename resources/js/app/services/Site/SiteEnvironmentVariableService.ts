import { injectable, inject } from "inversify";
import RestServiceClass from "@app/services/RestServiceClass";

@injectable()
export default class SiteEnvironmentVariableService extends RestServiceClass {
  constructor(
    @inject("HttpService") httpService,
    @inject("ApiRouteService") apiRouteService,
  ) {
    super(
      httpService,
      apiRouteService,
      "SiteSiteEnvironmentVariablesController",
    );
  }
}
