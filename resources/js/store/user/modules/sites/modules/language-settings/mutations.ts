import { LanguageSettingsState } from "./stateInterface";

export default function() {
  return {
    SET_AVAILABLE_SETTINGS: (state: LanguageSettingsState, data) => {
      state.available_language_settings = data;
    },
  };
}
