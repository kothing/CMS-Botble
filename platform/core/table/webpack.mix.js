let mix = require('laravel-mix')

const path = require('path')
let directory = path.basename(path.resolve(__dirname))

const source = 'platform/core/' + directory
const dist = 'public/vendor/core/core/' + directory

mix
    .js(source + '/resources/assets/js/table.js', dist + '/js')
    .js(source + '/resources/assets/js/filter.js', dist + '/js')
    .sass(source + '/resources/assets/sass/table.scss', dist + '/css')

if (mix.inProduction()) {
    mix
        .copy(dist + '/js/table.js', source + '/public/js')
        .copy(dist + '/js/filter.js', source + '/public/js')
        .copy(dist + '/css/table.css', source + '/public/css')
}
