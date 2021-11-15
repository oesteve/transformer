<?php

namespace Oesteve\Transformer\ResolverLocator;

use Oesteve\Transformer\ResolverLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Oesteve\Transformer\Resolver;

class SymfonyResolverLocator implements ResolverLocator
{
    private ServiceLocator $serviceLocator;

    public function __construct(ServiceLocator $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    public function locate(string $dtoClassName): Resolver
    {
        if (!$this->serviceLocator->has($dtoClassName)) {
            throw new ResolverNotFoundException("Unable to find resolver for $dtoClassName in ServiceLocator");
        }

        return $this->serviceLocator->get($dtoClassName);
    }
}
