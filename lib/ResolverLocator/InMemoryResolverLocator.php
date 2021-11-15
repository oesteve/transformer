<?php

namespace Oesteve\Transformer\ResolverLocator;

use Oesteve\Transformer\Resolver;
use Oesteve\Transformer\ResolverLocator;

class InMemoryResolverLocator implements ResolverLocator
{
    /** @var array<string,Resolver<mixed>> */
    private array $handlers = [];

    /**
     * @param Resolver<mixed> $resolver
     */
    public function set(Resolver $resolver): void
    {
        $this->handlers[$resolver::supports()] = $resolver;
    }

    public function locate(string $dtoClassName): Resolver
    {
        if (!isset($this->handlers[$dtoClassName])) {
            throw new ResolverNotFoundException("Resolver not found for class ".$dtoClassName);
        }

        return $this->handlers[$dtoClassName];
    }
}
