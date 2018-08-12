import { injectable, inject } from "inversify";
import HttpServiceInterface from "varie/lib/http/HttpServiceInterface";

@injectable()
export default class UserService {
  private apiRouteService;
  private $http: HttpServiceInterface;

  constructor(
    @inject("$http") $http,
    @inject("ApiRouteService") ApiRouteService,
  ) {
    this.$http = $http;
    this.apiRouteService = ApiRouteService;
  }

  me() {
    return this.$http.get("/api/me");
  }

  markAnnouncementRead() {
    return this.$http.post(
      this.apiRouteService.action("AnnouncementsController@store"),
    );
  }

  resendConfirmation() {
    return this.$http.post(
      this.apiRouteService.action("UserUserConfirmController@store"),
    );
  }
}