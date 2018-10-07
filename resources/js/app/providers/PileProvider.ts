import PileService from "@app/services/PileService";
import ServiceProvider from "varie/lib/support/ServiceProvider";

export default class PileProvider extends ServiceProvider {
  public register() {
    this.app.bind("PileService", PileService);
  }
}
