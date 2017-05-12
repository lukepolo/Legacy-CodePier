        .copy(paths.fontawesome + 'fonts', paths.fonts_build)
        .copy(paths.jcf_forms + 'dist/img/icons', paths.imgs_build + 'icons')
        .scripts([
            paths.jquery + 'jquery.min.js',
            paths.bootstrap + 'js/bootstrap.js',
            paths.select2 + 'js/select2.js',
            paths.moment + 'moment.js',
            paths.moment_timezone + 'moment-timezone-with-data-2012-2022.min.js',
            paths.confirm2 + 'jquery-confirm.min.js',
            paths.ace + 'ace.js',
            paths.ace + 'mode-sh.js',
            paths.ace + 'ext-searchbox.js',
            paths.js_resources + 'laroute.js',
            paths.jcf_forms + '/js/jcf_forms.js',
            paths.jquery_cron + 'jquery-cron-min.js'
        ])
