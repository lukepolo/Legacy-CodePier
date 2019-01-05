import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { SslCertificatesState } from "./stateInterface";

export default function(httpService) {
  return {
    sampleAction: (
      context: ActionContext<SslCertificatesState, RootState>,
      data,
    ) => {
      return httpService.post("/some-url", {
        data,
      });
    },
  };
}
