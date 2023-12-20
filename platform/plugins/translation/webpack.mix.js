let mix = require('laravel-mix')

const path = require('path')
let directory = path.basename(path.resolve(__dirname))

const source = 'platform/plugins/' + directory
const dist = 'public/vendor/core/plugins/' + directory

mix
    .js(source + '/resources/assets/js/translation.js', dist + '/js')
    .js(source + '/resources/assets/js/locales.js', dist + '/js')
    .js(source + '/resources/assets/js/theme-translations.js', dist + '/js')

    .sass(source + '/resources/assets/sass/translation.scss', dist + '/css')
    .sass(source + '/resources/assets/sass/theme-translations.scss', dist + '/css')

if (mix.inProduction()) {
    mix
        .copy(dist + '/js/translation.js', source + '/public/js')
        .copy(dist + '/js/locales.js', source + '/public/js')
        .copy(dist + '/js/theme-translations.js', source + '/public/js')
        .copy(dist + '/css/translation.css', source + '/public/css')
        .copy(dist + '/css/theme-translations.css', source + '/public/css')
}
