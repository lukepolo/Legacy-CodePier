import { ServiceProvider } from "varie";
import ServerService from "@app/services/ServerService";
import ServerFeatureService from "@app/services/Server/ServerFeatureService";

export default class ServerProvider extends ServiceProvider {
  public register() {
    this.app.bind("ServerService", ServerService);
    this.app.bind("ServerFeatureService", ServerFeatureService);
  }
}
