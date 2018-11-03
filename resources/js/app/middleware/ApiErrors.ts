import { injectable, inject } from "inversify";

import AlertServiceInterface from "varie/lib/plugins/alerts/AlertServiceInterface";
import AxiosHttpMiddlewareInterface from "varie/lib/http/AxiosHttpMiddlewareInterface";

@injectable()
export default class ApiErrors implements AxiosHttpMiddlewareInterface {
  private alertService: AlertServiceInterface;

  constructor(@inject("AlertService") alertService: AlertServiceInterface) {
    this.alertService = alertService;
  }

  public request(config) {
    return config;
  }

  public response(response) {
    return response;
  }

  public responseError(error) {
    if (error.response && error.response.data) {
      this.alertService.showError(error.response.data);
    }
    return error;
  }
}
