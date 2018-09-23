import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { NotificationSettingsState } from "./stateInterface";
import NotificationSettingService from "@app/services/notification/NotificationSettingService";

export default function(
  NotificationSettingService: NotificationSettingService,
) {
  return {
    get: (context: ActionContext<NotificationSettingsState, RootState>) => {
      return NotificationSettingService.getSettings().then(({ data }) => {
        context.commit("SET_SETTINGS", data);
      });
    },
  };
}
