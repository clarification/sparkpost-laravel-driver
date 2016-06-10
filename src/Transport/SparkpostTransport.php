<?php

namespace Clarification\MailDrivers\Sparkpost\Transport;

use Illuminate\Mail\Transport\Transport;


/**
 * This is almost a direct copy past of the driver included in Laravel 5.2.23+
 * https://github.com/laravel/framework/blob/5.2/src/Illuminate/Mail/Transport/SparkPostTransport.php
 *
 * You should only need this package if you are using laravel 5.0.0 to 5.2.22
 */
class SparkPostTransport extends Transport
{
    use SparkPostTransportTrait;
}
