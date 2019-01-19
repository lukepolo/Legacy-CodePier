import BroadcastProvider from "@app/providers/BroadcastProvider";
import AppServiceProvider from "@app/providers/AppServiceProvider";
import StateServiceProvider from "@app/providers/StateServiceProvider";
import RoutingServiceProvider from "@app/providers/RouteServiceProvider";

import PileProvider from "@app/providers/PileProvider";
import SiteProvider from "@app/providers/SiteProvider";
import UserProvider from "@app/providers/UserProvider";
import AdminProvider from "@app/providers/AdminProvider";
import ServerProvider from "@app/providers/ServerProvider";
import SystemProvider from "@app/providers/SystemProvider";
import AuthServiceProvider from "@app/providers/AuthServiceProvider";
import NotificationProvider from "@app/providers/NotificationProvider";

import {
  FormServiceProvider,
  AlertServiceProvider,
  HttpServiceProvider,
  ConfigServiceProvider,
  CookieServiceProvider,
  StorageServiceProvider,
  ValidationServiceProvider,
  AutoRegisterMixinServiceProvider,
  AutoRegisterFilterServiceProvider,
  AutoRegisterLayoutServiceProvider,
  AutoRegisterComponentServiceProvider,
  AutoRegisterDirectiveServiceProvider,
} from "varie";

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
     * Varie Framework Service Providers...
     */
    ConfigServiceProvider,
    CookieServiceProvider,
    StorageServiceProvider,
    HttpServiceProvider,
    StateServiceProvider,
    RoutingServiceProvider,

    /*
     * Varie Plugin Service Providers...
     */
    FormServiceProvider,
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
    AuthServiceProvider,
    BroadcastProvider,
    AlertServiceProvider,
    AdminProvider,
    NotificationProvider,
    PileProvider,
    ServerProvider,
    SiteProvider,
    SystemProvider,
    UserProvider,
  },
};
