import { injectable, inject } from "inversify";
import RestServiceClass from "@app/services/RestServiceClass";

@injectable()
export default class UserServerProviderService extends RestServiceClass {
  constructor(
    @inject("$http") $http,
    @inject("ApiRouteService") apiRouteService,
  ) {
    super($http, apiRouteService, "UserProvidersUserServerProviderController");
  }

  connectProvider(provider, data) {
    return this.$http.post(
      "/api/server/providers/" + provider.provider_name + "/provider",
      data,
    );
  }
}
