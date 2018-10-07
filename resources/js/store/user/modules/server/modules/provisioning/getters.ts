import { ProvisioningState } from "./stateInterface";

export default function() {
  return {
    SAMPLE_GETTER: (state: ProvisioningState) => {
      return state;
    },
  };
}
