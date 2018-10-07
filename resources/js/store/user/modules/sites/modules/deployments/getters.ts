import { DeploymentsState } from "./stateInterface";

export default function() {
  return {
    SAMPLE_GETTER: (state: DeploymentsState) => {
      return state;
    },
  };
}
