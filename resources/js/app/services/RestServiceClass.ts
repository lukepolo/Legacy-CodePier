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

  public get(parameters = null) {
    return this.httpService.get(this.getUrl("index", parameters));
  }

  public create(parameters = null, data) {
    return this.httpService.post(this.getUrl("store", parameters), data);
  }

  public show(parameters) {
    return this.httpService.get(this.getUrl("show", parameters));
  }

  public update(parameters, data) {
    return this.httpService.patch(this.getUrl("update", parameters), data);
  }

  public destroy(parameters) {
    return this.httpService.delete(this.getUrl("destroy", parameters));
  }

  protected getUrl(action, parameters) {
    // laroute deletes params when replacing......
    if (parameters) {
      parameters = JSON.parse(JSON.stringify(parameters));
    }

    let url = this.$apiRouteService.action(
      `${this.$controllerClass}@${action}`,
      parameters,
    );

    if (!url) {
      throw `Invalid Controller/Action : ${this.$controllerClass}@${action}`;
    }

    return url;
  }
}
