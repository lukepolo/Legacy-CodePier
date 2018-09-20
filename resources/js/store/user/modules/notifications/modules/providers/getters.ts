import { ProvidersState } from "./stateInterface";

export default function() {
  return {
    SAMPLE_GETTER: (state: ProvidersState) => {
      return state;
    },
  };
}
