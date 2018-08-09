import StateServiceInterface from "varie/lib/state/StateServiceInterface";
import ConfigInterface from "../../../../node_modules/varie/lib/config/ConfigInterface";

export default function(to, from, next) {
  if (
    localStorage &&
    !localStorage.getItem("token") &&
    !$app.make<ConfigInterface>("$config").get("app.hasToken")
  ) {
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
