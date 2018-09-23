import { SettingsState } from "./stateInterface";

export default function() {
  return {
    SET_SETTINGS: (state: SettingsState, settings) => {
      state.settings = settings;
    },
  };
}
