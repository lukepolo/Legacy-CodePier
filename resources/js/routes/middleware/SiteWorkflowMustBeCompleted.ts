import { injectable, inject } from "inversify";
import StateServiceInterface from "varie/lib/state/StateServiceInterface";
import RouteMiddlewareInterface from "varie/lib/routing/RouteMiddlewareInterface";

@injectable()
export default class SiteWorkflowMustBeCompleted
  implements RouteMiddlewareInterface {
  private storeService;

  constructor(@inject("StoreService") storeService: StateServiceInterface) {
    this.storeService = storeService.getStore();
  }

  handler(to, from, next) {
    if (!this.storeService.getters["user/sites/show"](to.params.site)) {
      return this.storeService.dispatch("user/sites/get").then(() => {
        let site = this.storeService.getters["user/sites/show"](to.params.site);
        if (site.workflow) {
          return next();
        }
        console.warn("TODO - they should be redirected to the workflow");
        return next();
      });
    }
  }
}
