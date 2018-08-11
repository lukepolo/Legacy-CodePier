import LocalStorage from "@app/services/LocalStorage";
import ConfigInterface from "varie/lib/config/ConfigInterface";

// TODO - Need to figure out how to protect this token from users in the browser
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
