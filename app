#!/usr/bin/env php
<?php

use Battleship\Application;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

require __DIR__.'/vendor/autoload.php';

$container = new ContainerBuilder();
$loader = new YamlFileLoader($container, new FileLocator());
$loader->load(__DIR__.'/config/services.yml');

$container->compile();

exit($container->get(Application::class)->run());