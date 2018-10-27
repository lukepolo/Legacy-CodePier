import HttpServiceProvider from "varie/lib/http/HttpServiceProvider";
import BroadcastProvider from "@app/providers/BroadcastProvider";
import AppServiceProvider from "@app/providers/AppServiceProvider";
import ConfigServiceProvider from "varie/lib/config/ConfigServiceProvider";
import CookieServiceProvider from "varie/lib/cookies/CookieServiceProvider";
import StorageServiceProvider from "varie/lib/storage/StorageServiceProvider";
import StateServiceProvider from "@app/providers/StateServiceProvider";
import RoutingServiceProvider from "@app/providers/RouteServiceProvider";
import FormsServiceProvider from "varie/lib/plugins/forms/FormServiceProvider";
import ValidationServiceProvider from "varie/lib/validation/ValidationServiceProvider";
import NotificationsProvider from "varie/lib/plugins/notifications/NotificationServiceProvider";
import AutoRegisterMixinServiceProvider from "varie/lib/plugins/autoRegisterMixins/AutoRegisterMixinServiceProvider";
import AutoRegisterFilterServiceProvider from "varie/lib/plugins/autoRegisterFilters/AutoRegisterFilterServiceProvider";
import AutoRegisterLayoutServiceProvider from "varie/lib/plugins/autoRegisterLayouts/AutoRegisterLayoutServiceProvider";
import AutoRegisterComponentServiceProvider from "varie/lib/plugins/autoRegisterComponents/AutoRegisterComponentServiceProvider";
import AutoRegisterDirectiveServiceProvider from "varie/lib/plugins/autoRegisterDirectives/AutoRegisterDirectiveServiceProvider";

import PileProvider from "@app/providers/PileProvider";
import SiteProvider from "@app/providers/SiteProvider";
import UserProvider from "@app/providers/UserProvider";
import AdminProvider from "@app/providers/AdminProvider";
import ServerProvider from "@app/providers/ServerProvider";
import SystemProvider from "@app/providers/SystemProvider";
import NotificationProvider from "@app/providers/NotificationProvider";
import AuthenticationServiceProvider from "@app/providers/AuthenticationServiceProvider";

export default {
  /*
  |--------------------------------------------------------------------------
  | Mounting Element
  |--------------------------------------------------------------------------
  |
  | This is the element that the app will bind to
  |
  */
  mount: "#app",

  /*
  |--------------------------------------------------------------------------
  | App Name
  |--------------------------------------------------------------------------
  |
  */
  name: "Varie",

  /*
  |--------------------------------------------------------------------------
  | Application Timezone
  |--------------------------------------------------------------------------
  |
  | Here you may specify the default timezone for your application, which
  | will be used by the moment functions.
  |
  */
  timezone: "UTC",

  /*
  |--------------------------------------------------------------------------
  | Application Locale Configuration
  |--------------------------------------------------------------------------
  |
  |
  */
  locale: "en",

  /*
  |--------------------------------------------------------------------------
  | Service Providers
  |--------------------------------------------------------------------------
  | These will get auto loaded into the application to provide features
  |
  */
  providers: {
    /*
     * Framework Service Providers...
     */
    ConfigServiceProvider,
    CookieServiceProvider,
    StorageServiceProvider,
    StateServiceProvider,
    RoutingServiceProvider,
    HttpServiceProvider,
    FormsServiceProvider,
    ValidationServiceProvider,
    AutoRegisterMixinServiceProvider,
    AutoRegisterFilterServiceProvider,
    AutoRegisterLayoutServiceProvider,
    AutoRegisterComponentServiceProvider,
    AutoRegisterDirectiveServiceProvider,

    /*
     * Application Service Providers...
     */
    AppServiceProvider,
    BroadcastProvider,
    NotificationsProvider,
    AdminProvider,
    AuthenticationServiceProvider,
    NotificationProvider,
    PileProvider,
    ServerProvider,
    SiteProvider,
    SystemProvider,
    UserProvider,
  },
};
