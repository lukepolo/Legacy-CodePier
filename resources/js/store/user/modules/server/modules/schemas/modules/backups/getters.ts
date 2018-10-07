import { BackupsState } from "./stateInterface";

export default function() {
  return {
    SAMPLE_GETTER: (state: BackupsState) => {
      return state;
    },
  };
}
