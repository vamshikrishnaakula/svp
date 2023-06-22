const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
    .postCss('resources/css/app.css', 'public/css', [
        require('postcss-import'),
        require('tailwindcss'),
    ])
    .webpackConfig(require('./webpack.config'));


/** --------------------------------------------------------------------------
 * Add all the JS files to combine here
 * ------------------------------------------------------------------------ */
mix.js([
        'resources/js/common.js'
    ], 'public/js/common.js')
    .js([
        'resources/js/attendance.js',
        'resources/js/compare-probationers.js',
        'resources/js/extra-classes.js',
        'resources/js/extra-sessions.js',
        'resources/js/fitness-evaluation.js',
        'resources/js/timetable.js',
        'resources/js/activity.js',
        'resources/js/notifications.js',
        'resources/js/personal-notes.js',
        'resources/js/reports.js'
    ], 'public/js/combined.js')
    .js([
        'resources/js/faculty-dashboard.js'
    ], 'public/js/faculty-dashboard.js')
    .js([
        'resources/js/probationer-dashboard.js'
    ], 'public/js/probationer-dashboard.js')
    .js([
        'resources/js/receptionist.js'
    ], 'public/js/receptionist.js')
    .js([
        'resources/js/squads.js'
    ], 'public/js/squads.js')
    .js([
        'resources/js/statistics.js'
    ], 'public/js/statistics.js');

/** --------------------------------------------------------------------------
 * Add all the CSS files to combine here
 * ------------------------------------------------------------------------ */
mix.styles([
        'resources/css/common-styles.css',
        'resources/css/activities.css',
        'resources/css/attendance.css',
        'resources/css/compare-probationers.css',
        'resources/css/extrasessions.css',
        'resources/css/timetable.css',
        'resources/css/notifications.css',
        'resources/css/personal-notes.css'
    ], 'public/css/combined.css')
    .styles([
        'resources/css/probationer-dashboard.css',
    ], 'public/css/probationer-dashboard.css')
    .styles([
        'resources/css/faculty-dashboard.css',
    ], 'public/css/faculty-dashboard.css')
    .styles([
        'resources/css/style.css',
    ], 'public/css/style.css');


 /**
  * Print this jquery library
  * src: https://github.com/jasonday/printThis
  */
mix.js('resources/js/printThis.js', 'public/js/printThis.js');
