import { ServerState } from "./stateInterface";

export default function() {
  return {
    SAMPLE_GETTER: (state: ServerState) => {
      return state;
    },
  };
}
