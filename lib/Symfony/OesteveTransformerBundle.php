<?php

namespace Oesteve\Transformer\Symfony;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class OesteveTransformerBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new ResolverLocatorCompilerPass());
    }
}