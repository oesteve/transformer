<?php

namespace Oesteve\Tests\Transformer\Transformer;

use Oesteve\Tests\Transformer\Dto\UserDto;
use Oesteve\Tests\Transformer\Dto\UserResolver;
use Oesteve\Transformer\Collection;
use Oesteve\Transformer\Middleware\LoggerMiddleware;
use Oesteve\Transformer\Middleware\ResolverMiddleware;
use Oesteve\Transformer\Resolver\InvalidResolverResponseException;
use Oesteve\Transformer\Resolver;
use Oesteve\Transformer\ResolverLocator\InMemoryResolverLocator;
use Oesteve\Transformer\ResolverLocator\ResolverNotFoundException;
use Oesteve\Transformer\Transformer;
use PHPUnit\Framework\TestCase;

class TransformerTest extends TestCase
{
    public function testHandlerNotFoundException(): void
    {
        $locator = new InMemoryResolverLocator();

        $transformer = new Transformer(
            new ResolverMiddleware($locator)
        );

        $this->expectException(ResolverNotFoundException::class);
        $this->expectExceptionMessage("Resolver not found for class Oesteve\Tests\Transformer\Dto\UserDto");

        $transformer->transform(UserDto::class, 'my-user-key');
    }

    public function testTransform(): void
    {
        $locator = new InMemoryResolverLocator();

        $transformer = new Transformer(
            new ResolverMiddleware($locator)
        );

        $locator->set(new UserResolver());

        $user = $transformer->transform(UserDto::class, 'bob');

        self::assertNotNull($user);
        self::assertEquals(new UserDto('bob'), $user);
    }

    public function testTransformEmptyArray(): void
    {
        $locator = new InMemoryResolverLocator();

        $transformer = new Transformer(
            new ResolverMiddleware($locator)
        );

        $users = $transformer->transformMany(UserDto::class, []);

        self::assertCount(0, $users);
    }

    public function testInvalidHandlerResponse(): void
    {
        $locator = new InMemoryResolverLocator();
        $locator->set(new EmptyResponseResolver());

        $transformer = new Transformer(
            new ResolverMiddleware($locator)
        );

        $this->expectException(InvalidResolverResponseException::class);
        $this->expectExceptionMessage("Missing key my-user-key in resolver return data");

        $transformer->transform(UserDto::class, 'my-user-key');
    }
}

class EmptyResponseResolver implements Resolver
{
    public function resolve(Collection $items): void
    {
    }

    public static function supports(): string
    {
        return UserDto::class;
    }
}
