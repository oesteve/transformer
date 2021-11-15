<?php


namespace Oesteve\Transformer\Middleware;

use Oesteve\Transformer\Collection;
use Oesteve\Transformer\ResolverLocator;

class ResolverMiddleware implements Middleware
{
    private ResolverLocator $resolverLocator;

    public function __construct(ResolverLocator $resolverLocator)
    {
        $this->resolverLocator = $resolverLocator;
    }

    public function next(Collection $collection, callable $next): void
    {
        $this->resolverLocator->locate($collection->getClassName())
            ->resolve($collection);
    }
}
