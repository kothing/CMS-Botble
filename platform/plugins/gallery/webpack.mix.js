let mix = require('laravel-mix')

const path = require('path')
let directory = path.basename(path.resolve(__dirname))

const source = 'platform/plugins/' + directory
const dist = 'public/vendor/core/plugins/' + directory

mix
    .sass(source + '/resources/assets/sass/gallery.scss', dist + '/css')
    .sass(source + '/resources/assets/sass/object-gallery.scss', dist + '/css')
    .sass(source + '/resources/assets/sass/admin-gallery.scss', dist + '/css')

    .js(source + '/resources/assets/js/gallery.js', dist + '/js/gallery.js')
    .js(source + '/resources/assets/js/gallery-admin.js', dist + '/js/gallery-admin.js')
    .js(source + '/resources/assets/js/object-gallery.js', dist + '/js/object-gallery.js')

if (mix.inProduction()) {
    mix
        .copy(dist + '/js/gallery.js', source + '/public/js')
        .copy(dist + '/js/gallery-admin.js', source + '/public/js')
        .copy(dist + '/js/object-gallery.js', source + '/public/js')
        .copy(dist + '/css/gallery.css', source + '/public/css')
        .copy(dist + '/css/admin-gallery.css', source + '/public/css')
        .copy(dist + '/css/object-gallery.css', source + '/public/css')
}
