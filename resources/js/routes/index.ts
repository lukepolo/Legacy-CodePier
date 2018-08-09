let $router = $app.make<RouterInterface>("$router");
import RouterInterface from "varie/lib/routing/RouterInterface";

/*
|--------------------------------------------------------------------------
| Your default routes for your application
|--------------------------------------------------------------------------
|
*/

$router.route("/provider/:provider/callback", "oauth");

$router
  .layout("authed")
  .middleware(["Auth"])
  .group(() => {
    $router.route("/", "dashboard/Dashboard").setName("dashboard");
  });

$router.route("/login", "login/Login").setName("login");

$router.route("*", "errors/404");
