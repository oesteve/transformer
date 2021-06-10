<?php

namespace Oesteve\Tests\Transformer\Symfony;

use Oesteve\Tests\Transformer\Dto\UserHandler;
use Oesteve\Transformer\ResolverLocator\SymfonyResolverLocator;
use Oesteve\Transformer\Transformer;
use Oesteve\Transformer\Symfony\ResolverLocatorCompilerPass;
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

        self::assertTrue($container->has(SymfonyResolverLocator::class));
        $definition = $container->getDefinition(SymfonyResolverLocator::class);

        $serviceLocatorReference = $definition->getArgument(0);
        $serviceLocatorDefinition = $container->getDefinition($serviceLocatorReference);

        self::assertCount(1, $serviceLocatorDefinition->getArguments() );

    }
    public function testTransformerDefinition(): void
    {
        $container = new ContainerBuilder();

        $pass = new ResolverLocatorCompilerPass();
        $pass->process($container);

        self::assertTrue($container->has(Transformer::class));
    }
}
