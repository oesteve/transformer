<?php

namespace Oesteve\Tests\Transformer\Middleware;

use Oesteve\Tests\Transformer\Dto\UserDto;
use Oesteve\Tests\Transformer\Dto\UserResolver;
use Oesteve\Tests\Transformer\Logger\TraceableLogger;
use Oesteve\Transformer\Middleware\LoggerMiddleware;
use Oesteve\Transformer\Middleware\ResolverMiddleware;
use Oesteve\Transformer\ResolverLocator\InMemoryResolverLocator;
use Oesteve\Transformer\Transformer;
use PHPUnit\Framework\TestCase;

class LoggerMiddlewareTest extends TestCase
{
    public function testLogMessages(): void
    {
        $locator = new InMemoryResolverLocator();
        $locator->set(new UserResolver());

        $logger = new TraceableLogger();

        $transformer = new Transformer(
            new LoggerMiddleware($logger),
            new ResolverMiddleware($locator)
        );

        $transformer->transform(UserDto::class, 'Bob');

        $expected = [
            'debug' => [[
                'Transform', [
                    'class' => 'Oesteve\\Tests\\Transformer\\Dto\\UserDto',
                    'keys' => ['Bob'],
                ],
            ]],
        ];
        $entries = $logger->entries;
        $this->assertEquals($expected, $entries);
    }
}
