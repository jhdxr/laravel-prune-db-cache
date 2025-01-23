[![Latest Stable Version](https://poser.pugx.org/jhdxr/laravel-prune-db-cache/v/stable.svg)](https://packagist.org/packages/jhdxr/laravel-prune-db-cache)
[![License](https://poser.pugx.org/jhdxr/laravel-prune-db-cache/license.svg)](https://packagist.org/packages/jhdxr/laravel-prune-db-cache) 


# Prune Expired Cache For Laravel DB Driver

The built-in `database` cache driver in Laravel does not have a built-in way to delete expired cache entries automatically, nor a quick way to clear the expired items only. This package helps you to handle this issue.

This package adds one artisan command: `php artisan cache:db-prune-expired` and it will delete all expired records from the cache table.


## Installation

You can install the package via composer:

```bash
composer require jhdxr/laravel-prune-db-cache
```


## Usage

Run the following command
```bash
php artisan cache:db-prune-expired
```


### Run on schedule
It's very easy to use [Laravel's built-in scheduler](https://laravel.com/docs/11.x/scheduling) to run the command automatically. For example, if you want to run the command every day at 4 AM, you can add the following to your `app/Console/Kernel.php` file:
```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('cache:db-prune-expired')->dailyAt('04:00');
}
```


## License

This package is open-sourced software licensed under the [MIT license](LICENSE).
