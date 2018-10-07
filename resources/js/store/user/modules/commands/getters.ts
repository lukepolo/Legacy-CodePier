import { CommandsState } from "./stateInterface";

export default function() {
  return {
    SAMPLE_GETTER: (state: CommandsState) => {
      return state;
    },
  };
}
