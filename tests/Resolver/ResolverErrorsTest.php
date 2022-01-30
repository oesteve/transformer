<?php

namespace Oesteve\Tests\Transformer\Resolver;

use Oesteve\Tests\Transformer\Dto\UserDto;
use Oesteve\Transformer\Resolver\AbstractTransformer;
use Oesteve\Transformer\Resolver\ResolverException;
use Oesteve\Transformer\ResolverLocator\InMemoryResolverLocator;
use Oesteve\Transformer\Transformer;
use PHPUnit\Framework\TestCase;

class ResolverErrorsTest extends TestCase
{
    public function testThrowException(): void
    {
        $transformer = $this->buildTransformer();

        $this->expectException(ResolverException::class);
        $transformer->transform(UserDto::class, '3');
    }

    public function testResults(): void
    {
        $transformer = $this->buildTransformer();

        try {
            $results = $transformer->transformMany(UserDto::class, [1, 2, 3]);
        } catch (ResolverException $resolverException) {
            $results = $resolverException->getValidResults();
        }

        $this->assertEquals(new UserDto('User 1'), $results[0]);
    }

    public function testExceptionValues(): void
    {
        $transformer = $this->buildTransformer();

        try {
            $transformer->transformMany(UserDto::class, [1, 2, 3]);
            $this->fail('Exception expected');
        } catch (ResolverException $resolverException) {
            $this->assertEquals('Invalid user id', $resolverException->getErrors()[0]->getMessage());
            $this->assertInstanceOf(MyException::class, $resolverException->getErrors()[0]);
            $this->assertCount(1, $resolverException->getErrors());
            $this->assertCount(2, $resolverException->getValidResults());
            $this->assertEquals(new UserDto('User 1'), $resolverException->getValidResults()[0]);
        }
    }

    /**
     * @return Transformer
     */
    private function buildTransformer(): Transformer
    {
        $resolverLocator = new InMemoryResolverLocator();
        $resolverLocator->set(new MyResolver());
        $transformer = new Transformer($resolverLocator);
        return $transformer;
    }
}

class MyException extends \Exception
{
}

class MyResolver extends AbstractTransformer
{
    public function transform(string $key): UserDto
    {
        if (0 === (int) $key % 3) {
            throw new MyException('Invalid user id');
        }

        return new UserDto('User '.$key);
    }

    public static function supports(): string
    {
        return UserDto::class;
    }
}
