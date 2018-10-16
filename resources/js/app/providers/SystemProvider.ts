import ServiceProvider from "varie/lib/support/ServiceProvider";
import SystemServerProviderService from "@app/services/System/SystemServerProviderService";
import SystemSourceControlProviderService from "@app/services/System/SystemSourceControlProviderService";

export default class SystemProvider extends ServiceProvider {
  public register() {
    // TODO - rename these
    this.app.bind("SystemServerProviderService", SystemServerProviderService);
    this.app.bind(
      "SystemSourceControlProviderService",
      SystemSourceControlProviderService,
    );
  }
}
