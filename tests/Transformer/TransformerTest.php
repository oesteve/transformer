<?php

namespace Oesteve\Tests\Transformer\Transformer;

use Oesteve\Tests\Transformer\Dto\UserDto;
use Oesteve\Tests\Transformer\Dto\UserHandler;
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
        $transformer = new Transformer(new InMemoryResolverLocator());


        $this->expectException(ResolverNotFoundException::class);
        $this->expectExceptionMessage("Resolver not found for class Oesteve\Tests\Transformer\Dto\UserDto");

        $transformer->transform(UserDto::class, 'my-user-key');
    }

    public function testTransform(): void
    {
        $locator = new InMemoryResolverLocator();
        $transformer = new Transformer($locator);

        $locator->set(new UserHandler());


        $user = $transformer->transform(UserDto::class, 'bob');

        self::assertNotNull($user);
        self::assertEquals(new UserDto('bob'), $user);
    }

    public function testTransformEmptyArray(): void
    {
        $locator = new InMemoryResolverLocator();
        $transformer = new Transformer($locator);
        $users = $transformer->transformMany(UserDto::class, []);

        self::assertCount(0,$users);
    }

    public function testInvalidHandlerResponse():void
    {
        $resolverLocator = new InMemoryResolverLocator();
        $resolverLocator->set(new EmptyResponseResolver());

        $transformer = new Transformer($resolverLocator);

        $this->expectException(InvalidResolverResponseException::class);
        $this->expectExceptionMessage("Missing key my-user-key in resolver return data");

        $transformer->transform(UserDto::class, 'my-user-key');

    }

}

class EmptyResponseResolver implements Resolver {

    public function resolve(array $keys): array
    {
        return [];
    }

    public static function supports(): string
    {
        return UserDto::class;
    }
}