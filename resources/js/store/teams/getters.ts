import { TeamsState } from "./stateInterface";

export default function() {
  return {
    SAMPLE_GETTER: (state: TeamsState) => {
      return state;
    },
  };
}
