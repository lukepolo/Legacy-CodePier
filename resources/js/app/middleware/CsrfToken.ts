import LocalStorage from "@app/services/LocalStorage";

export default class CsrfToken {

  public request(config) {
    let $localStorage = $app.make<LocalStorage>('LocalStorage');

    let token = $localStorage.get('token');

    if(token) {
        $localStorage.remove('token');
        config.headers.common.Authorization = `Bearer ${token}`
    }

    window.onbeforeunload = () => {
        $localStorage.set('token', token)
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
