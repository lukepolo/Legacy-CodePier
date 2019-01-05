import { DaemonsState } from "./stateInterface";

export default function() {
  return {
    SAMPLE_GETTER: (state: DaemonsState) => {
      return state;
    },
  };
}
