const mix = require("laravel-mix");

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
    "vue",
    "vuex",
    "brace",
    "axios",
    "jquery",
    "lodash",
    "hint.css",
    "nprogress",
    "clipboard",
    "vue-router",
    "vuedraggable",
    "laravel-echo",
    "moment-timezone",
    "filesize-parser",
    "lodash-inflection",
    "moment-precise-range-plugin",
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
    open: "external",
    host: "codepier.test",
    proxy: "codepier.test",
    files: [
      "resources/views/**/*.php",
      "public/js/**/*.js",
      "public/css/**/*.css",
    ],
  });
}
