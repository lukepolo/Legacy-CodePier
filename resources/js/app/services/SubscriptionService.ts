import { injectable, inject } from "inversify";
import HttpServiceInterface from "varie/lib/http/HttpServiceInterface";

@injectable()
export default class SubscriptionService {
  private apiRouteService;
  private $http: HttpServiceInterface;

  constructor(
    @inject("$http") $http,
    @inject("ApiRouteService") ApiRouteService,
  ) {
    this.$http = $http;
    this.apiRouteService = ApiRouteService;
  }

  getCurrentSubscription() {
    return this.$http.get(
      this.apiRouteService.action(
        "UserSubscriptionUserSubscriptionController@index",
      ),
    );
  }

  plans() {
    return this.$http.get(
      this.apiRouteService.action("SubscriptionController@index"),
    );
  }

  invoices() {
    return this.$http.get(
      this.apiRouteService.action(
        "UserSubscriptionUserSubscriptionInvoiceController@index",
      ),
    );
  }
}
