let mix = require('laravel-mix')

const path = require('path')
let directory = path.basename(path.resolve(__dirname))

const source = 'platform/plugins/' + directory
const dist = 'public/vendor/core/plugins/' + directory

mix
    .js(source + '/resources/assets/js/social-login.js', dist + '/js')
    .sass(source + '/resources/assets/sass/social-login.scss', dist + '/css')

if (mix.inProduction()) {
    mix
        .copy(dist + '/css', source + '/public/css')
        .copy(dist + '/js', source + '/public/js')
}
