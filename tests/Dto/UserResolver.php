<?php

namespace Oesteve\Tests\Transformer\Dto;

use Oesteve\Transformer\Collection;
use Oesteve\Transformer\Item;
use Oesteve\Transformer\Resolver;

class UserResolver implements Resolver
{
    /** @var array<Collection<UserDto>> */
    public array $calls = [];

    public function resolve(Collection $items): void
    {
        $this->calls[] = $items;

        $items->forEach(function (Item $item, string $key) {
            $item->set(new UserDto($key));
        });
    }

    public static function supports(): string
    {
        return UserDto::class;
    }
}
