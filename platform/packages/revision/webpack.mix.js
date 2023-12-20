let mix = require('laravel-mix')

const path = require('path')
let directory = path.basename(path.resolve(__dirname))

const source = 'platform/packages/' + directory
const dist = 'public/vendor/core/packages/' + directory

mix
    .sass(source + '/resources/assets/sass/revision.scss', dist + '/css')
    .js(source + '/resources/assets/js/revision.js', dist + '/js')

if (mix.inProduction()) {
    mix
        .copy(dist + '/css/revision.css', source + '/public/css')
        .copy(dist + '/js/revision.js', source + '/public/js')
}
