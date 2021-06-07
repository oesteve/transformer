<?php


namespace Oesteve\Transformer\Symfony;

use Oesteve\Transformer\InMemory\InMemoryTransformer;
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
            SymfonyHandlerLocator::class,
            new Definition(
                SymfonyHandlerLocator::class,
                [$serviceLocator]
            )
        );

        $transformerDefinition = new Definition(
            InMemoryTransformer::class,
            [
                new Reference(SymfonyHandlerLocator::class)
            ]);

        $container->setDefinition(
            InMemoryTransformer::class,
            $transformerDefinition
        );
    }
}