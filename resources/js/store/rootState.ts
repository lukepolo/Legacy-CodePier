import Alertmodel from "varie/lib/plugins/alerts/models/Alertmodel";

/*
|--------------------------------------------------------------------------
| Root State
|--------------------------------------------------------------------------
| This manages the root state of the entire application, which allows
| typescript to let us know whats available
|
*/

export default interface rootState {
  varie: {
    notifications: {
      notifications: Array<Alertmodel>;
    };
  };
}
