let mix = require('laravel-mix')

const path = require('path')
let directory = path.basename(path.resolve(__dirname))

const source = 'platform/plugins/' + directory
const dist = 'public/vendor/core/plugins/' + directory

mix
    .js(source + '/resources/assets/js/request-log.js', dist + '/js')

if (mix.inProduction()) {
    mix.copy(dist + '/js/request-log.js', source + '/public/js')
}
