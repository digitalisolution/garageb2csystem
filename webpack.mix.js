const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining Webpack build steps
 | for your Laravel application. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/calendarfrd.js', 'public/js')
   .js('resources/js/app.js', 'public/js')
   .js('resources/js/ampm-calendar.js', 'public/js')
   .postCss('resources/css/app.css', 'public/css')
   .sass('resources/sass/styles/app.scss', 'public/css')
   .version();
