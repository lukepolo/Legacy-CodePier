import { ServersState } from "./stateInterface";

export default function() {
  return {
    SAMPLE_GETTER: (state: ServersState) => {
      return state;
    },
  };
}