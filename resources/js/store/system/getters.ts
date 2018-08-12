import { SystemState } from "./stateInterface";

export default function() {
  return {
    SAMPLE_GETTER: (state: SystemState) => {
      return state;
    },
  };
}
