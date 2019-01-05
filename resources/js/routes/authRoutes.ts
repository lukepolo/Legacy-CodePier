import AuthViews from "@views/auth";
import middleware from "./middleware";
import RouterInterface from "varie/lib/routing/RouterInterface";

export default function($router: RouterInterface) {
  $router.route("/provider/:provider/callback", AuthViews.Oauth);

  $router
    .area(AuthViews.AuthArea)
    .middleware([middleware.NoAuth])
    .group(() => {
      $router.route("login", AuthViews.Login);
      $router.route("register", AuthViews.Register);
      $router.route("reset-password", AuthViews.ResetPassword);
      $router.route("forgot-password", AuthViews.ForgotPassword);
    });
}
