import { injectable, inject } from "inversify";

@injectable()
export default class EventService {
  private httpService;
  private apiRouteService;

  constructor(
    @inject("HttpService") httpService,
    @inject("ApiRouteService") apiRouteService,
  ) {
    this.httpService = httpService;
    this.apiRouteService = apiRouteService;
  }

  getEvents(options) {
    return this.httpService.post(
      this.apiRouteService.get("EventController@store"),
      {
        data: options,
      },
    );
  }
}
