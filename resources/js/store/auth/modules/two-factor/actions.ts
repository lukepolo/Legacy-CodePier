import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { TwoFactorState } from "./stateInterface";
import TwoFactorAuthentication from "@app/services/TwoFactorAuthentication";

export default function(twoFactorAuthentication: TwoFactorAuthentication) {
  return {
    validate: (context: ActionContext<TwoFactorState, RootState>, token) => {
      return twoFactorAuthentication.validate(token).then(({ data }) => {
        context.commit("auth/SET_USER", data, { root: true });
        return data;
      });
    },
    generateQr: (context: ActionContext<TwoFactorState, RootState>) => {
      return twoFactorAuthentication.generateQr().then(({ data }) => {
        return data;
      });
    },
  };
}
