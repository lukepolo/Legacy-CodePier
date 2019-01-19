import PileService from "@app/services/PileService";
import { ServiceProvider } from "varie";

export default class PileProvider extends ServiceProvider {
  public register() {
    this.app.bind("PileService", PileService);
  }
}
