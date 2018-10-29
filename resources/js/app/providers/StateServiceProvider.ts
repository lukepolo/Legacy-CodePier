import ServiceProvider from "varie/lib/state/StateServiceProvider";
import StateServiceInterface from "varie/lib/state/StateServiceInterface";

import UserStore from "@store/user/UserStore";
import ServerStore from "@store/server/ServerStore";
import SystemStore from "@store/system/SystemStore";
import NotificationStore from "@store/notifications/NotificationStore";
import SubscriptionStore from "@store/subscriptions/SubscriptionStore";
import SourceControlProviderStore from "@store/source-control-providers/SourceControlProviderStore";

/*
|--------------------------------------------------------------------------
| Store Service Provider
|--------------------------------------------------------------------------
|
*/
export default class StateServiceProvider extends ServiceProvider {
  public $store: StateServiceInterface;

  map() {
    this.$store
      .registerStore(SystemStore)
      .registerStore(UserStore)
      .registerStore(SubscriptionStore)
      .registerStore(ServerStore)
      .registerStore(SourceControlProviderStore)
      .registerStore(NotificationStore);
  }
}
