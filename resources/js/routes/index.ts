/*
|--------------------------------------------------------------------------
| Your default routes for your application
|--------------------------------------------------------------------------
|
*/
import middleware from "./middleware";
import RouterInterface from "varie/lib/routing/RouterInterface";

import ErrorViews from "@views/errors";
import ServerViews from "@views/server";
import Dashboard from "@views/dashboard/Dashboard.vue";

import userRoutes from "./userRoutes";
import authRoutes from "./authRoutes";
import siteRoutes from "./siteRoutes";

export default function($router: RouterInterface) {
  // AUTHED
  $router
    .layout("authed")
    .middleware([middleware.Auth])
    .group(() => {
      $router.route("", Dashboard).setName("dashboard");

      userRoutes($router);
      siteRoutes($router);

      $router
        .route("server/create/:type?/:site?", ServerViews.CreateServer)
        .setName("server.create")
        .setAlias("server/create");

      $router.route("servers", ServerViews.Servers);
    });

  authRoutes($router);

  $router.route("*", ErrorViews.Error404);
}
