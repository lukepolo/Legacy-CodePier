import { injectable, inject } from "inversify";
import VarieAuthService from "varie/lib/authentication/AuthService";
import HttpServiceInterface from "varie/lib/http/HttpServiceInterface";

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
