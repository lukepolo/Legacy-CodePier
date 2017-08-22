import store from "./store";
import router from "./router";

require("./bootstrap");
require("./components");
require("./directives");
require("./emitters");
require("./mixins");

window.store = store;

const app = new Vue({
  store,
  router
});

window.app = app;

if (app.$store.state.user.user.is_subscribed) {
  app.$store.dispatch("user_sites/get");
  app.$store.dispatch("user_commands/get");
  app.$store.dispatch("user_ssh_keys/get");
  app.$store.dispatch("user_piles/get");
  app.$store.dispatch('repository_providers/get')
}

app.$store.dispatch("user_teams/get");

Echo.channel("app").listen("ReleasedNewVersion", data => {
  app.$store.dispatch("system/setVersion", data);
});

app.$mount("#app-layout");
