const mix = require('laravel-mix');

mix.less('resources/less/app.less', 'public/assets/css')
    .js('resources/js/app.js', 'public/assets/js')
    .babelConfig({
        presets: ['@babel/preset-env']
    })
    .autoload({
        jquery: ['$', 'window.jQuery', 'jQuery']
    });
