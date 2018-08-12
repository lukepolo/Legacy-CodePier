import { AuthState } from "./stateInterface";

export default class Mutations {
  SET_USER = (state: AuthState, user) => {
    state.user = user;
  };

  UPDATE_AUTH_AREA_DATA = (state: AuthState, data) => {
    state.authAreaData = Object.assign(state.authAreaData, data);
  };
}
