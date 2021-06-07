<?php
namespace Oesteve\Transformer;

interface Transformer
{

    /**
     * @template T
     * @param class-string<T> $className
     * @param mixed $key
     *
     * @return T | null
     */
    public function transform(string $className, mixed $key);

    /**
     * @template T
     * @param class-string<T> $className
     * @param array<string> $keys
     *
     * @return array<T>
     */
    public function transformMany(string $className, array $keys): array;

}