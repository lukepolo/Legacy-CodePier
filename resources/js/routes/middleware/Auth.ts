import ConfigInterface from "varie/lib/config/ConfigInterface";
import StateServiceInterface from "varie/lib/state/StateServiceInterface";

export default function(to, from, next) {
  // // TODO - we need access to the token in a diff. place earlier
  // if (
  //   localStorage &&
  //   !localStorage.getItem("token") &&
  //   !$app.make<ConfigInterface>("$config").get("app.hasToken")
  // ) {
  //   next({
  //     name: "login",
  //   });
  //   return false;
  // }
  // let $store = $app.make<StateServiceInterface>("$store").getStore();
  //
  // if (!$store.state.auth.user) {
  //   $store.dispatch("auth/me").then(() => {
  //     return true;
  //   });
  // }
  return true;
}
