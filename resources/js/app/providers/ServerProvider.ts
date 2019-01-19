import ServerService from "@app/services/ServerService";
import { ServiceProvider } from "varie";

export default class ServerProvider extends ServiceProvider {
  public register() {
    this.app.bind("ServerService", ServerService);
  }
}
