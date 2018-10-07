import { BuoysState } from "./stateInterface";

export default function() {
  return {
    SAMPLE_GETTER: (state: BuoysState) => {
      return state;
    },
  };
}
