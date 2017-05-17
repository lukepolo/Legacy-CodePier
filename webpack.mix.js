const { mix } = require('laravel-mix')

console.info(mix.config.Paths)

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
    .js('resources/assets/js/app.js', 'public/js')
    .sass('resources/assets/sass/app.scss', 'public/css')
    // .sass('resources/assets/sass/public.scss', 'public/css')
    .extract([
        'vue',
        'vuex',
        'brace',
        'axios',
        'jquery',
        'lodash',
        'hint.css',
        'nprogress',
        'jcf-forms',
        'pusher-js',
        'clipboard',
        'vue-router',
        'vuedraggable',
        'laravel-echo',
        'moment-timezone',
        'filesize-parser',
        'lodash-inflection'
    ])
    .autoload({
        vue: 'Vue',
        lodash: '_',
        ace: 'brace',
        'pusher-js': 'Pusher',
        clipboard: 'Clipboard',
        jquery: ['$', 'jQuery']
    })

if (mix.config.inProduction) {
    mix.version()
} else {
    mix.browserSync({
        open: 'external',
        host: 'codepier.dev',
        proxy: 'codepier.dev',
        files: [
            'resources/views/**/*.php',
            'public/js/**/*.js',
            'public/css/**/*.css'
        ]
    })
}
