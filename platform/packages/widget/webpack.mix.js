let mix = require('laravel-mix')

const path = require('path')
let directory = path.basename(path.resolve(__dirname))

const source = 'platform/packages/' + directory
const dist = 'public/vendor/core/packages/' + directory

mix
    .js(source + '/resources/assets/js/widget.js', dist + '/js')

if (mix.inProduction()) {
    mix
        .copy(dist + '/js/widget.js', source + '/public/js')
}
