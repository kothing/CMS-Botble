let mix = require('laravel-mix')

const path = require('path')
let directory = path.basename(path.resolve(__dirname))

const source = 'platform/plugins/' + directory
const dist = 'public/vendor/core/plugins/' + directory

mix
    .js(source + '/resources/assets/js/backup.js', dist + '/js')
    .sass(source + '/resources/assets/sass/backup.scss', dist + '/css')

if (mix.inProduction()) {
    mix
        .copy(dist + '/js/backup.js', source + '/public/js')
        .copy(dist + '/css/backup.css', source + '/public/css')
}
