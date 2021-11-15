<?php

namespace Oesteve\Transformer\Symfony;

use Oesteve\Transformer\Middleware\ResolverMiddleware;
use Oesteve\Transformer\ResolverLocator\SymfonyResolverLocator;
use Oesteve\Transformer\Transformer;
use Oesteve\Transformer\Resolver;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ResolverLocatorCompilerPass implements CompilerPassInterface
{
    public const RESOLVER_TAG = 'transformer.resolver';

    public function process(ContainerBuilder $container): void
    {
        $this->defineResolverLocator($container);
        $this->defineResolverMiddleware($container);
        $this->defineTransformer($container);
    }

    private function defineTransformer(ContainerBuilder $container): void
    {
        $transformerDefinition = new Definition(
            Transformer::class,
            [
                new Reference(ResolverMiddleware::class)
            ]
        );

        $transformerDefinition->setPublic(true);

        $container->setDefinition(
            Transformer::class,
            $transformerDefinition
        );
    }

    /**
     * @param ContainerBuilder $container
     */
    private function defineResolverLocator(ContainerBuilder $container): void
    {
        $resolverMap = [];

        /**
         * @var class-string<Resolver<mixed>> $id
         * @var array<string,mixed> $attributes
         */
        foreach ($container->findTaggedServiceIds(self::RESOLVER_TAG) as $id => $attributes) {
            $dtoClassName = $id::supports();
            $resolverMap[$dtoClassName] = new Reference($id);
        }

        $serviceLocator = ServiceLocatorTagPass::register($container, $resolverMap);

        $container->setDefinition(
            SymfonyResolverLocator::class,
            new Definition(
                SymfonyResolverLocator::class,
                [$serviceLocator]
            )
        );
    }

    private function defineResolverMiddleware(ContainerBuilder $container): void
    {
        $resolverLocator = new Reference(SymfonyResolverLocator::class);
        $container->setDefinition(
            ResolverMiddleware::class,
            new Definition(
                ResolverMiddleware::class,
                [$resolverLocator]
            )
        );
    }
}
