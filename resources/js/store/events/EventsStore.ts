import state from "./state";
import actions from "./actions";
import getters from "./getters";
import mutations from "./mutations";
import StoreModule from "varie/lib/state/StoreModule";
import { injectable, inject, unmanaged } from "inversify";

@injectable()
export default class EventsStore extends StoreModule {
  constructor(@inject("EventService") eventService) {
    super();
    this.setName("events")
      .addState(state)
      .addActions(actions(eventService))
      .addMutations(mutations)
      .addGetters(getters);
  }
}
