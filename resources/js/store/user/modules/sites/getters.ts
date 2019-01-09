import SiteModel from "@models/SiteModel";
import { SitesState } from "./stateInterface";

let getNextStep = (site) => {
  if (site.workflow) {
    let availableSteps = site.workflow
      .filter((step) => {
        return !step.completed;
      })
      .sort((a, b) => {
        return a.order > b.order;
      });

    let nextStep = availableSteps && availableSteps[0];
    if (nextStep) {
      return {
        name: nextStep.step,
      };
    }
  }
};

export default function() {
  return {
    sitesByPileId: (state: SitesState) => (pileId) => {
      return state.sites.filter((site: SiteModel) => {
        return site.pile_id === pileId;
      });
    },
    workFlowCompleted: (state: SitesState) => (site) => {
      return getNextStep(site) === undefined;
    },
    getNextWorkFlowStep: (state: SitesState) => (site) => {
      return getNextStep(site);
    },
  };
}
