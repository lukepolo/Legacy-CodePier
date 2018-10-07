import { UsersState } from "./stateInterface";

export default function() {
  return {
    SAMPLE_GETTER: (state: UsersState) => {
      return state;
    },
  };
}
