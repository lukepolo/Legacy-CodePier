import store from "./store";
import router from "./router";

require("./bootstrap");
require("./components");
require("./directives");
require("./emitters");
require("./mixins");

const app = new Vue({
  store,
  router
});

window.app = app;

app.$store.dispatch("user_sites/get");
app.$store.dispatch("user_commands/get");
app.$store.dispatch("user_ssh_keys/get");

Echo.channel("app").listen("ReleasedNewVersion", data => {
  app.$store.dispatch("system/setVersion", data);
});

app.$mount("#app-layout");
