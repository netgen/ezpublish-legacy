<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;
use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/autoload',
        __DIR__ . '/benchmarks',
        __DIR__ . '/cronjobs',
        __DIR__ . '/extension',
        __DIR__ . '/kernel',
        __DIR__ . '/lib',
        __DIR__ . '/tests',
        __DIR__ . '/update',
    ]);

    // register a single rule
    $rectorConfig->rule(InlineConstructorDefaultToPropertyRector::class);

    // define sets of rules
       $rectorConfig->sets([
           LevelSetList::UP_TO_PHP_82
       ]);
};
