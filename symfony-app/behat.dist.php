<?php

use App\Tests\Behat\HomepageContext;
use App\Tests\Behat\MessageProcessingTimeContext;
use App\Tests\Behat\WebExtensionContext;
use Behat\Config\Config;
use Behat\Config\Extension;
use Behat\Config\Profile;
use Behat\Config\Suite;
use FriendsOfBehat\SymfonyExtension\ServiceContainer\SymfonyExtension;

return (new Config())
    ->withProfile((new Profile('default'))
        ->withExtension(new Extension(SymfonyExtension::class, [
            'bootstrap' => 'tests/Behat/bootstrap.php',
        ]))
        ->withSuite((new Suite('default'))
            ->withContexts(
                HomepageContext::class,
                WebExtensionContext::class,
                MessageProcessingTimeContext::class
            )));
