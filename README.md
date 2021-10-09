## Installation

1. clone the repo
2. cp .env.example to .env and fill the data
3. composer install
4. npm install
5. npm run dev
6. php artisan migrate
7. php artisan db:seed --class=AdminSeeder
8. php artisan db:seed --class=RoleSeeder

## Admin User
To login firstly you should use email and password that you entered in .env as APP_ADMIN_EMAIL and APP_ADMIN_PASSWORD

## API docs 
You can find API docs by URL /api/documentation.

You should log in using "/login" endpoint - it will return Bearer token that you can paste in modal window after 
pressing "Authorize" button - after that You'll be able to use locked methods

## Contributors
mrudchenko@brightgrove.com
