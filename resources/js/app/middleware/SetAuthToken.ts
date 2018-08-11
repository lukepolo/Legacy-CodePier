import LocalStorage from "@app/services/LocalStorage";

export default class SetAuthToken {
  public request(config) {
    let $localStorage = $app.make<LocalStorage>("LocalStorage");
    let token = $localStorage.get("token");
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
