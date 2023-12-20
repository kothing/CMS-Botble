let mix = require('laravel-mix')

const path = require('path')
let directory = path.basename(path.resolve(__dirname))

const source = 'platform/packages/' + directory
const dist = 'public/vendor/core/packages/' + directory

mix
    .js(source + '/resources/assets/js/seo-helper.js', dist + '/js')
    .sass(source + '/resources/assets/sass/seo-helper.scss', dist + '/css')

if (mix.inProduction()) {
    mix
        .copy(dist + '/js/seo-helper.js', source + '/public/js')
        .copy(dist + '/css/seo-helper.css', source + '/public/css')
}
