const mix = require('laravel-mix');

mix.less('resources/less/app.less', 'public/assets/css')
    .less('resources/less/dropzone.less', 'public/assets/css') // Добавляем компиляцию dropzone.less
    .js('resources/js/app.js', 'public/assets/js')
    .babelConfig({
        presets: ['@babel/preset-env']
    })
    .autoload({
        jquery: ['$', 'window.jQuery', 'jQuery']
    });
