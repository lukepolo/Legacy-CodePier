import { FrameworksState } from "./stateInterface";

export default function() {
  return {
    SAMPLE_GETTER: (state: FrameworksState) => {
      return state;
    },
  };
}
