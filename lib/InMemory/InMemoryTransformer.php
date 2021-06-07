<?php

namespace Oesteve\Transformer\InMemory;

use Oesteve\Transformer\ResolverLocator;
use Oesteve\Transformer\Transformer as TransformerAlias;

class InMemoryTransformer implements TransformerAlias
{

    private ResolverLocator $resolverLocator;

    public function __construct(ResolverLocator $resolverLocator)
    {
        $this->resolverLocator = $resolverLocator;
    }

    public function transform(string $className, mixed $key)
    {
        return $this->transformMany($className, [$key])[0];
    }

    public function transformMany(string $className, array $keys): array
    {
        return $this->resolverLocator->locate($className)->resolve($keys);
    }
}