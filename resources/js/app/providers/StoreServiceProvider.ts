import ServiceProvider from "varie/lib/state/ServiceProvider";
import StateServiceInterface from "varie/lib/state/StateServiceInterface";

import AuthStore from "@store/auth/AuthStore";
import UserStore from "@store/user/UserStore";
import ServerStore from "@store/server/ServerStore";
import SystemStore from "@store/system/SystemStore";
import SubscriptionStore from "@store/subscriptions/SubscriptionStore";
import SourceControlProviderStore from "@store/source-control-providers/SourceControlProviderStore";
import NotificationStore from "@store/notifications/NotificationStore";

/*
|--------------------------------------------------------------------------
| Store Service Provider
|--------------------------------------------------------------------------
|
*/
export default class StoreServiceProvider extends ServiceProvider {
  public $store: StateServiceInterface;

  map() {
    this.$store
      .registerStore(AuthStore)
      .registerStore(SystemStore)
      .registerStore(UserStore)
      .registerStore(SubscriptionStore)
      .registerStore(ServerStore)
      .registerStore(SourceControlProviderStore)
      .registerStore(NotificationStore);
  }
}
