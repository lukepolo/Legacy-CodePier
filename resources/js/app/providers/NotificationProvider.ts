import { ServiceProvider } from "varie";
import NotificationSettingService from "@app/services/Notification/NotificationSettingService";
import NotificationProviderService from "@app/services/Notification/NotificationProviderService";

export default class NotificationProvider extends ServiceProvider {
  public register() {
    this.app.bind("NotificationSettingService", NotificationSettingService);
    this.app.bind("NotificationProviderService", NotificationProviderService);
  }
}
