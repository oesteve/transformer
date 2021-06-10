<?php
namespace Oesteve\Transformer;

use Oesteve\Transformer\Resolver\InvalidResolverResponseException;

class Transformer
{

    private ResolverLocator $resolverLocator;

    public function __construct(ResolverLocator $resolverLocator)
    {
        $this->resolverLocator = $resolverLocator;
    }

    /**
     * @template T
     * @param class-string<T> $className
     * @param string $key
     *
     * @return T
     * @throws InvalidResolverResponseException
     */
    public function transform(string $className, string $key)
    {
        $res = $this->transformMany($className, [$key]);

        if(!isset($res[$key])){
            throw new InvalidResolverResponseException("Missing key $key in resolver return data");
        }

        return $res[$key];
    }

    /**
     * @template T
     * @param class-string<T> $className
     * @param array<string> $keys
     *
     * @return array<T>
     */
    public function transformMany(string $className, array $keys): array
    {
        return $this->resolverLocator->locate($className)->resolve($keys);
    }

}