import { injectable, inject } from "inversify";
import RestServiceClass from "@app/services/RestServiceClass";

@injectable()
export default class UserServerProviderService extends RestServiceClass {
  constructor(
    @inject("HttpService") httpService,
    @inject("ApiRouteService") apiRouteService,
  ) {
    super(
      httpService,
      apiRouteService,
      "UserProvidersUserServerProviderController",
    );
  }

  connectProvider(provider, data) {
    return this.httpService.post(
      "/api/server/providers/" + provider.provider_name + "/provider",
      data,
    );
  }
}
