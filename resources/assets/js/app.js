import store from "./store";
import router from "./router";

require("./bootstrap");
require("./components");
require("./directives");
require("./emitters");
require("./filters");
require("./plugins");
require("./mixins");

window.store = store;

const app = new Vue({
  store,
  router,
});

window.app = app;

app.$store.dispatch("user_piles/get");
app.$store.dispatch("user_sites/get");
app.$store.dispatch("user_servers/get");
app.$store.dispatch("events/get");
app.$store.dispatch("user_commands/get");
app.$store.dispatch("user_ssh_keys/get");

app.$store.dispatch("server_providers/get");
app.$store.dispatch("repository_providers/get");
app.$store.dispatch("notification_settings/get");
app.$store.dispatch(
  "user_repository_providers/get",
  app.$store.state.user.user.id,
);

app.$store.dispatch("user_teams/get");

Echo.channel("app").listen("ReleasedNewVersion", (data) => {
  app.$store.dispatch("system/setVersion", data);
});

app.$mount("#app-layout");
