import CookieStorage from "@app/services/CookieStorage";
import StateServiceInterface from "varie/lib/state/StateServiceInterface";

export default function(to, from, next) {
  if (!$app.make<CookieStorage>("CookieStorage").get("token")) {
    next({
      name: "login",
    });
    return false;
  }
  let $store = $app.make<StateServiceInterface>("$store").getStore();

  if (!$store.state.auth.user) {
    $store.dispatch("auth/me").then(() => {
      return true;
    });
  }
  return true;
}
