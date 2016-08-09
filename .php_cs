<?php

use Symfony\CS\FixerInterface;

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->in([
        __DIR__ .'/src',
    ])
;

return Symfony\CS\Config\Config::create()
    ->setUsingCache(true)
    ->level(FixerInterface::PSR2_LEVEL)
    ->fixers([
        'phpdoc_order',
        'align_equals',
        'align_double_arrow',
        'short_array_syntax'
    ])
    ->finder($finder)
;
