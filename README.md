### Install

Require this package with composer using the following command:
```bash
composer require clarification/sparkpost-laravel-driver
```

After updating composer, add the service provider to the `providers` array in `config/app.php`
```php
Clarification\MailDrivers\Sparkpost\SparkpostServiceProvider::class,
```

You will also need to add the sparkpost API Key settings to the array in `config/services.php` and set up the environment key
```php
'sparkpost' => [
    'secret' => env('SPARKPOST_SECRET'),
],
```
```bash
SPARKPOST_SECRET=__Your_key_here__
```

Finally you need to set your mail driver to `sparkpost`. You can do this by changing the driver in `config/mail.php`
```php
'driver' => env('MAIL_DRIVER', 'sparkpost'),
```

Or by setting the environment variable `MAIL_DRIVER` in your .env file
```bash
MAIL_DRIVER=sparkpost
```

If you need to pass any options to the guzzle client instance which is making the request to the sparkpost API, you can do so by setting the 'guzzle' options in `config/services.php`. Also you can provide options to pass to the SparkPost API by setting the 'options' array.
```php
'sparkpost' => [
    'secret' => env('SPARKPOST_SECRET'),
    'guzzle' => [
        'verify' => true,
        'decode_content' => true,
    ],
    'options' => [
        'open_tracking' => false,
        'click_tracking' => false,
        'transactional' => true,
    ],
],
```

**This is only needed if you are using Laravel 5.1.* or older, the sparkpost driver is included in 5.2**
If the Laravel Sparkpost driver is present (you are running >=5.2), The service provider will not load this packages Sparkpost driver.
