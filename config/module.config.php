<?php
/**
 * Module configuration
 * @author Andreas Rutz <andreas.rutz@posteo.de>
 * @license MIT
 */
namespace ShiftpiMetascanApi;

return [
    'service_manager' => [
        'invokables' => [
            Entity\Result::class => Entity\Result::class,
            Entity\Progress::class => Entity\Progress::class,
        ],
        'factories' => [
            Service\Scan::class => Service\ScanFactory::class,
            __NAMESPACE__ . '\Http\ApiRequest' => Http\ApiRequestFactory::class,
            __NAMESPACE__ . '\Http\ScanRequest' => Http\ScanRequestFactory::class,
        ],
        'shared' => [
            Entity\Result::class => false,
            Entity\Progress::class => false,
        ],
    ],
    'metascan' => [
        'data_url' => 'https://scan.metascan-online.com/v2/file',
        'key' => '' // your key
    ],
];