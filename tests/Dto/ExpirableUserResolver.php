<?php

namespace Oesteve\Tests\Transformer\Dto;

use Oesteve\Transformer\Collection;
use Oesteve\Transformer\Item;
use Oesteve\Transformer\Resolver;

class ExpirableUserResolver implements Resolver
{
    /** @var array<Collection<UserDto>> */
    public array $calls = [];

    public function resolve(Collection $items): void
    {
        $this->calls[] = $items;

        $items->forEach(function (Item $item, string $key) {
            $item->set(new UserDto($key));
            $item->expiresAt((new \DateTime())->add(new \DateInterval('P1D')));
        });
    }

    public static function supports(): string
    {
        return UserDto::class;
    }
}
