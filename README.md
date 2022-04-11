## Requirements
1. PHP 7.3+
2. PHP Modules: cli common json opcache mysql mbstring mcrypt zip fpm xml
3. MySql 5.7+
4. apache2

## Installation

1. clone the repo
2. cp .env.example to .env and fill the data
3. composer install
4. php artisan key:generate
5. npm install
6. npm run dev
7. php artisan migrate
8. php artisan db:seed --class=RoleSeeder
9. php artisan db:seed --class=AdminSeeder
10. add cron job * * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1 
11. php artisan serve (optional. You can instead configure the host for it )

## Admin User
To login firstly you should use email and password that you entered in .env as APP_ADMIN_EMAIL and APP_ADMIN_PASSWORD

## API docs 
You can find API docs by URL /api/documentation.

You should log in using "/login" endpoint - it will return Bearer token that you can paste in modal window after 
pressing "Authorize" button - after that You'll be able to use locked methods

## Contributors
mrudchenko@brightgrove.com
