const mix = require('laravel-mix')
const tailwindcss = require('tailwindcss')

const path = require('path')
const directory = path.basename(path.resolve(__dirname))

const source = 'platform/packages/' + directory
const dist = 'public/vendor/core/packages/' + directory

mix
    .sass(source + '/resources/assets/sass/style.scss', dist + '/css', {}, [tailwindcss(source + '/tailwind.config.js')])

if (mix.inProduction()) {
    mix.copy(dist + '/css/style.css', source + '/public/css')
}
