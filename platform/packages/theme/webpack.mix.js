let mix = require('laravel-mix')

const path = require('path')
let directory = path.basename(path.resolve(__dirname))

const source = 'platform/packages/' + directory
const dist = 'public/vendor/core/packages/' + directory

mix
    .js(source + '/resources/assets/js/custom-css.js', dist + '/js')
    .js(source + '/resources/assets/js/custom-js.js', dist + '/js')
    .js(source + '/resources/assets/js/custom-html.js', dist + '/js')
    .js(source + '/resources/assets/js/theme-options.js', dist + '/js')
    .js(source + '/resources/assets/js/theme.js', dist + '/js')


    .sass(source + '/resources/assets/sass/custom-css.scss', dist + '/css')
    .sass(source + '/resources/assets/sass/theme-options.scss', dist + '/css')
    .sass(source + '/resources/assets/sass/admin-bar.scss', dist + '/css')
    .sass(source + '/resources/assets/sass/guideline.scss', dist + '/css')

if (mix.inProduction()) {
    mix
        .copy(dist + '/js/custom-css.js', source + '/public/js')
        .copy(dist + '/js/custom-js.js', source + '/public/js')
        .copy(dist + '/js/custom-html.js', source + '/public/js')
        .copy(dist + '/js/theme-options.js', source + '/public/js')
        .copy(dist + '/js/theme.js', source + '/public/js')
        .copy(dist + '/css/custom-css.css', source + '/public/css')
        .copy(dist + '/css/theme-options.css', source + '/public/css')
        .copy(dist + '/css/admin-bar.css', source + '/public/css')
        .copy(dist + '/css/guideline.css', source + '/public/css')
}
