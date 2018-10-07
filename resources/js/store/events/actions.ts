import { ActionContext } from "vuex";
import RootState from "@store/rootState";
import { EventsState } from "./stateInterface";
import EventService from "@app/services/EventService";

export default function(eventService: EventService) {
  return {
    getEvents: (context: ActionContext<EventsState, RootState>, data) => {
      console.info(data);
      return eventService.getEvents(data);
    },
  };
}
