import { FeaturesState } from "./stateInterface";

export default function() {
  return {
    hasFeature: (state: FeaturesState) => (service, feature) => {
      return (
        state.features[service] &&
        state.features[service][feature] &&
        state.features[service][feature].enabled
      );
    },
  };
}
