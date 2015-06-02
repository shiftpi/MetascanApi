<?php
$autoloadPaths = [
    __DIR__ . '/../../../vendor/autoload.php',  // If the module is installed under /module
    __DIR__ . '/../../../autoload.php',         // or under /vendor
    __DIR__ . '/../vendor/autoload.php',        // or as a separate project
];

foreach ($autoloadPaths as $autoloadPath) {
    if (file_exists($autoloadPath)) {
        require $autoloadPath;
        break;
    }
}

$loader = new \Zend\Loader\StandardAutoloader([
    \Zend\Loader\StandardAutoloader::LOAD_NS => [
        'ShiftpiMetascanApi' => __DIR__ . '/../src/ShiftpiMetascanApi',
    ],
]);

$config = require __DIR__ . '/config/config.local.php';
define('APIKEY', $config['apikey']);

$loader->register();