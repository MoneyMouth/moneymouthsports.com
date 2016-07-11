<?php

use Symfony\Component\Debug\Debug;
use Symfony\Component\Form\Exception\RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Yaml;

/**
 * @var Composer\Autoload\ClassLoader
 */
$loader = require __DIR__.'/../app/autoload.php';

$configYaml = Yaml::parse(file_get_contents(__DIR__ . "/../app/config/parameters.yml"));

$environment = $configYaml['parameters']['environment'];
if($environment === 'prod') {
    include_once __DIR__.'/../var/bootstrap.php.cache';
} elseif($environment === 'dev') {
    Debug::enable();
} else {
    throw new RuntimeException('Environment is not set');
}

$kernel = new AppKernel($environment, $environment === 'dev');
$kernel->loadClassCache();
//$kernel = new AppCache($kernel);
// When using the HttpCache, you need to call the method in your front controller instead of relying on the configuration parameter
//Request::enableHttpMethodParameterOverride();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
