import { injectable, inject } from "inversify";
import RestServiceClass from "@app/services/RestServiceClass";

@injectable()
export default class UserSshKeyService extends RestServiceClass {
  constructor(
    @inject("$http") $http,
    @inject("ApiRouteService") apiRouteService,
  ) {
    super($http, apiRouteService, "UserUserSshKeyController");
  }
}
