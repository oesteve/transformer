<?php

namespace Oesteve\Transformer;

/**
 * @template T
 */
interface Resolver
{
    /**
     * @param Collection<T> $items
     */
    public function resolve(Collection $items): void;

    /**
     * @return class-string<T>
     */
    public static function supports(): string;
}
