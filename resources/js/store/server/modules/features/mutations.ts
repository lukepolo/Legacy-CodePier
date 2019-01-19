import { ServerFeatureState } from "./stateInterface";

export default function() {
  return {
    SET_FEATURES: (state: ServerFeatureState, data) => {
      state.features = data;
    },
  };
}
