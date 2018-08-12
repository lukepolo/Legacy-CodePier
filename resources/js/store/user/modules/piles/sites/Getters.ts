import { SitesState } from "./stateInterface";
import SiteModel from "@models/SiteModel";

export default class Getters {
  sitesByPileId = (state: SitesState) => (pileId) => {
    return state.sites.filter((site: SiteModel) => {
      return site.pile_id === pileId;
    });
  };
}
