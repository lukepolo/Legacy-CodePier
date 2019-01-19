import BuoyService from "@app/services/BuoyService";
import BittService from "@app/services/BittService";
import EventService from "@app/services/EventService";
import CategoryService from "@app/services/CategoryService";
import { ServiceProvider } from "varie";

export default class AdminProvider extends ServiceProvider {
  public register() {
    this.app.bind("CategoryService", CategoryService);
    this.app.bind("BittService", BittService);
    this.app.bind("BuoyService", BuoyService);
    this.app.bind("EventService", EventService);
  }
}
