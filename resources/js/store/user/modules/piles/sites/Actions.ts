import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { SitesState } from "./stateInterface";
import SiteService from "@app/services/SiteService";
import getDecorators from "inversify-inject-decorators";

const { lazyInject } = getDecorators($app.$container);

export default class Actions {
  @lazyInject("SiteService")
  private $siteService: SiteService;

  get = (context: ActionContext<SitesState, RootState>) => {
    return this.$siteService.get().then(({ data }) => {
      context.commit("SET_SITES", data);
      return data;
    });
  };
}
