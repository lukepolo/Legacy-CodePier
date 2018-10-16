import HttpServiceProvider from "varie/lib/http/ServiceProvider";
import BroadcastProvider from "@app/providers/BroadcastProvider";
import AppServiceProvider from "@app/providers/AppServiceProvider";
import ConfigServiceProvider from "varie/lib/config/ServiceProvider";
import CookieServiceProvider from "varie/lib/cookies/CookieServiceProvider";
import StoreServiceProvider from "@app/providers/StoreServiceProvider";
import RoutingServiceProvider from "@app/providers/RouteServiceProvider";
import FormsServiceProvider from "varie/lib/plugins/forms/ServiceProvider";
import ValidationServiceProvider from "varie/lib/validation/ServiceProvider";
import NotificationsProvider from "varie/lib/plugins/notifications/ServiceProvider";
import AutoRegisterMixinsServiceProvider from "varie/lib/plugins/autoRegisterMixins/ServiceProvider";
import AutoRegisterFiltersServiceProvider from "varie/lib/plugins/autoRegisterFilters/ServiceProvider";
import AutoRegisterLayoutsServiceProvider from "varie/lib/plugins/autoRegisterLayouts/ServiceProvider";
import AutoRegisterComponentsServiceProvider from "varie/lib/plugins/autoRegisterComponents/ServiceProvider";
import AutoRegisterDirectivesServiceProvider from "varie/lib/plugins/autoRegisterDirectives/ServiceProvider";
import AdminProvider from "@app/providers/AdminProvider";
import AuthProvider from "@app/providers/AuthProvider";
import NotificationProvider from "@app/providers/NotificationProvider";
import PileProvider from "@app/providers/PileProvider";
import ServerProvider from "@app/providers/ServerProvider";
import SiteProvider from "@app/providers/SiteProvider";
import SystemProvider from "@app/providers/SystemProvider";
import UserProvider from "@app/providers/UserProvider";

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
    StoreServiceProvider,
    RoutingServiceProvider,
    HttpServiceProvider,
    FormsServiceProvider,
    ValidationServiceProvider,
    AutoRegisterMixinsServiceProvider,
    AutoRegisterFiltersServiceProvider,
    AutoRegisterLayoutsServiceProvider,
    AutoRegisterComponentsServiceProvider,
    AutoRegisterDirectivesServiceProvider,

    /*
     * Application Service Providers...
     */
    AppServiceProvider,
    BroadcastProvider,
    NotificationsProvider,
    AdminProvider,
    AuthProvider,
    NotificationProvider,
    PileProvider,
    ServerProvider,
    SiteProvider,
    SystemProvider,
    UserProvider,
  },
};
