import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { ServerState } from "./stateInterface";
import UserServerService from "@app/services/User/UserServerService";

export default function(userServerService: UserServerService) {
  return {
    getDeletedServers: (context: ActionContext<ServerState, RootState>) => {
      return userServerService.getDeletedServers().then(({ data }) => {
        context.commit("SET_DELETED_SERVERS", data);
        return data;
      });
    },
  };
}
