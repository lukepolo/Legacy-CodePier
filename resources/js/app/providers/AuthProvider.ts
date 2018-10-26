import AuthService from "@app/services/AuthService";
import OauthService from "@app/services/OauthService";
import JwtGuard from "varie/lib/authentication/guards/JwtGuard";
import ServiceProvider from "varie/lib/support/ServiceProvider";
import TwoFactorAuthentication from "@app/services/TwoFactorAuthentication";

export default class AuthProvider extends ServiceProvider {
  public register() {
    this.app.bind("JwtGuard", JwtGuard);
    this.app.bind("AuthService", AuthService);
    this.app.bind("OauthService", OauthService);
    this.app.bind("TwoFactorAuthentication", TwoFactorAuthentication);
  }
}
