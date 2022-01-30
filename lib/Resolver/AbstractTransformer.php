<?php

namespace Oesteve\Transformer\Resolver;

use Oesteve\Transformer\Resolver;

/**
 * @template T
 * @implements Resolver<T>
 */
abstract class AbstractTransformer implements Resolver
{
    /**
     * @return T
     */
    protected function transform(string $key): mixed
    {
        throw new InvalidTransformerException('Resolve method not implemented in '.static::class);
    }

    /**
     * @param array<string> $keys
     *
     * @return array<string, T | \Closure(string):T>
     */
    protected function transformMany(array $keys): array
    {
        return [];
    }

    /**
     * @param array<string> $keys
     *
     * @return array<string, T | \Closure(string):T>
     */
    public function resolve(array $keys): array
    {
        $result = $this->transformMany($keys);

        if (0 === count($result)) {
            foreach ($keys as $key) {
                $result[$key] = fn ($key) => $this->transform($key);
            }
        }

        return $result;
    }
}
