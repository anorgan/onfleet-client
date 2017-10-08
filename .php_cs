<?php


use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->in([
        __DIR__ .'/src',
        __DIR__ .'/tests',
    ])
;

return Config::create()
    ->setUsingCache(true)
    ->setRules([
        '@PSR2'              => true,
        'array_syntax'       => ['syntax' => 'short'],
        'phpdoc_order'       => true,
    ])
    ->setFinder($finder)
;
