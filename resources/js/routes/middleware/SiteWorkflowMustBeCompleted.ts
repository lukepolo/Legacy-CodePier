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
    this.checkWorkflow();
  }

  checkWorkflow() {
    if (this.site.workflow) {
      return this.next();
    }
    console.warn("TODO - they should be redirected to the workflow");
    return this.next();
  }
}
