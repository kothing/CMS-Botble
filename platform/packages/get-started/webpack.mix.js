let mix = require('laravel-mix')

const path = require('path')
let directory = path.basename(path.resolve(__dirname))

const source = 'platform/packages/' + directory
const dist = 'public/vendor/core/packages/' + directory

mix
    .js(source + '/resources/assets/js/get-started.js', dist + '/js')
    .sass(source + '/resources/assets/sass/get-started.scss', dist + '/css')

if (mix.inProduction()) {
    mix
        .copy(dist + '/js/get-started.js', source + '/public/js')
        .copy(dist + '/css/get-started.css', source + '/public/css')
}
