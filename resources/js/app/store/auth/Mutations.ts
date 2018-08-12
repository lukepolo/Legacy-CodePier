import { AuthState } from "./stateInterface";

export default class Mutations {
  UPDATE_AUTH_AREA_DATA = (state: AuthState, data) => {
    state.authAreaData = Object.assign(state.authAreaData, data);
  };
}
