<?php

namespace Oesteve\Tests\Transformer\Middleware\CacheMiddleware;

use Oesteve\Tests\Transformer\Dto\ExpirableUserResolver;
use Oesteve\Tests\Transformer\Dto\UserDto;
use Oesteve\Tests\Transformer\Dto\UserResolver;
use Oesteve\Tests\Transformer\Logger\TraceableLogger;
use Oesteve\Transformer\Middleware\CacheMiddleware\CacheKey;
use Oesteve\Transformer\Middleware\CacheMiddleware\CacheMiddleware;
use Oesteve\Transformer\Middleware\ResolverMiddleware;
use Oesteve\Transformer\ResolverLocator\InMemoryResolverLocator;
use Oesteve\Transformer\ResolverLocator\ResolverNotFoundException;
use Oesteve\Transformer\Transformer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

class CacheMiddlewareTest extends TestCase
{
    public function testSetCacheOnMiss(): void
    {
        $cache = new ArrayAdapter();
        $cacheMiddleware = new CacheMiddleware($cache);

        $locator = new InMemoryResolverLocator();
        $locator->set(new UserResolver());

        $transformer = new Transformer(
            $cacheMiddleware,
            new ResolverMiddleware($locator)
        );

        $res = $transformer->transform(UserDto::class, 'Bob');

        self::assertEquals(new UserDto('Bob'), $res);

        $cacheItem = $cache->getItem('Oesteve\Tests\Transformer\Dto\UserDto_Bob');
        self::assertEquals(new UserDto('Bob'), $cacheItem->get());
    }

    public function testRetrieveFromCached(): void
    {
        $cache = new ArrayAdapter();
        $cacheMiddleware = new CacheMiddleware($cache);

        $locator = new InMemoryResolverLocator();
        $userResolver = new UserResolver();
        $locator->set($userResolver);

        $transformer = new Transformer(
            $cacheMiddleware,
            new ResolverMiddleware($locator)
        );

        $transformer->transform(UserDto::class, 'Bob');
        $this->assertCount(1, $userResolver->calls);

        // The second call don't call to resolver
        $transformer->transform(UserDto::class, 'Bob');
        $this->assertCount(1, $userResolver->calls);
    }

    public function testLoggingMissAndHits(): void
    {
        $cache = new ArrayAdapter();
        $logger = new TraceableLogger();
        $cacheMiddleware = new CacheMiddleware($cache, $logger);

        $locator = new InMemoryResolverLocator();
        $userResolver = new UserResolver();
        $locator->set($userResolver);

        $transformer = new Transformer(
            $cacheMiddleware,
            new ResolverMiddleware($locator)
        );

        $transformer->transform(UserDto::class, 'Bob');
        $this->assertEquals([
            'debug' => [[
                'Miss from cache',
                [ 'keys' => ['Bob']]
            ]]
        ], $logger->entries);
        $logger->clear();


        // The second call don't call to resolver
        $transformer->transform(UserDto::class, 'Bob');
        $this->assertCount(1, $userResolver->calls);
        $this->assertEquals([
            'debug' => [[
                'Hit from cache',
                [ 'keys' => ['Bob']]
            ]]
        ], $logger->entries);
    }

    public function testKeyCollisions(): void
    {
        $cache = new ArrayAdapter();
        $logger = new TraceableLogger();
        $cacheMiddleware = new CacheMiddleware($cache, $logger);

        $locator = new InMemoryResolverLocator();
        $userResolver = new UserResolver();
        $locator->set($userResolver);

        $transformer = new Transformer(
            $cacheMiddleware,
            new ResolverMiddleware($locator)
        );

        $transformer->transform(UserDto::class, 'Bob');

        $this->expectException(ResolverNotFoundException::class);
        $transformer->transform('Another\Class', 'Bob');
    }

    public function testExpirableKey(): void
    {
        $cache = new ArrayAdapter();
        $logger = new TraceableLogger();
        $cacheMiddleware = new CacheMiddleware($cache, $logger);

        $locator = new InMemoryResolverLocator();
        $userResolver = new ExpirableUserResolver();
        $locator->set($userResolver);

        $transformer = new Transformer(
            $cacheMiddleware,
            new ResolverMiddleware($locator)
        );

        $transformer->transform(UserDto::class, 'Bob');
    }
}
