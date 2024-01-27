# Botble
Botble is a CMS based on Laravel Framework that allows you to build websites for any purpose. It has powerful tools for developers to build any kind of website.

# Feature
- Page, blog, menu, contact, gallery, statics blocks… modules are provided with the use of components to avoid boilerplate code.
- Multi language support. Unlimited number of languages.
- SEO & sitemap support: access sitemap.xml to see more
- Powerful media system, also support Amazon S3, DigitalOcean Spaces
- RESTful API using Laravel Sanctum.
- Custom fields: easy to add new fields to page, post, category…
- Google Analytics: display analytics data in admin panel.
- Translation tool: easy to translate front theme and admin panel to your language.
- CRUD generator: easy to create new plugin/package with just one command.
- Theme generator: generate a new theme with just one command.
- Widget generator: generate theme’s widgets using command.
- News theme are ready to use.
- Powerful Permission System: Manage user, team, role by permissions. Easy to manage user by permissions.
- Admin template comes with color schemes to match your taste.
- Fully Responsive: Compatible with all screen resolutions.
- Coding Standard: All code follow coding standards PSR-2 and best practices.

# Requirements
- Apache, nginx, or another compatible web server.
- PHP >= 7.0 >> Higher
- MySQL Database server
- PDO PHP Extension
- OpenSSL PHP Extension
- Mbstring PHP Extension
- Exif PHP Extension
- Fileinfo Extension
- XML PHP Extension
- Ctype PHP Extension
- JSON PHP Extension
- Tokenizer PHP Extension
- Module Re_write server
- PHP_CURL Module Enable

# Free plugins
There are some free plugins available on our Marketplace: https://marketplace.botble.com/products

Those plugins are working fine for products based on Botble CMS.

# Installation
Follow the steps mentioned below to install and run the project.

1. Clone or download the repository
2. Go to the project directory and run `composer install`
3. Create `.env` file by copying the `.env.example`. You may use the command to do that `cp .env.example .env`
4. Update the database name and credentials in `.env` file
5. Run the command to generate application key `php artisan key:generate`
6. Run the command `php artisan migrate --seed`
7. Link storage directory: `php artisan storage:link`
8. You may create a virtualhost entry to access the application or run `php artisan serve` from the project root and visit `http://127.0.0.1:8000`

# Create theme
You may use the command to a theme:  
`php artisan cms:theme:create themename`

# Demo
Homepage: https://cms.botble.com  
Author login page: https://cms.botble.com/login  
Author: john.smith@botble.com – 12345678  
Admin login page: https://cms.botble.com/admin  
Admin: botble – 159357  
Note: default username & password are autofilled.
