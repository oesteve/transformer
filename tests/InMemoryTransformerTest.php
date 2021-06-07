<?php


namespace Oesteve\Tests\Transformer;



use PHPUnit\Framework\TestCase;
use Oesteve\Transformer\InMemory\InMemoryResolverLocator;
use Oesteve\Transformer\InMemory\InMemoryTransformer;
use Oesteve\Transformer\ResolverNotFoundException;

use Oesteve\Tests\Transformer\Dto\UserHandler;
use Oesteve\Tests\Transformer\Dto\UserDto;


class InMemoryTransformerTest extends TestCase
{

    public function testHandlerNotFoundException(): void
    {
        $transformer = new InMemoryTransformer(new InMemoryResolverLocator());


        $this->expectException(ResolverNotFoundException::class);
        $this->expectExceptionMessage("Resolver not found for class Oesteve\Tests\Transformer\Dto\UserDto");

        $transformer->transform(UserDto::class, 'my-user-key');
    }

    public function testTransform(): void
    {
        $locator = new InMemoryResolverLocator();
        $transformer = new InMemoryTransformer($locator);

        $locator->set(new UserHandler());


        $user = $transformer->transform(UserDto::class, 'bob');

        self::assertNotNull($user);
        self::assertEquals(new UserDto('bob'), $user);
    }

}
