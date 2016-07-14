## Laravel 5 API (based off vue-starter Backend API)

This application serves as a boilerplate for a laravel 5 API project. It is a Laravel 5 API, using Dingo and JWT for authentication.

The boilerplate was largely adopted from [vue-starter Backend API](https://github.com/layer7be/vue-starter-laravel-api)

## Installation

### Step 1: Clone the repo
```
git clone https://github.com/dcon138/laravel5-boilerplate
```

### Step 2: Prerequisites
```
cp .env.example .env
update .env with DB details
remove the pre-update-cmd from composer.json
composer install
re-add the pre-update-cmd to composer.json
php artisan migrate
php artisan db:seed
php artisan key:generate
php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\JWTAuthServiceProvider"
php artisan jwt:generate
```

### Step 3: Serve (with Apache)
```
Set up a Virtual Host
Set up an entry in hosts file
Start Apache
```

### Note: If NOT Using Apache
If you don't use Apache to serve this, you will need to remove the following 2 lines from your .htaccess:
```
RewriteCond %{HTTP:Authorization} ^(.*)
RewriteRule .* - [e=HTTP_AUTHORIZATION:%1]
```


