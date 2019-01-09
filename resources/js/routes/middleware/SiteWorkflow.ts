import { injectable, inject } from "inversify";
import StateServiceInterface from "varie/lib/state/StateServiceInterface";
import RouteMiddlewareInterface from "varie/lib/routing/RouteMiddlewareInterface";

@injectable()
export default class SiteWorkflow implements RouteMiddlewareInterface {
  private to;
  private next;
  private site;
  private stateService;

  constructor(@inject("StateService") stateService: StateServiceInterface) {
    this.stateService = stateService.getStore();
  }

  handler(to, from, next) {
    this.to = to;
    if (to.name !== "site.workflow") {
      this.next = next;
      this.getSite(to.params.site);
      if (!this.site) {
        return this.stateService.dispatch("user/sites/get").then(() => {
          this.getSite(to.params.site);
          return this.checkWorkflow();
        });
      }
      return this.checkWorkflow();
    }
    return next();
  }

  private getSite(siteId) {
    return (this.site = this.stateService.getters["user/sites/show"](siteId));
  }
  checkWorkflow() {
    if (!this.site.repository) {
      if (this.to.name !== "site.setup") {
        return this.next({
          name: "site.setup",
          params: { site: this.site.id },
        });
      }
    } else if (!this.site.workflow) {
      return this.next({
        name: "site.workflow",
        params: { site: this.site.id },
      });
    }
    return this.next(
      this.stateService.getters["user/sites/getNextWorkFlowStep"](this.site),
    );
  }
}
