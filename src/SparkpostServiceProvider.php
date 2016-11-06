<?php

namespace Clarification\MailDrivers\Sparkpost;

use GuzzleHttp\Client;
use Illuminate\Mail\TransportManager;
use Illuminate\Support\ServiceProvider;
use Illuminate\Mail\Transport\Transport as AbstractTransport;
use Clarification\MailDrivers\Sparkpost\Transport\SparkPostTransport;
use Clarification\MailDrivers\Sparkpost\Transport\SparkPostTransportFiveZero;
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
        if(class_exists(LaravelSparkPostTransport::class)) {
            return;
        }

        $this->app->extend('swift.transport', function(TransportManager $manager) {

            $manager->extend('sparkpost', function() {

                $config = $this->app['config']->get('services.sparkpost', []);
                $sparkpostOptions = isset($config['options']) ? $config['options'] : [];
                $guzzleOptions = isset($config['guzzle']) ? $config['guzzle'] : [];
                $client = new Client($guzzleOptions);

                if(class_exists(AbstractTransport::class)) {
                    return new SparkPostTransport($client, $config['secret'], $sparkpostOptions);
                }

                // Fallback to implementation which only depends on Swift_Transport
                return new SparkPostTransportFiveZero($client, $config['secret'], $sparkpostOptions);
            });

            return $manager;
        });
    }
}
