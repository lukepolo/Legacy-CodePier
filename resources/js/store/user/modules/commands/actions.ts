import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { CommandsState } from "./stateInterface";
import UserService from "@app/services/UserService";

export default function(userService: UserService) {
  return {
    getActiveCommands: (
      context: ActionContext<CommandsState, RootState>,
      data,
    ) => {
      return userService.getActiveCommands();
    },
  };
}
