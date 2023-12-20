let mix = require('laravel-mix')

const path = require('path')
let directory = path.basename(path.resolve(__dirname))

const source = 'platform/packages/' + directory
const dist = 'public/vendor/core/packages/' + directory

mix
    .js(source + '/resources/assets/js/plugin.js', dist + '/js')
    .js(source + '/resources/assets/js/marketplace.js', dist + '/js')

    .sass(source + '/resources/assets/sass/plugin.scss', dist + '/css')

if (mix.inProduction()) {
    mix
        .copy(dist + '/js/plugin.js', source + '/public/js')
        .copy(dist + '/js/marketplace.js', source + '/public/js')
        .copy(dist + '/css/plugin.css', source + '/public/css')
}
