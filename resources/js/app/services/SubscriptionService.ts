import { injectable, inject } from "inversify";
import HttpServiceInterface from "varie/lib/http/HttpServiceInterface";

@injectable()
export default class SubscriptionService {
  private apiRouteService;
  private httpService: HttpServiceInterface;

  constructor(
    @inject("HttpService") httpService,
    @inject("ApiRouteService") ApiRouteService,
  ) {
    this.httpService = httpService;
    this.apiRouteService = ApiRouteService;
  }

  getCurrentSubscription() {
    return this.httpService.get(
      this.apiRouteService.action(
        "UserSubscriptionUserSubscriptionController@index",
      ),
    );
  }

  plans() {
    return this.httpService.get(
      this.apiRouteService.action("SubscriptionController@index"),
    );
  }

  invoices() {
    return this.httpService.get(
      this.apiRouteService.action(
        "UserSubscriptionUserSubscriptionInvoiceController@index",
      ),
    );
  }

  subscribe(form) {
    return this.httpService.post(
      this.apiRouteService.action(
        "UserSubscriptionUserSubscriptionController@store",
      ),
      form,
    );
  }

  updateSubscription(form) {
    return this.httpService.put(
      this.apiRouteService.action(
        "UserSubscriptionUserSubscriptionController@update",
      ),
      form,
    );
  }

  cancelSubscription() {
    return this.httpService.delete(
      this.apiRouteService.action(
        "UserSubscriptionUserSubscriptionController@destroy",
      ),
    );
  }

  downloadInvoice(invoice) {
    return this.httpService
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
