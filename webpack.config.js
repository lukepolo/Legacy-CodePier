const path = require("path");
const VarieBundler = require("varie-bundler");

module.exports = function(env, argv) {
  return new VarieBundler(argv, __dirname)
    .entry("app", ["resources/js/app/app.ts", "resources/sass/app.scss"])
    .aliases({
      "@app": path.join(__dirname, "resources/js/app"),
      "@routes": path.join(__dirname, "resources/js/routes"),
      "@config": path.join(__dirname, "resources/js/config"),
      "@store": path.join(__dirname, "resources/js/app/store"),
      "@models": path.join(__dirname, "resources/js/app/models"),
      "@resources": path.join(__dirname, "resources/js/resources"),
      "@views": path.join(__dirname, "resources/js/resources/views"),
      "@components": path.join(__dirname, "resources/js/app/components"),
    })
    .chainWebpack((config, env) => {
      config.plugin("clean").tap((opts) => {
        opts[0] = [
          "public/css",
          "public/js",
          "resources/views/layouts/app.blade.php",
        ];
        return opts;
      });

      config.when(!env.isHot, () => {
        config.plugin("html").tap((opts) => {
          opts[0].filename = "../resources/views/layouts/app.blade.php";
          return opts;
        });

        config.devServer.proxy("http://app.codepier.test");
      });
    })
    .build();
};
