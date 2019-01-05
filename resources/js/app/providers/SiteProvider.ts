import SiteService from "../services/SiteService";
import ServiceProvider from "varie/lib/support/ServiceProvider";
import SiteServerService from "../services/Site/SiteServerService";
import UserSiteLifeLineService from "@app/services/User/UserSiteLifeLineService";

export default class SiteProvider extends ServiceProvider {
  public register() {
    this.app.bind("SiteService", SiteService);
    this.app.bind("SiteServerService", SiteServerService);
    this.app.bind("UserSiteLifeLineService", UserSiteLifeLineService);
  }
}
