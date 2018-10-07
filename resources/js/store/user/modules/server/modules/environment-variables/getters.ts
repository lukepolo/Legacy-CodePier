import { EnvironmentVariablesState } from "./stateInterface";

export default function() {
  return {
    SAMPLE_GETTER: (state: EnvironmentVariablesState) => {
      return state;
    },
  };
}
