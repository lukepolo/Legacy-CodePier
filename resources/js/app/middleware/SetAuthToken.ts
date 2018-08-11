import CookieStorage from "@app/services/CookieStorage";

export default class SetAuthToken {
  public request(config) {
    let $cookieStorage = $app.make<CookieStorage>("CookieStorage");
    let token = $cookieStorage.get("token");
    if (token) {
      config.headers.common.Authorization = `Bearer ${token}`;
    }
    return config;
  }

  public response(response) {
    return response;
  }

  public responseError(error) {
    return Promise.reject(error);
  }
}
