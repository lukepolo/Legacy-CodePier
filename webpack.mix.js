const { mix } = require('laravel-mix');

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
    .options({
        purifyCss: true
    })
    .js('resources/assets/js/app.js', 'public/js')
    .sass('resources/assets/sass/app.scss', 'public/css')
    .sass('resources/assets/sass/public.scss', 'public/css')
    .browserSync({
        proxy : 'codepier.dev',
        host: 'codepier.dev',
        open: 'external'
    })
    .extract([
        'vue',
        'brace',
        'jquery',
        'lodash',
        'progress',
        'jcf-forms',
        'pusher-js',
        'clipboard',
        'vue-router',
        'laravel-echo',
        'filesize-parser',
        'lodash-inflection',
    ])
    .autoload({
        jquery: ['$', 'jQuery'],
        lodash : '_',
        clipboard : 'Clipboard',
        moment : 'moment',
        'pusher-js' : 'Pusher',
    })
    .version()