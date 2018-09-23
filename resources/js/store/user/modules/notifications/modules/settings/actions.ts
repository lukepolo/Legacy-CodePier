import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { SettingsState } from "./stateInterface";
import UserNotificationSettingService from "@app/services/User/UserNotificationSettingService";

export default function(
  userNotificationSettingService: UserNotificationSettingService,
) {
  return {
    get: (context: ActionContext<SettingsState, RootState>, data) => {
      return userNotificationSettingService.get().then(({ data }) => {
        context.commit("SET_SETTINGS", data);
        return data;
      });
    },
    update: (context: ActionContext<SettingsState, RootState>, data) => {
      return userNotificationSettingService
        .create(null, data)
        .then(({ data }) => {
          context.commit("SET_SETTINGS", data);
          return data;
        });
    },
  };
}
