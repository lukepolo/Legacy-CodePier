import { SitesState } from "./stateInterface";

export default class Mutations {
  SET_SITES = (state: SitesState, data) => {
    state.sites = Object.assign([], state.sites, data);
  };
}
