import { LanguagesState } from "./stateInterface";

export default function() {
  return {
    SAMPLE_GETTER: (state: LanguagesState) => {
      return state;
    },
  };
}
