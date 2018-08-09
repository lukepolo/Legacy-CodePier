import LocalStorage from "@app/services/LocalStorage";
import ConfigInterface from "varie/lib/config/ConfigInterface";

export default class SetAuthToken {
  public request(config) {
    let $localStorage = $app.make<LocalStorage>("LocalStorage");
    let $config = $app.make<ConfigInterface>("$config");
    let token = $localStorage.get("token");
    if (token) {
      $config.set("app.hasToken", true);
      // $localStorage.remove("token");
      config.headers.common.Authorization = `Bearer ${token}`;
    }
    window.onbeforeunload = () => {
      // $localStorage.set("token", token);
    };
    return config;
  }

  public response(response) {
    return response;
  }

  public responseError(error) {
    return Promise.reject(error);
  }
}
