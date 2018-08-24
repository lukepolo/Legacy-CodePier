import { injectable, inject } from "inversify";

import HttpMiddlewareInterface from "varie/lib/http/HttpMiddlewareInterface";
import NotificationServiceInterface from "varie/lib/plugins/notifications/NotificationServiceInterface";

@injectable()
export default class ApiErrors implements HttpMiddlewareInterface {
  private notificationService: NotificationServiceInterface;

  constructor(
    @inject("$notifications") notificationService: NotificationServiceInterface,
  ) {
    this.notificationService = notificationService;
  }

  public request(config) {
    return config;
  }

  public response(response) {
    return response;
  }

  public responseError(error) {
    if (error.response && error.response.data) {
      this.notificationService.showError(error.response.data);
    }
    return error;
  }
}
