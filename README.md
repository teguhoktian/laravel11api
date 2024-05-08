# Laravel 11 API with Breeze and Sanctum

Starterpack for make application based on Laravel 11 and API Breeze Package

### Stack & Package

-   Laravel 11.7.0
-   Laravel Breeze (API)
-   [Laravel Permission v6.x](https://spatie.be/docs/laravel-permission/v6/introduction)
-   [Laravel Backup v8.x](https://spatie.be/docs/laravel-backup/v8/introduction)
-   [Laravel Setting](https://github.com/spatie/laravel-settings)
-   [Laravel Log Activity v4.x](https://spatie.be/docs/laravel-activitylog/v4/introduction)

### Usage

-   Clone repository
-   Clone `.env` file from `.env.example`
-   Setting `.env` variable with your local/production setup
-   Add location of dump binary mysql database to `MYSQL_DUMP_PATH` variable on `.env` file.
-   Update Composer

```bash
composer update
```

-   Generate Key

```bash
php artisan key:generate
```

-   Migrate database structure

```bash
php artisan migrate
```

-   Add Dummy Data of user

```bash
php artisan db:seed
```

-   Storage Link

```bash
php artisan storage:link
```

-   Run localhost

```bash
php artisan serve
```

-   Run Queue

```bash
php artisan queue:work
```

Visit Localhost (http://localhost:8000/)

### API Documentation

Visit Postman Docs (https://documenter.getpostman.com/view/1487227/2sA3JKchQc)

### Licensing

-   Copyright 2023 [Raincode.My.Id](https://raincode.my.id)
-   Licensed under **MIT**

### Donation

Buy Me some Cofee and Snack with Donate me at [Saweria](https://saweria.co/raincodemyid)
