import { SourceControlProvidersState } from "./stateInterface";

export default function() {
  return {
    SAMPLE_GETTER: (state: SourceControlProvidersState) => {
      return state;
    },
  };
}
