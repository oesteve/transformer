<?php

namespace Oesteve\Transformer;

use Oesteve\Transformer\Resolver\InvalidResolverResponseException;

class Transformer
{
    public const OPTION_RESULT_ASSOC = 'OPTION_RESULT_ASSOC';

    private ResolverLocator $resolverLocator;

    public function __construct(ResolverLocator $resolverLocator)
    {
        $this->resolverLocator = $resolverLocator;
    }

    /**
     * @template T
     *
     * @param class-string<T> $className
     *
     * @return T
     *
     * @throws InvalidResolverResponseException
     */
    public function transform(string $className, string $key)
    {
        $res = $this->transformMany($className, [$key], [self::OPTION_RESULT_ASSOC]);

        if (!isset($res[$key])) {
            throw new InvalidResolverResponseException("Missing key $key in resolver return data for type $className");
        }

        return $res[$key];
    }

    /**
     * @template T
     *
     * @param class-string<T> $className
     * @param array<mixed>    $keys
     * @param array<string>   $options
     *
     * @return array<T>
     */
    public function transformMany(string $className, array $keys, array $options = []): array
    {
        if (empty($keys)) {
            return [];
        }

        $keys = array_map(function (mixed $key) {
            if (is_string($key)) {
                return $key;
            }

            return (string) $key;
        }, $keys);

        $res = $this->resolverLocator->locate($className)->resolve($keys);

        if (in_array(self::OPTION_RESULT_ASSOC, $options)) {
            return $res;
        }

        return array_values($res);
    }
}
