import { ServicesState } from "./stateInterface";

export default function() {
  return {
    SAMPLE_GETTER: (state: ServicesState) => {
      return state;
    },
  };
}
