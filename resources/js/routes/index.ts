let $router = $app.make<RouterInterface>("$router");
import RouterInterface from "varie/lib/routing/RouterInterface";

/*
|--------------------------------------------------------------------------
| Your default routes for your application
|--------------------------------------------------------------------------
|
*/

$router.route("/provider/:provider/callback", "oauth");

$router.middleware(["Auth"]).group(() => {
  $router.route("/", "dashboard/Dashboard");
});

$router.route("/login", "login/Login").setName("login");

$router.route("*", "errors/404");
