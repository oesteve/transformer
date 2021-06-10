<?php

namespace Oesteve\Tests\Transformer\Symfony;

use Oesteve\Transformer\Symfony\OesteveTransformerBundle;
use Oesteve\Transformer\Symfony\ResolverLocatorCompilerPass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class OesteveTransformerBundleTest extends TestCase
{
    public function testCompilerPassAdded(): void
    {

        $container = new ContainerBuilder();
        $bundle = new OesteveTransformerBundle();

        $bundle->build($container);

        $compilerPasses = $container->getCompilerPassConfig()->getPasses();
        $compilerPassClasses = array_map(fn(mixed $class) => $class::class, $compilerPasses);
        self::assertContains(ResolverLocatorCompilerPass::class, $compilerPassClasses);

    }

}
