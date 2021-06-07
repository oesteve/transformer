<?php

namespace Oesteve\Tests\Transformer\Symfony;

use Oesteve\Tests\Transformer\Dto\UserHandler;
use Oesteve\Transformer\InMemory\InMemoryTransformer;
use Oesteve\Transformer\Symfony\ResolverLocatorCompilerPass;
use Oesteve\Transformer\Symfony\SymfonyHandlerLocator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class ResolverLocatorCompilerPassTest extends TestCase
{

    public function testProcess(): void
    {
        $container = new ContainerBuilder();

        $service = new Definition(UserHandler::class);
        $service->addTag(ResolverLocatorCompilerPass::RESOLVER_TAG);

        $container->addDefinitions([ UserHandler::class => $service]);

        $pass = new ResolverLocatorCompilerPass();
        $pass->process($container);

        self::assertTrue($container->has(SymfonyHandlerLocator::class));
        $definition = $container->get(SymfonyHandlerLocator::class);

        $serviceLocatorReference = $definition->getArgument(0);
        $serviceLocatorDefinition = $container->getDefinition($serviceLocatorReference);

        self::assertCount(1, $serviceLocatorDefinition->getArguments() );

    }
    public function testTransformerDefinition(): void
    {
        $container = new ContainerBuilder();

        $pass = new ResolverLocatorCompilerPass();
        $pass->process($container);

        self::assertTrue($container->has(InMemoryTransformer::class));
    }
}
