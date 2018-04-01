const mix = require("laravel-mix");
let proxy = require("http-proxy-middleware");

let appUrl = "codepier.test";

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix
  .js("resources/assets/js/app.js", "public/js")
  .js("resources/assets/js/public.js", "public/js")
  .sass("resources/assets/sass/app.scss", "public/css")
  .sass("resources/assets/sass/public.scss", "public/css")
  .extract([
    "axios",
    "brace",
    "clipboard",
    "filesize-parser",
    "jquery",
    "laravel-echo",
    "lodash",
    "lodash-inflection",
    "moment-precise-range-plugin",
    "moment-timezone",
    "nprogress",
    "portal-vue",
    "raven-js",
    "vue",
    "vue-router",
    "vuedraggable",
    "vuex",
  ])
  .autoload({
    vue: "Vue",
    lodash: "_",
    ace: "brace",
    clipboard: "Clipboard",
    jquery: ["$", "jQuery"],
  })
  .sourceMaps()
  .version();

if (!mix.inProduction()) {
  mix.browserSync({
    host: appUrl,
    proxy: appUrl,
    open: "external",
    files: [
      "public/js/**/*.js",
      "public/css/**/*.css",
      "resources/views/**/*.php",
    ],
    middleware: [
      function(req, res, next) {
        let target =
          "http://" + req.headers.host.replace(`${appUrl}:3000`, appUrl);
        if (target !== `http://${appUrl}`) {
          proxy({
            target,
            changeOrigin: true,
            logLevel: "silent",
          })(req, res, next);
        } else {
          next();
        }
      },
    ],
  });
}
