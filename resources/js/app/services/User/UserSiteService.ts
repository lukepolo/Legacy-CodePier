import { injectable, inject } from "inversify";
import RestServiceClass from "@app/services/RestServiceClass";

@injectable()
export default class UserSiteService extends RestServiceClass {
  constructor(
    @inject("HttpService") httpService,
    @inject("ApiRouteService") apiRouteService,
  ) {
    super(httpService, apiRouteService, "SiteSiteController");
  }

  updateWorkflow(site, workflow) {
    return this.httpService.post(
      this.$apiRouteService.action("SiteSiteWorkflowController@store", {
        site,
      }),
      {
        workflow,
      },
    );
  }

  public async createDeployHook(site) {
    return this.httpService.post(
      this.$apiRouteService.action(
        "SiteRepositoryRepositoryHookController@store",
        {
          site,
        },
      ),
    );
  }

  public async removeDeployHook(site, hook) {
    return this.httpService.delete(
      this.$apiRouteService.action(
        "SiteRepositoryRepositoryHookController@destroy",
        {
          site,
          hook,
        },
      ),
    );
  }
}
