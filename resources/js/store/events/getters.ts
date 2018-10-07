import { EventsState } from "./stateInterface";

export default function() {
  return {
    SAMPLE_GETTER: (state: EventsState) => {
      return state;
    },
  };
}
