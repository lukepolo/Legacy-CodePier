import Routes from "@routes";
import RouterInterface from "varie/lib/routing/RouterInterface";
import { RoutingServiceProvider as ServiceProvider } from "varie";

/*
|--------------------------------------------------------------------------
| Route Service Provider
|--------------------------------------------------------------------------
|
*/
export default class RoutingServiceProvider extends ServiceProvider {
  public $router: RouterInterface;

  map() {
    this.$router.register(Routes);
  }
}
