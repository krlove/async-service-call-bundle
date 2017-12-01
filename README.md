(In development -> not stable yet)

AsyncServiceCallBundle
========================

This bundle allows you to execute methods of your services asynchronously in a background process

Installation
------------
Download using composer

`composer require krlove/async-service-call-bundle`

Enable the bundle at `AppKernel`

    $bundles = [
       ...
       new Krlove\AsyncServiceCallBundle\KrloveAsyncServiceCallBundle(),
    ]

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

This will start new `Symfony\Component\Process\Process` which executes `krlove:service:call` command which invokes `AppBundle\Service\AwesomeService
::doSomething` method with supplied arguments asynchronously in a background (refer this [Stackoverflow question](https://stackoverflow.com/a/222445/1667170) for more information).