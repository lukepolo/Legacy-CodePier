import { injectable, inject } from "inversify";
import StateServiceInterface from "varie/lib/state/StateServiceInterface";
import RouteMiddlewareInterface from "varie/lib/routing/RouteMiddlewareInterface";

@injectable()
export default class SiteWorkflowMustBeCompleted
  implements RouteMiddlewareInterface {
  private next;
  private site;
  private storeService;

  constructor(@inject("StoreService") storeService: StateServiceInterface) {
    this.storeService = storeService.getStore();
  }

  handler(to, from, next) {
    if (to.name !== "site.setup") {
      this.next = next;
      this.site = this.storeService.getters["user/sites/show"](to.params.site);
      if (!this.site) {
        return this.storeService.dispatch("user/sites/get").then(() => {
          this.site = this.storeService.getters["user/sites/show"](
            to.params.site,
          );
          return this.checkWorkflow();
        });
      }
      return this.checkWorkflow();
    }
    return next();
  }

  checkWorkflow() {
    if (this.site.workflow) {
      return this.next();
    }
    return this.next({
      name: "site.setup",
      params: {
        site: this.site.id,
      },
    });
  }
}
