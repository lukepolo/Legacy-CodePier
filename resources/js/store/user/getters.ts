import { UserState } from "./stateInterface";

export default function() {
  return {
    SAMPLE_GETTER: (state: UserState) => {
      return state;
    },
  };
}
