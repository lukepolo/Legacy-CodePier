import { LifeLinesState } from "./stateInterface";

export default function() {
  return {
    SAMPLE_GETTER: (state: LifeLinesState) => {
      return state;
    },
  };
}
