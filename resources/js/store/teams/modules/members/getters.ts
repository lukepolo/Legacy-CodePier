import { MembersState } from "./stateInterface";

export default function() {
  return {
    SAMPLE_GETTER: (state: MembersState) => {
      return state;
    },
  };
}
