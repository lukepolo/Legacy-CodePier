import { injectable, inject } from "inversify";

import HttpMiddlewareInterface from "varie/lib/http/HttpMiddlewareInterface";

@injectable()
export default class ValidationErrors implements HttpMiddlewareInterface {
  public request(config) {
    return config;
  }

  public response(response) {
    return response;
  }

  public responseError(error) {
    return error;
  }
}
