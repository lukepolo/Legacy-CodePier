import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { LanguageSettingsState } from "./stateInterface";
import SiteLanguageSettingService from "@app/services/Site/SiteLanguageSettingService";

export default function(
  siteLanguageSettingService: SiteLanguageSettingService,
) {
  return {
    getAvailable: (
      context: ActionContext<LanguageSettingsState, RootState>,
      data,
    ) => {
      return siteLanguageSettingService.getAvailable(data).then(({ data }) => {
        context.commit("SET_AVAILABLE_SETTINGS", data);
        return data;
      });
    },
  };
}
