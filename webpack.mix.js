let mix = require('laravel-mix');

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

mix.disableSuccessNotifications();

mix.copyDirectory('resources/img', 'public/img')
    .js('resources/js/admin/admin.js', 'public/js')
    .sass('resources/sass/admin.scss', 'public/css')
    .sass('resources/sass/front.scss', 'public/css')
    .extract(['vue','vue-router','moment','axios','lodash','dropzone']);
