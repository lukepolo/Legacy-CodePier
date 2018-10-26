import { injectable, inject } from "inversify";

import AxiosHttpMiddlewareInterface from "varie/lib/http/AxiosHttpMiddlewareInterface";

@injectable()
export default class ValidationErrors implements AxiosHttpMiddlewareInterface {
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
