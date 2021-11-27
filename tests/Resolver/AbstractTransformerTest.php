<?php

namespace Oesteve\Tests\Transformer\Resolver;

use Oesteve\Tests\Transformer\Dto\UserDto;
use Oesteve\Transformer\Resolver\AbstractTransformer;
use Oesteve\Transformer\Resolver\InvalidTransformerException;
use Oesteve\Transformer\ResolverLocator\InMemoryResolverLocator;
use Oesteve\Transformer\Transformer;
use PHPUnit\Framework\TestCase;

class AbstractTransformerTest extends TestCase
{
    public function testUnImplementedTransformer(): void
    {
        $resolverLocator = new InMemoryResolverLocator();
        $resolverLocator->set(new UnImplementedTransformer());
        $transformer = new Transformer($resolverLocator);

        $this->expectException(InvalidTransformerException::class);
        $this->expectExceptionMessage("Resolve method not implemented in Oesteve\Tests\Transformer\Resolver\UnImplementedTransformer");
        $transformer->transform(UserDto::class, 'bob');
    }

    public function testTransformMany(): void
    {
        $resolverLocator = new InMemoryResolverLocator();
        $resolverLocator->set(new ManyTransformer());
        $transformer = new Transformer($resolverLocator);

        $user = $transformer->transform(UserDto::class, 'bob');
        $this->assertEquals('bob', $user->name);
    }

    public function testTransformOne(): void
    {
        $resolverLocator = new InMemoryResolverLocator();
        $resolverLocator->set(new SingleTransformer());
        $transformer = new Transformer($resolverLocator);

        $user = $transformer->transform(UserDto::class, 'bob');
        $this->assertEquals('bob', $user->name);
    }
}

class UnImplementedTransformer extends AbstractTransformer
{
    public static function supports(): string
    {
        return UserDto::class;
    }
}

class ManyTransformer extends AbstractTransformer
{
    protected function transformMany(array $keys): array
    {
        $key = $keys[0];

        return [$key => new UserDto($key)];
    }

    public static function supports(): string
    {
        return UserDto::class;
    }
}
class SingleTransformer extends AbstractTransformer
{
    protected function transform(string $key): UserDto
    {
        return new UserDto($key);
    }

    public static function supports(): string
    {
        return UserDto::class;
    }
}
