import { injectable, inject } from "inversify";

import HttpMiddlewareInterface from "varie/lib/http/HttpMiddlewareInterface";
import CookieStorage from "@app/services/CookieStorage";

@injectable()
export default class SetAuthToken implements HttpMiddlewareInterface {
  private $cookieStorage;

  constructor(@inject("CookieStorage") cookieStorage: CookieStorage) {
    this.$cookieStorage = cookieStorage;
  }

  public request(config) {
    let token = this.$cookieStorage.get("token");
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
