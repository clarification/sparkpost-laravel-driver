<?php

namespace Clarification\MailDrivers\Sparkpost;

use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use Illuminate\Mail\TransportManager;
use Illuminate\Support\ServiceProvider;
use Clarification\MailDrivers\Sparkpost\Transport\SparkpostTransport;
use Illuminate\Mail\Transport\SparkPostTransport as LaravelSparkPostTransport;

class SparkpostServiceProvider extends ServiceProvider
{
    /**
     * After register is called on all service providers, then boot is called
     */
    public function boot()
    {
        //
    }

    /**
     * Register is called on all service providers first.
     *
     * We must register the extension before anything tries to use the mailing functionality.
     * None of the closures are executed until someone tries to send an email.
     *
     * This will register a closure which will be run when 'swift.transport' (the transport manager) is first resolved.
     * Then we extend the transport manager, by adding the spark post transport object as the 'sparkpost' driver.
     */
    public function register()
    {
        // Don't need to register our driver if the current laravel install already has the spark post transport
        if(class_exists(LaravelSparkPostTransport::class, false)) {
            return;
        }

        $this->app->extend('swift.transport', function(TransportManager $manager) {
            $manager->extend('sparkpost', function() {
                $config = $this->app['config']->get('services.sparkpost', []);
                $client = new Client(Arr::get($config, 'guzzle', []));
                return new SparkpostTransport($client, $config['secret']);
            });
            return $manager;
        });
    }
}
