import { injectable, inject } from "inversify";
import RestServiceClass from "@app/services/RestServiceClass";

@injectable()
export default class UserSourceControlProviderService extends RestServiceClass {
  constructor(
    @inject("$http") $http,
    @inject("ApiRouteService") apiRouteService,
  ) {
    super(
      $http,
      apiRouteService,
      "UserProvidersUserRepositoryProviderController",
    );
  }

  redirectToProvider(provider) {
    window.location.replace(
      this.$apiRouteService.action("AuthOauthController@newProvider", {
        provider: provider,
      }),
    );
  }
}
