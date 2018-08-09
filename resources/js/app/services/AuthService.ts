import { injectable, inject } from "inversify";
import HttpServiceInterface from "varie/lib/http/HttpServiceInterface";

@injectable()
export default class AuthService {

    private apiRouteService;
    private $http : HttpServiceInterface;

    constructor(
        @inject('$http') $http,
        @inject('ApiRouteService') ApiRouteService
    ) {
        this.$http = $http;
        this.apiRouteService = ApiRouteService;
    }

    login(email, password) {
        return this.$http.post('/api'+this.apiRouteService.action('AuthLoginController@login'), {
            email,
            password
        });
    }

    oAuthLogin(provider, code, state) {
        return this.$http.get(this.apiRouteService.action('Auth\OauthController@getHandleProviderCallback', {
            state,
            code,
            provider
        }));
    }
}