import { LanguagesState } from "./stateInterface";

export default function() {
  return {
    SET_LANGUAGES: (state: LanguagesState, data) => {
      // @ts-ignore
      state.languages = data;
    },
  };
}
