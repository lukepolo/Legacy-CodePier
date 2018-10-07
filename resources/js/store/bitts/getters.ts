import { BittsState } from "./stateInterface";

export default function() {
  return {
    SAMPLE_GETTER: (state: BittsState) => {
      return state;
    },
  };
}
