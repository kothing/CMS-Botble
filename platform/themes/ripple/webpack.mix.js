let mix = require('laravel-mix')
const purgeCss = require('@fullhuman/postcss-purgecss')

const path = require('path')
let directory = path.basename(path.resolve(__dirname))

const source = 'platform/themes/' + directory
const dist = 'public/themes/' + directory

mix
    // purgeCSS is used to remove unused CSS, if you are not sure about it, you can change it to
    // .sass(source + '/assets/sass/style.scss', dist + '/css')
    .sass(source + '/assets/sass/style.scss', dist + '/css', {}, [
        purgeCss({
            content: [
                source + '/layouts/*.blade.php',
                source + '/partials/*.blade.php',
                source + '/partials/**/*.blade.php',
                source + '/views/*.blade.php',
                source + '/views/**/*.blade.php',
                source + '/widgets/**/templates/frontend.blade.php',
                'platform/plugins/contact/resources/views/forms/contact.blade.php',
            ],
            defaultExtractor: (content) => content.match(/[\w-/.:]+(?<!:)/g) || [],
            safelist: [
                /^navigation-/,
                /^language-/,
                /language_bar_list/,
                /show-admin-bar/,
                /^fa-/,
                /breadcrumb/,
                /active/,
                /disabled/,
                /pagination/,
                /page-item/,
                /page-link/,
                /show/,
                /ck-content/,
            ],
        }),
    ])
    .js(source + '/assets/js/ripple.js', dist + '/js')
    .js(source + '/assets/js/icons-field.js', dist + '/js')

if (mix.inProduction()) {
    mix.copy(dist + '/css/style.css', source + '/public/css')
        .copy(dist + '/js/ripple.js', source + '/public/js')
        .copy(dist + '/js/icons-field.js', source + '/public/js')
}
