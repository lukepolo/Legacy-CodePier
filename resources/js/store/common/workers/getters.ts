import { WorkersState } from "./stateInterface";

export default function() {
  return {
    SAMPLE_GETTER: (state: WorkersState) => {
      return state;
    },
  };
}
