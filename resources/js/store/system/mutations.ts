import { SystemState } from "./stateInterface";

export default function() {
  return {
    SET_VERSION: (state: SystemState, version) => {
      state.version = version;
    },
  };
}
