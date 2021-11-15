<?php

namespace Oesteve\Tests\Transformer\ResolverLocator;

use Oesteve\Tests\Transformer\Dto\UserDto;
use Oesteve\Tests\Transformer\Dto\UserResolver;
use Oesteve\Transformer\Middleware\ResolverMiddleware;
use Oesteve\Transformer\ResolverLocator\ResolverNotFoundException;
use Oesteve\Transformer\ResolverLocator\SymfonyResolverLocator;
use Oesteve\Transformer\Transformer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ServiceLocator;

class SymfonyResolverLocatorTest extends TestCase
{
    public function testHandlerNotFoundError(): void
    {
        $locator = new SymfonyResolverLocator(new ServiceLocator([]));
        $transformer = new Transformer(
            new ResolverMiddleware($locator)
        );

        $this->expectException(ResolverNotFoundException::class);
        $this->expectExceptionMessage("Unable to find resolver for Oesteve\Tests\Transformer\Dto\UserDto in ServiceLocator");

        $transformer->transform(UserDto::class, 'my-user');
    }

    public function testHandlerLocator(): void
    {
        $factories = [
            'Oesteve\Tests\Transformer\Dto\UserDto' => fn () => new UserResolver()
        ];
        $locator = new SymfonyResolverLocator(new ServiceLocator($factories));
        $transformer = new Transformer(
            new ResolverMiddleware($locator)
        );

        $dto = $transformer->transform(UserDto::class, 'my-user');
        self::assertEquals(new UserDto('my-user'), $dto);
    }
}
