let mix = require('laravel-mix')

const path = require('path')
let directory = path.basename(path.resolve(__dirname))

const source = 'platform/core/' + directory
const dist = 'public/vendor/core/core/' + directory

mix
    .sass(source + '/resources/assets/sass/media.scss', dist + '/css')
    .js(source + '/resources/assets/js/media.js', dist + '/js')
    .js(source + '/resources/assets/js/jquery.addMedia.js', dist + '/js')
    .js(source + '/resources/assets/js/integrate.js', dist + '/js')

if (mix.inProduction()) {
    mix
        .copy(dist + '/js/media.js', source + '/public/js')
        .copy(dist + '/js/jquery.addMedia.js', source + '/public/js')
        .copy(dist + '/js/integrate.js', source + '/public/js')
        .copy(dist + '/css/media.css', source + '/public/css')
}
