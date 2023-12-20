const path = require('path');
const directory = path.basename(path.resolve(__dirname));
const source = 'platform/packages/' + directory;

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
         source + '/resources/views/*.blade.php',
         source + '/resources/views/partials/*.blade.php',
    ],
    plugins: [
        require('@tailwindcss/forms')
    ],
}
