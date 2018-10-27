import { injectable, inject } from "inversify";
import VarieAuthService from "varie-authentication-plugin/lib/AuthService";

@injectable()
export default class AuthService extends VarieAuthService {
  //
  // oAuthLogin(provider, code, state) {
  //   return this.httpService.get(
  //     this.apiRouteService.action(
  //       "AuthOauthController@getHandleProviderCallback",
  //       {
  //         state,
  //         code,
  //         provider,
  //       },
  //     ),
  //   );
  // }
  //
  // oAuthConnect(provider, code, state) {
  //   return this.httpService.get(
  //     this.apiRouteService.action(
  //       "AuthOauthController@getHandleProviderCallback",
  //       {
  //         state,
  //         code,
  //         provider,
  //       },
  //     ),
  //   );
  // }
  //
  //
}
