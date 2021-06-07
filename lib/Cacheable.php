<?php
namespace Oesteve\Transformer;

/**
 * @template T
 * @template-extends Resolver<T>
 */
interface Cacheable extends Resolver
{
    public function getKey(mixed $dtoObject): string;

    public function getTtl(string $key): int;
}