let mix = require('laravel-mix')

const path = require('path')
let directory = path.basename(path.resolve(__dirname))

const source = 'platform/core/' + directory
const dist = 'public/vendor/core/core/' + directory

mix
    .js(source + '/resources/assets/js/profile.js', dist + '/js')
    .js(source + '/resources/assets/js/role.js', dist + '/js')

    .sass(source + '/resources/assets/sass/login.scss', dist + '/css')

if (mix.inProduction()) {
    mix
        .copy(dist + '/js/profile.js', source + '/public/js')
        .copy(dist + '/js/role.js', source + '/public/js')
        .copy(dist + '/css/login.css', source + '/public/css')
}
