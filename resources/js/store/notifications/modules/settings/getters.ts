import { NotificationSettingsState } from "./stateInterface";

export default function() {
  return {
    SAMPLE_GETTER: (state: NotificationSettingsState) => {
      return state;
    },
  };
}
