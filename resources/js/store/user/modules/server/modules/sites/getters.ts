import { SitesState } from "./stateInterface";

export default function() {
  return {
    SAMPLE_GETTER: (state: SitesState) => {
      return state;
    },
  };
}
