<?php


namespace Oesteve\Transformer\Middleware\CacheMiddleware;


use Psr\Cache\CacheItemPoolInterface;

interface CachePoolFactory
{
    public function buildFor(string $className): CacheItemPoolInterface;
}
