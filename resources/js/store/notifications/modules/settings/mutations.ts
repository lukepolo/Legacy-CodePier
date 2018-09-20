import { NotificationSettingsState } from "./stateInterface";

export default function() {
  return {
    SET_SETTINGS: (state: NotificationSettingsState, data) => {
      state.settings = data;
    },
  };
}
