import { injectable, unmanaged } from "inversify";

@injectable()
export default class RestServiceClass {
  protected httpService;
  protected $apiRouteService;
  protected $controllerClass;

  constructor(
    httpService,
    apiRouteService,
    @unmanaged() controllerClass: string,
  ) {
    this.httpService = httpService;
    this.$apiRouteService = apiRouteService;
    this.$controllerClass = controllerClass;
  }

  get(parameters = null) {
    return this.httpService.get(this.getUrl("index", parameters));
  }

  create(parameters = null, data) {
    return this.httpService.post(this.getUrl("store", parameters), data);
  }

  show(parameters) {
    return this.httpService.get(this.getUrl("show", parameters));
  }

  update(parameters, data) {
    return this.httpService.patch(this.getUrl("update", parameters), data);
  }

  destroy(parameters) {
    return this.httpService.delete(this.getUrl("destroy", parameters));
  }

  private getUrl(action, parameters) {
    // laroute deletes params when replacing......
    if (parameters) {
      parameters = JSON.parse(JSON.stringify(parameters));
    }
    return this.$apiRouteService.action(
      `${this.$controllerClass}@${action}`,
      parameters,
    );
  }
}
