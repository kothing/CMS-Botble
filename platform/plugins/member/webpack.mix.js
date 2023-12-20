let mix = require('laravel-mix')

const path = require('path')
let directory = path.basename(path.resolve(__dirname))

const source = 'platform/plugins/' + directory
const dist = 'public/vendor/core/plugins/' + directory

mix.js(source + '/resources/assets/js/app.js', dist + '/js')

mix
    .js(source + '/resources/assets/js/member-admin.js', dist + '/js')
    .js(source + '/resources/assets/js/activity-logs.js', dist + '/js')
    .sass(source + '/resources/assets/sass/member.scss', dist + '/css')
    .sass(source + '/resources/assets/sass/app.scss', dist + '/css')

if (mix.inProduction()) {
    mix
        .copy(dist + '/js/app.js', source + '/public/js')
        .copy(dist + '/js/activity-logs.js', source + '/public/js')
        .copy(dist + '/js/member-admin.js', source + '/public/js')
        .copy(dist + '/css/member.css', source + '/public/css')
        .copy(dist + '/css/app.css', source + '/public/css')
}
