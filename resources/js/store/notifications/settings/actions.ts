import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { NotificationSettingsState } from "./stateInterface";
import NotificationSettingService from "@app/services/System/NotificationSettingService";

export default function(
  NotificationSettingService: NotificationSettingService,
) {
  return {
    getSettings: (
      context: ActionContext<NotificationSettingsState, RootState>,
    ) => {
      return NotificationSettingService.getSettings().then(({ data }) => {
        context.commit("SET_SETTINGS", data);
      });
    },
  };
}
