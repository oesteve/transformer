<?php

namespace Oesteve\Tests\Transformer\Middleware\CacheMiddleware;

use Oesteve\Tests\Transformer\Dto\UserDto;
use Oesteve\Transformer\Middleware\CacheMiddleware\CacheKey;
use PHPUnit\Framework\TestCase;

class CacheKeyTest extends TestCase
{
    public function testToKey(): void
    {
        $cacheKey = CacheKey::for(UserDto::class);
        $this->assertEquals('Oesteve\Tests\Transformer\Dto\UserDto_bob', $cacheKey->key('bob'));
    }

    public function testToKeys(): void
    {
        $cacheKey = CacheKey::for(UserDto::class);
        $this->assertEquals(
            [
                'Oesteve\Tests\Transformer\Dto\UserDto_bob',
                'Oesteve\Tests\Transformer\Dto\UserDto_alice'
            ],
            $cacheKey->keys(['bob','alice'])
        );
    }

    public function testFromKeys(): void
    {
        $cacheKey = CacheKey::for(UserDto::class);
        $this->assertEquals(
            'alice',
            $cacheKey->fromKey('Oesteve\Tests\Transformer\Dto\UserDto_alice')
        );
    }
}
