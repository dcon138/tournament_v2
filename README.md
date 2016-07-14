## vue-starter Backend API (Laravel-based)

This application will serve as the companion app to another project called vue-starter. It is meant to be a small demo of a Laravel API, using Dingo and JWT for authentication.

[vue-starter Frontend App](https://github.com/layer7be/vue-starter)

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


