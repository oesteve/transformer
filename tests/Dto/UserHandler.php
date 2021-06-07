<?php


namespace Oesteve\Tests\Transformer\Dto;

use Oesteve\Transformer\Resolver;


class UserHandler implements Resolver
{

    public function resolve(array $keys): array
    {
        return array_map(
            fn(string $key) => new UserDto($key),
            $keys
        );
    }

    public static function supports(): string
    {
        return UserDto::class;
    }
}