import UserService from "@app/services/UserService";
import { ServiceProvider } from "varie";
import UserSiteService from "@app/services/User/UserSiteService";
import SubscriptionService from "@app/services/SubscriptionService";
import UserServerService from "@app/services/User/UserServerService";
import UserSshKeyService from "@app/services/User/UserSshKeyService";
import UserServerProviderService from "@app/services/User/UserServerProviderService";
import UserNotificationSettingService from "@app/services/User/UserNotificationSettingService";
import UserNotificationProviderService from "@app/services/User/UserNotificationProviderService";
import UserSourceControlProviderService from "@app/services/User/UserSourceControlProviderService";

export default class UserProvider extends ServiceProvider {
  public register() {
    this.app.bind("UserService", UserService);
    this.app.bind("UserSiteService", UserSiteService);
    this.app.bind("UserServerService", UserServerService);
    this.app.bind("UserSshKeyService", UserSshKeyService);
    this.app.bind("SubscriptionService", SubscriptionService);
    this.app.bind("UserServerProviderService", UserServerProviderService);
    this.app.bind(
      "UserSourceControlProviderService",
      UserSourceControlProviderService,
    );
    this.app.bind(
      "UserNotificationProviderService",
      UserNotificationProviderService,
    );

    this.app.bind(
      "UserNotificationSettingService",
      UserNotificationSettingService,
    );
  }
}
