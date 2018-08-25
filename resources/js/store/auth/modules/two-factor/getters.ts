import { TwoFactorState } from "./stateInterface";

export default function() {
  return {
    SAMPLE_GETTER: (state: TwoFactorState) => {
      return state;
    },
  };
}
