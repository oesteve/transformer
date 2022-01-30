<?php

namespace Oesteve\Transformer;

/**
 * @template T
 */
interface Resolver
{
    /**
     * @param array<string> $keys
     *
     * @return array<string,T|\Closure>
     */
    public function resolve(array $keys): array;

    /**
     * @return class-string<T>
     */
    public static function supports(): string;
}
