<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude('tools')
    ->exclude('vendor')
    ->in(__DIR__)
;

$config = new PhpCsFixer\Config();
return $config->setRules([
    '@PSR12' => true,
    'strict_param' => true,
    'array_syntax' => ['syntax' => 'short'],
])
    ->setFinder($finder)
    ;
