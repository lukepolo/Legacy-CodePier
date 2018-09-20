import Vue from "vue";
import PortalVue from "portal-vue";

import UserService from "@app/services/UserService";
import PileService from "@app/services/PileService";

import AuthService from "@app/services/AuthService";
import OauthService from "@app/services/OauthService";
import TwoFactorAuthentication from "@app/services/TwoFactorAuthentication";

import CookieStorage from "@app/services/CookieStorage";
import ServiceProvider from "varie/lib/support/ServiceProvider";
import NotificationSettingService from "@app/services/System/NotificationSettingService";

import SiteService from "@app/services/SiteService";
import SubscriptionService from "@app/services/SubscriptionService";
import SiteServerService from "@app/services/Site/SiteServerService";
import UserSshKeyService from "@app/services/User/UserSshKeyService";

import SystemServerProviderService from "@app/services/System/SystemServerProviderService";
import UserServerProviderService from "@app/services/User/UserServerProviderService";
import UserSourceControlProviderService from "@app/services/User/UserSourceControlProviderService";
import SystemSourceControlProviderService from "@app/services/System/SystemSourceControlProviderService";

/*
|--------------------------------------------------------------------------
| App Service Provider
|--------------------------------------------------------------------------
| You can bind various items to the app here, or can create other
| custom providers that bind the container
|
*/
export default class AppProviderServiceProvider extends ServiceProvider {
  public boot() {
    Vue.use(PortalVue);
  }

  public register() {
    // SYSTEM
    this.app.singleton("CookieStorage", CookieStorage);
    this.app.bind("SystemServerProviderService", SystemServerProviderService);
    this.app.bind("NotificationSettingService", NotificationSettingService);
    this.app.bind(
      "SystemSourceControlProviderService",
      SystemSourceControlProviderService,
    );

    // AUTH SERVICES
    this.app.bind("AuthService", AuthService);
    this.app.bind("OauthService", OauthService);
    this.app.bind("TwoFactorAuthentication", TwoFactorAuthentication);

    // PILE SERVICES
    this.app.bind("PileService", PileService);

    // SITE SERVICES
    this.app.bind("SiteService", SiteService);
    this.app.bind("SiteServerService", SiteServerService);

    // USER SERVICES
    this.app.bind("UserService", UserService);
    this.app.bind("UserSshKeyService", UserSshKeyService);
    this.app.bind("SubscriptionService", SubscriptionService);
    this.app.bind("UserServerProviderService", UserServerProviderService);
    this.app.bind(
      "UserSourceControlProviderService",
      UserSourceControlProviderService,
    );

    // ROUTING
    this.app.constant("ApiRouteService", require("@app/helpers/routes"));
  }
}
