import ConfigInterface from "varie/lib/config/ConfigInterface";
import StateServiceInterface from "varie/lib/state/StateServiceInterface";
import LocalStorage from "@app/services/LocalStorage";

export default function(to, from, next) {
  if (
    !$app.make<LocalStorage>("LocalStorage").get("token")
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
