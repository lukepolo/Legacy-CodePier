import { SystemState } from "./stateInterface";

export default class Mutations {
  SET_VERSION = (state: SystemState, version) => {
    state.version = version;
  };
}
