import { injectable, inject } from "inversify";

@injectable()
export default class NotificationSettingService {
  private httpService;
  private apiRouteService;

  constructor(
    @inject("HttpService") httpService,
    @inject("ApiRouteService") apiRouteService,
  ) {
    this.httpService = httpService;
    this.apiRouteService = apiRouteService;
  }

  getSettings() {
    return this.httpService.get(
      this.apiRouteService.action("NotificationSettingsController@index"),
    );
  }
}
