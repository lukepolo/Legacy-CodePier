require('dotenv').config({
    path: __dirname + '/.env'
});

var env = process.env;
var elixir = require('laravel-elixir');

require('laravel-elixir-vue');

var bower_path = './resources/assets/bower/';

var paths = {

    /*
     |--------------------------------------------------------------------------
     | Directory Paths
     |--------------------------------------------------------------------------
     |
     */

    public_build: './public/build/',
    fonts_build: './public/build/fonts/',
    imgs_build: './public/build/img/',

    js_resources: './resources/assets/js/',
    js_public: './public/js/',
    css_public: './public/css/',

    sass_partials : './resources/assets/sass/partials/',

    /*
     |--------------------------------------------------------------------------
     | Vendors
     |--------------------------------------------------------------------------
     |
     */

    bootstrap: bower_path + 'bootstrap-sass/assets/',
    fontawesome: bower_path + 'font-awesome/',
    jquery: bower_path + 'jquery/dist/',
    select2: bower_path + 'select2/dist/',
    moment : bower_path + 'moment/',
    moment_timezone : bower_path + 'moment-timezone/builds/',
    confirm2: bower_path + 'jquery-confirm2/dist/',
    ace : bower_path + 'ace-build/src-min/',
    jcf_forms : bower_path + 'jcf-forms/',
    jquery_cron : bower_path + 'jquery-cron/cron/'
    
};

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 */

elixir((mix) => {
    mix
        .copy(paths.fontawesome + 'fonts', paths.fonts_build)
        .copy(paths.bootstrap + 'fonts', paths.fonts_build + 'bootstrap')
        .copy(paths.jcf_forms + 'dist/img/icons', paths.imgs_build + 'icons')
        .sass('app.scss')
        .scripts([
            paths.jquery + 'jquery.min.js',
            paths.bootstrap + 'javascripts/bootstrap.js',
            paths.select2 + 'js/select2.js',
            paths.moment + 'moment.js',
            paths.moment_timezone + 'moment-timezone-with-data-2010-2020.min.js',
            paths.confirm2 + 'jquery-confirm.min.js',
            paths.ace + 'ace.js',
            paths.ace + 'mode-sh.js',
            paths.ace + 'ext-searchbox.js',
            paths.js_resources + 'laroute.js',
            paths.jcf_forms + 'assets/js/jcf_forms.js',
            paths.jquery_cron + 'jquery-cron-min.js'
        ])
        .version([
            paths.css_public + "app.css",
            paths.js_public + "all.js"
        ])
        .webpack('app.js')
        .browserSync({
            proxy: env.APP_URL,
            files : [
                elixir.config.appPath + '/**/*.php',
                elixir.config.get('public.css.outputFolder') + '/**/*.css',
                elixir.config.get('public.js.outputFolder') + '/**/*.js',
                elixir.config.get('public.versioning.buildFolder') + '/rev-manifest.json',
                elixir.config.viewPath + '/**/*.php',
                elixir.config.publicPath + '/js/app.js'
            ]
        });
});