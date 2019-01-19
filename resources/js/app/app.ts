/*
|--------------------------------------------------------------------------
| App Entry
|--------------------------------------------------------------------------
| Bootstrapping the application together
|
*/
import Vue from "vue";
import { Application } from "varie";
import BaseLayout from "varie/BaseLayout.vue";
import RouterInterface from "varie/lib/routing/RouterInterface";
import StateServiceInterface from "varie/lib/state/StateServiceInterface";

new Application().boot().then((app) => {
  //  /*
  // |--------------------------------------------------------------------------
  // | Sentry + Raven Setup
  // |--------------------------------------------------------------------------
  // |
  // */
  //  import Raven from "raven-js";
  //  import RavenVue from "raven-js/plugins/vue";
  //
  //  if (Laravel.env !== "local") {
  //    Raven.config("https://50124e89d68945bb8f787666f0482807@sentry.codepier.io/4")
  //      .addPlugin(RavenVue, Vue)
  //      .install();
  //  }

  new Vue({
    store: app.make<StateServiceInterface>("StateService").getStore(),
    router: app.make<RouterInterface>("RouterService").getRouter(),
    render: (h) => h(BaseLayout),
  }).$mount($config.get("app.mount"));
});
