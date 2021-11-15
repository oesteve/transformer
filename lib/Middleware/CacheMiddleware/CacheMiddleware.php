<?php


namespace Oesteve\Transformer\Middleware\CacheMiddleware;

use Oesteve\Transformer\Collection;
use Oesteve\Transformer\Middleware\Middleware;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\CacheItem;

class CacheMiddleware implements Middleware
{
    private CacheItemPoolInterface $cache;
    private ?LoggerInterface $logger;

    public function __construct(CacheItemPoolInterface $cache, ?LoggerInterface $logger = null)
    {
        $this->cache = $cache;
        $this->logger = $logger;
    }

    public function next(Collection $collection, callable $next): void
    {

        // Filter the non cached items
        $filtered = $this->filterCached($collection);

        if ($filtered->empty()) {
            return;
        }

        $this->logger?->debug("Miss from cache", [ 'keys' => $filtered->getKeys() ]);
        $next($filtered);

        // Set resolved value on original request
        $collection->push($filtered);

        // Persist resolved values in the cache
        $this->storeCache($filtered);
    }

    /**
     * @template T
     * @param Collection<T> $collection
     * @return Collection<T>
     */
    private function filterCached(Collection $collection): Collection
    {
        $cacheKey = CacheKey::for($collection->getClassName());
        $keys = $collection->getKeys();
        $cacheItems = $this->cache->getItems($cacheKey->keys($keys));

        $found = [];
        $missed = [];

        /** @var CacheItem $cacheItem */
        foreach ($cacheItems as $cacheItem) {
            $key = $cacheKey->fromKey($cacheItem->getKey());
            if ($cacheItem->isHit()) {
                $collection
                    ->get($key)
                    ->set($cacheItem->get());


                $found[] = $cacheKey->fromKey($cacheItem->getKey());
                continue;
            }

            $missed[] = $key;
        }

        if (count($found)) {
            $this->logger?->debug("Hit from cache", [ 'keys' => $found ]);
        }
        return new Collection($collection->getClassName(), $missed);
    }

    /**
     * @template T
     * @param Collection<T> $collection
     */
    private function storeCache(Collection $collection):void
    {
        $cacheKey = CacheKey::for($collection->getClassName());

        foreach ($collection->getItems() as $item) {
            $cacheKey = $cacheKey->key($item->getKey());
            $cacheItem = $this->cache->getItem($cacheKey);
            $cacheItem->set($item->getValue());

            if ($item->getExpiresAt()) {
                $cacheItem->expiresAt($item->getExpiresAt());
            }

            $this->cache->saveDeferred($cacheItem);
        }

        $this->cache->commit();
    }
}
