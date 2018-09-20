import { injectable, inject } from "inversify";
import RestServiceClass from "@app/services/RestServiceClass";

@injectable()
export default class UserSourceControlProviderService extends RestServiceClass {
  constructor(
    @inject("HttpService") httpService,
    @inject("ApiRouteService") apiRouteService,
  ) {
    super(
      httpService,
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
