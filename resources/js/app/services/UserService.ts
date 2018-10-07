import { injectable, inject } from "inversify";
import HttpServiceInterface from "varie/lib/http/HttpServiceInterface";

@injectable()
export default class UserService {
  private apiRouteService;
  private httpService: HttpServiceInterface;

  constructor(
    @inject("HttpService") httpService,
    @inject("ApiRouteService") ApiRouteService,
  ) {
    this.httpService = httpService;
    this.apiRouteService = ApiRouteService;
  }

  me() {
    return this.httpService.get("/api/me");
  }

  update(data) {
    return this.httpService.put(
      this.apiRouteService.action("UserUserController@update"),
      data,
    );
  }

  markAnnouncementRead() {
    return this.httpService.post(
      this.apiRouteService.action("AnnouncementsController@store"),
    );
  }

  resendConfirmation() {
    return this.httpService.post(
      this.apiRouteService.action("UserUserConfirmController@store"),
    );
  }

  requestData() {
    return this.httpService.get(
      this.apiRouteService.action("UserUserController@requestData"),
    );
  }

  getActiveCommands() {
    return this.httpService.get(
      this.apiRouteService.action("UserUserController@getRunningCommands"),
    );
  }

  getAvailableSslCertificates() {
    // UserUserSslCertificates@index
    // TODO - im not sure if there is a better way but this works?
  }
}
