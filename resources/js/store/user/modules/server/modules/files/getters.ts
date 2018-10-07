import { FilesState } from "./stateInterface";

export default function() {
  return {
    SAMPLE_GETTER: (state: FilesState) => {
      return state;
    },
  };
}
