import { injectable, inject } from "inversify";

import CookieInterface from "varie/lib/cookies/CookieInterface";
import HttpMiddlewareInterface from "varie/lib/http/HttpMiddlewareInterface";

@injectable()
export default class SetAuthToken implements HttpMiddlewareInterface {
  private cookieService;

  constructor(@inject("CookieService") cookieService: CookieInterface) {
    this.cookieService = cookieService;
  }

  public request(config) {
    let token = this.cookieService.get("token");
    if (token) {
      config.headers.common.Authorization = `Bearer ${token}`;
    }
    return config;
  }

  public response(response) {
    return response;
  }

  public responseError(error) {
    return error;
  }
}
