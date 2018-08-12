import SiteModel from "@models/SiteModel";
import { SitesState } from "./stateInterface";

export default function() {
  return {
    sitesByPileId: (state: SitesState) => (pileId) => {
      return state.sites.filter((site: SiteModel) => {
        return site.pile_id === pileId;
      });
    },
  };
}
