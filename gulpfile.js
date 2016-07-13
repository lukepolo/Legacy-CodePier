require('dotenv').config({
    path: __dirname + '/.env'
});

require('laravel-elixir-vueify');

var env = process.env;
var elixir = require('laravel-elixir');
var bower_path = './resources/assets/bower/';

var paths = {
    public_build: './public/build/',
    fonts_build: './public/build/fonts/',
    imgs_build: './public/build/img/',

    js_resources: './resources/assets/js/',
    js_public: './public/js/',
    css_public: './public/css/',

    sass_partials : './resources/assets/sass/partials/',

    /* Vendor Files */
    bootstrap: bower_path + 'bootstrap-sass/assets/',
    fontawesome: bower_path + 'font-awesome/',
    jquery: bower_path + 'jquery/dist/',
    select2: bower_path + 'select2/dist/',
    moment : bower_path + 'moment/',
    moment_timezone : bower_path + 'moment-timezone/builds/',
    confirm2: bower_path + 'jquery-confirm2/dist/',
    codemirror : bower_path + 'codemirror/',
    jcf_forms : bower_path + 'jcf-forms/'
    
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

elixir.config.js.browserify.watchify.options.poll = true;

elixir(function (mix) {
    mix
        .copy(paths.fontawesome + 'fonts', paths.fonts_build)
        .copy(paths.bootstrap + 'fonts', paths.fonts_build + 'bootstrap')
        .copy(paths.jcf_forms + 'dist/img/icons', paths.imgs_build + 'icons')
        .copy(paths.codemirror + 'lib/codemirror.css', paths.sass_partials + '_codemirror.scss')
        .sass('app.scss')
        .scripts([
            paths.jquery + 'jquery.min.js',
            paths.bootstrap + 'javascripts/bootstrap.js',
            paths.select2 + 'js/select2.js',
            paths.moment + 'moment.js',
            paths.moment_timezone + 'moment-timezone-with-data-2010-2020.min.js',
            paths.confirm2 + 'jquery-confirm.min.js',
            paths.codemirror + 'lib/codemirror.js',
            paths.codemirror + 'mode/shell/shell.js',
            paths.js_resources + 'laroute.js',
            paths.jcf_forms + 'assets/js/jcf_forms.js'
        ])
        .version([
            paths.css_public + "app.css",
            paths.js_public + "all.js"
        ])
        .browserify('app.js',  paths.js_public + 'app.js')
        .browserSync({
            proxy: env.APP_URL
        });
});