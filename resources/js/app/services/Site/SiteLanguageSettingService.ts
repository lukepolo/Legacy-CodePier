import { injectable, inject } from "inversify";
import RestServiceClass from "@app/services/RestServiceClass";

@injectable()
export default class SiteLanguageSettingService extends RestServiceClass {
  constructor(
    @inject("HttpService") httpService,
    @inject("ApiRouteService") apiRouteService,
  ) {
    super(httpService, apiRouteService, "SiteSiteLanguageSettingsController");
  }

  public getAvailable(parameters) {
    return this.httpService.get(this.getUrl("getLanguageSettings", parameters));
  }
}
