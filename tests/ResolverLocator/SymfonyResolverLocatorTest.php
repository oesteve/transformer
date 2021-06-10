<?php

namespace Oesteve\Tests\Transformer\ResolverLocator;

use Oesteve\Tests\Transformer\Dto\UserDto;
use Oesteve\Tests\Transformer\Dto\UserHandler;
use Oesteve\Transformer\ResolverLocator\ResolverNotFoundException;
use Oesteve\Transformer\ResolverLocator\SymfonyResolverLocator;
use Oesteve\Transformer\Transformer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ServiceLocator;

class SymfonyResolverLocatorTest extends TestCase
{

    public function testHandlerNotFoundError():void
    {
        $locator = new SymfonyResolverLocator(new ServiceLocator([]));
        $transformer = new Transformer($locator);

        $this->expectException(ResolverNotFoundException::class);
        $this->expectExceptionMessage("Unable to find resolver for Oesteve\Tests\Transformer\Dto\UserDto in ServiceLocator");

        $transformer->transform(UserDto::class, 'my-user');
    }

    public function testHandlerLocator():void
    {
        $factories = [
            'Oesteve\Tests\Transformer\Dto\UserDto' => fn() => new UserHandler()
        ];
        $locator = new SymfonyResolverLocator(new ServiceLocator($factories));
        $transformer = new Transformer($locator);


        $dto = $transformer->transform(UserDto::class, 'my-user');

        self::assertEquals(new UserDto('my-user'), $dto);
    }
}
