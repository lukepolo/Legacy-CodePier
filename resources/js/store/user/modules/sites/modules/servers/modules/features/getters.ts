import { FeaturesState } from "./stateInterface";

export default function() {
  return {
    SAMPLE_GETTER: (state: FeaturesState) => {
      return state;
    },
  };
}
