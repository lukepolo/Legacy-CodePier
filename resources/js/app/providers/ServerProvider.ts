import ServerService from "@app/services/ServerService";
import ServiceProvider from "varie/lib/support/ServiceProvider";

export default class ServerProvider extends ServiceProvider {
  public register() {
    this.app.bind("ServerService", ServerService);
  }
}
