import { ServerState } from "./stateInterface";

export default function() {
  return {
    SET_DELETED_SERVERS: (state: ServerState, data) => {
      state.deletedServers = data;
    },
  };
}
