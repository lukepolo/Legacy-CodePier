import { ServerFeatureState } from "./stateInterface";

export default function() {
  return {
    SAMPLE_GETTER: (state: ServerFeatureState) => {
      return state;
    },
  };
}
