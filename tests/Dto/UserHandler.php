<?php


namespace Oesteve\Tests\Transformer\Dto;


use Oesteve\Transformer\Resolver;

class UserHandler implements Resolver
{

    public function resolve(array $keys): array
    {
        $data = [];
        foreach ($keys as $key){
            $data[$key] = new UserDto($key);
        }

        return $data;
    }

    public static function supports(): string
    {
        return UserDto::class;
    }
}