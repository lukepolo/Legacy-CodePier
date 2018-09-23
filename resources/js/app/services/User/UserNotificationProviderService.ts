import { injectable, inject } from "inversify";
import RestServiceClass from "@app/services/RestServiceClass";

@injectable()
export default class UserNotificationProviderService extends RestServiceClass {
  constructor(
    @inject("HttpService") httpService,
    @inject("ApiRouteService") apiRouteService,
  ) {
    super(
      httpService,
      apiRouteService,
      "UserProvidersUserNotificationProviderController",
    );
  }

  connectProvider(provider, data) {
    return this.httpService.post(
      `/api/my/notification-providers/${provider}`,
      data,
    );
  }
}
