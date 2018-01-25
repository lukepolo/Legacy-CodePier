/*
 |--------------------------------------------------------------------------
 | Global Variables
 |--------------------------------------------------------------------------
 |
 */

window.laroute = require("./laroute");
window.moment = require("moment-timezone");
require("moment-precise-range-plugin");
moment.tz.setDefault("UTC");

/*
 |--------------------------------------------------------------------------
 | Vendors
 |--------------------------------------------------------------------------
 |
 */

_.mixin(require("lodash-inflection"));

/**
 * Ace editor
 */

require("brace");
require("brace/mode/sh");
require("brace/ext/searchbox");
require("brace/theme/monokai");

/*
 |--------------------------------------------------------------------------
 | Axios Setup
 |--------------------------------------------------------------------------
 |
 */

import loadProgressBar from "./plugins/loading";

window.axios = require("axios");

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
window.axios.defaults.headers.common["X-CSRF-TOKEN"] = window.Laravel.csrfToken;

loadProgressBar({
  easing: "ease",
  speed: 500,
  showSpinner: true
});

/*
 |--------------------------------------------------------------------------
 | Laravel Echo Setup
 |--------------------------------------------------------------------------
 |
 */

import Echo from "laravel-echo";

window.io = require('socket.io-client');

window.Echo = new Echo({
  broadcaster: "socket.io",
  key: Laravel.echoServerKey,
  host: `${window.location.hostname}:6001`,
});

/*
 |--------------------------------------------------------------------------
 | Sentry + Raven Setup
 |--------------------------------------------------------------------------
 |
 */
import Raven from "raven-js";
import RavenVue from "raven-js/plugins/vue";

if (Laravel.env !== "local") {
  Raven.config("https://50124e89d68945bb8f787666f0482807@sentry.codepier.io/4")
    .addPlugin(RavenVue, Vue)
    .install();
}
