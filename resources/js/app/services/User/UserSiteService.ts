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

  public updateWorkflow(site, workflow) {
    return this.httpService.post(
      this.$apiRouteService.action("SiteSiteWorkflowController@store", {
        site,
      }),
      {
        workflow,
      },
    );
  }

  public getDns(site) {
    return this.httpService.get(
      this.$apiRouteService.action("SiteSiteDnsController@index", { site }),
    );
  }

  public rename(site, domain, wildcardDomain) {
    return this.httpService.post(
      this.$apiRouteService.action("SiteSiteController@rename", { site }),
      {
        domain,
        wildcardDomain,
      },
    );
  }

  public createDeployHook(site) {
    return this.httpService.post(
      this.$apiRouteService.action(
        "SiteRepositoryRepositoryHookController@store",
        {
          site,
        },
      ),
    );
  }

  public removeDeployHook(site, hook) {
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
