import { SettingsState } from "./stateInterface";

export default function() {
  return {
    SAMPLE_GETTER: (state: SettingsState) => {
      return state;
    },
  };
}
