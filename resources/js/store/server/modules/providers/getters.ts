import { ServerProvidersState } from "./stateInterface";

export default function() {
  return {
    SAMPLE_GETTER: (state: ServerProvidersState) => {
      return state;
    },
  };
}
