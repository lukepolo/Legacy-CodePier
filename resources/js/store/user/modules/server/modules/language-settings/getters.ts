import { LanguageSettingsState } from "./stateInterface";

export default function() {
  return {
    SAMPLE_GETTER: (state: LanguageSettingsState) => {
      return state;
    },
  };
}
