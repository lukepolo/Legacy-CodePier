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

  get() {
    return this.$http.get(
      this.$apiRouteService.action(`${this.$controllerClass}@index`),
    );
  }

  show(resourceIds) {
    // TODO - we need to write something that can derive it
    console.info(this.$apiRouteService.action(`${this.$controllerClass}@show`));
    return this.$http.get(
      this.$apiRouteService.action(`${this.$controllerClass}@show`),
    );
  }

  update(resourceIds, data) {
    // TODO - we need to write something that can derive it
    console.info(
      this.$apiRouteService.action(`${this.$controllerClass}@update`),
    );
    return this.$http.put(
      this.$apiRouteService.action(`${this.$controllerClass}@update`),
      {
        data,
      },
    );
  }

  destroy() {
    return this.$http.delete(
      this.$apiRouteService.action(`${this.$controllerClass}@destroy`),
    );
  }
}
