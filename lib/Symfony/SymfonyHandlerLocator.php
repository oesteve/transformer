<?php


namespace Oesteve\Transformer\Symfony;


use Oesteve\Transformer\Resolver;
use Oesteve\Transformer\ResolverLocator;
use Oesteve\Transformer\ResolverNotFoundException;
use Symfony\Component\DependencyInjection\ServiceLocator;

class SymfonyHandlerLocator implements ResolverLocator
{
    private ServiceLocator $serviceLocator;

    public function __construct(ServiceLocator $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    public function locate(string $dtoClassName): Resolver
    {
        if(!$this->serviceLocator->has($dtoClassName)){
            throw new ResolverNotFoundException("Unable to find key $dtoClassName in ServiceLocator");
        }

        return $this->serviceLocator->get($dtoClassName);
    }
}