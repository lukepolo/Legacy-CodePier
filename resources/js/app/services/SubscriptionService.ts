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

  subscribe(form) {
    return this.$http.post(
      this.apiRouteService.action(
        "UserSubscriptionUserSubscriptionController@store",
      ),
      form,
    );
  }

  downloadInvoice(invoice) {
    return this.$http
      .get(
        this.apiRouteService.action(
          "UserSubscriptionUserSubscriptionInvoiceController@show",
          { invoice },
        ),
        {
          headers: {
            accept: "application/pdf",
          },
          responseType: "blob",
        },
      )
      .then((response) => {
        const blob = new Blob([response.data]);
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement("a");
        link.href = url;
        let disposition = response.headers["content-disposition"];
        link.setAttribute(
          "download",
          decodeURI(disposition.match(/filename="(.*)"/)[1]),
        );
        link.click();
        window.URL.revokeObjectURL(url);
      });
  }
}
