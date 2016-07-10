require('dotenv').config({
    path: __dirname + '/.env'
});

var env = process.env;
var elixir = require('laravel-elixir');
var bower_path = './resources/assets/bower/';

var paths = {
    public_build: './public/build/',
    fonts_build: './public/build/fonts/',
    imgs_build: './public/build/imgs/',

    js_resources: './resources/assets/js/',
    js_public: './public/js/',
    css_public: './public/css/',

    sass_partials : './resources/assets/sass/partials/',
    sass_partials : './resources/assets/sass/partials/',
    /* Vendor Files */
    bootstrap: bower_path + 'bootstrap-sass/assets/',
    fontawesome: bower_path + 'font-awesome/',
    jquery: bower_path + 'jquery/dist/',
    select2: bower_path + 'select2/dist/',
    moment : bower_path + 'moment/',
    confirm2: bower_path + 'jquery-confirm2/dist/',
    codemirror : bower_path + 'codemirror/'
};

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function (mix) {
    mix
        .copy(paths.fontawesome + 'fonts', paths.fonts_build)
        .copy(paths.bootstrap + 'fonts', paths.fonts_build + 'bootstrap')
        .copy(paths.codemirror + 'lib/codemirror.css', paths.sass_partials + '_codemirror.scss')
        .sass('app.scss')
        .scripts([
            paths.jquery + 'jquery.min.js',
            paths.bootstrap + 'javascripts/bootstrap.js',
            paths.select2 + 'js/select2.js',
            paths.moment + 'moment.js',
            paths.confirm2 + 'jquery-confirm.min.js',
            paths.codemirror + 'lib/codemirror.js',
            paths.codemirror + 'mode/shell/shell.js'
        ])
        .version([
            paths.css_public + "app.css",
            paths.js_public + "all.js"
        ])
        .browserSync({
            proxy: env.APP_URL
        });
});