import { AuthState } from "./stateInterface";

export default class Mutations {
  SET_USER = (state : AuthState, user) => {
    state.user = user;
  };
}
