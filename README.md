(In development -> not stable yet)

AsyncServiceCallBundle
========================

This bundle allows you to execute methods of your services asynchronously in a background process

Installation
------------
Download using composer

    composer require krlove/async-service-call-bundle

Enable the bundle at `AppKernel`

    $bundles = [
       ...
       new Krlove\AsyncServiceCallBundle\KrloveAsyncServiceCallBundle(),
    ]
    
Configuration
-------------
Options:

- `console_path` - path to `console` script.
Can be absolute or relative to `kernel.root_dir` parameter's value.
Defaults to `app/console` for Symfony 2.* and `bin/console` for Symfony 3.*.
- `php_path` - path to php executable. If no option provided in configuration, `Symfony\Component\Process\PhpExecutableFinder::find` will be used to set it up.

Example:

    # config.yml
    krlove_async_service_call:
        console_path: bin/console
        php_path: /usr/local/bin/php

Usage
-----
Define any service

    <?php
        
    namespace AppBundle\Service;
        
    class AwesomeService
    {
        public function doSomething($int, $string, $array)
        {
            // do something heavy
            sleep(10)
        }
    }

Register service

    # services.yml
    services:
        app.service.awesome:
            class: AppBundle\Service\AwesomeService
            public: true

> make sure your service is configured with `public: true`

Execute `doSomething` method asynchronously:

    $this->get('krlove.async')
        ->call('app.service.awesome', 'doSomething', [1, 'string', ['array']);

Line above will execute `AppBundle\Service\AwesomeService::doSomething` method by running `krlove:service:call` command in [asynchronous](https://stackoverflow.com/a/222445/1667170) `Symfony\Component\Process\Process`.

Process PID will be returned on success, `null` on failure.