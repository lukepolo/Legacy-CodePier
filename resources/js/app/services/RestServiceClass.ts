import { injectable, unmanaged } from "inversify";

@injectable()
export default class RestServiceClass {
  protected $http;
  protected $apiRouteService;
  protected $controllerClass;

  constructor($http, apiRouteService, @unmanaged() controllerClass: string) {
    this.$http = $http;
    this.$apiRouteService = apiRouteService;
    this.$controllerClass = controllerClass;
  }

  get(parameters) {
    return this.$http.get(this.getUrl("index", parameters));
  }

  create(parameters, data) {
    return this.$http.post(this.getUrl("store", parameters), data);
  }

  show(parameters) {
    return this.$http.get(this.getUrl("show", parameters));
  }

  update(parameters, data) {
    return this.$http.patch(this.getUrl("update", parameters), data);
  }

  destroy(parameters) {
    return this.$http.delete(this.getUrl("destroy", parameters));
  }

  private getUrl(action, parameters) {
    return this.$apiRouteService.action(
      `${this.$controllerClass}@${action}`,
      parameters,
    );
  }
}
