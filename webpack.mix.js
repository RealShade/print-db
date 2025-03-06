const mix = require('laravel-mix');

mix.less('resources/less/styles.less', 'public/assets/css')
    .js('resources/js/app.js', 'public/js')
    .babelConfig({
        presets: ['@babel/preset-env']
    })
    .autoload({
        jquery: ['$', 'window.jQuery', 'jQuery']
    });
