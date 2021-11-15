<?php

namespace Oesteve\Transformer;

use Closure;
use Oesteve\Transformer\Middleware\Middleware;
use Oesteve\Transformer\Resolver\InvalidResolverResponseException;

class Transformer
{
    /** @var Closure(Collection<mixed> $collection):void */
    private Closure $middlewareChain;

    public function __construct(Middleware ...$middleware)
    {
        $this->middlewareChain = $this->createExecutionChain($middleware);
    }

    /**
     * @template T
     * @param class-string<T> $className
     * @param string $key
     *
     * @return Collection<T>
     * @throws InvalidResolverResponseException
     * @throws KeyAlreadyDefinedException
     */
    public function transform(string $className, string $key): Collection
    {
        $res = $this->transformMany($className, [$key]);
        $keys = $res->getKeys();

        if (!isset($keys[$key])) {
            throw new InvalidResolverResponseException("Missing key $key in resolver return data for type $className");
        }

        return $res;
    }



    /**
     * @template T
     * @param class-string<T> $className
     * @param array<string> $keys
     *
     * @return Collection<T>
     * @throws KeyAlreadyDefinedException
     */
    public function transformMany(string $className, array $keys): Collection
    {
        $collection = new Collection($className, $keys);

        if (empty($keys)) {
            return $collection;
        }

        ($this->middlewareChain)($collection);

        return $collection;
    }

    /**
     * @param Middleware[] $middlewareList
     */
    private function createExecutionChain(array $middlewareList): Closure
    {
        $lastCallable = static fn () => null;

        while ($middleware = array_pop($middlewareList)) {
            $lastCallable = static fn (Collection $collection) => $middleware->next($collection, $lastCallable);
        }

        return $lastCallable;
    }
}
