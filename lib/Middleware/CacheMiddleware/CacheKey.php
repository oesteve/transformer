<?php


namespace Oesteve\Transformer\Middleware\CacheMiddleware;

class CacheKey
{
    private string $namespace;

    public function __construct(string $namespace)
    {
        $this->namespace = $namespace;
    }

    public static function for(string $namespace): self
    {
        return new self($namespace);
    }

    public function key(string $key): string
    {
        return sprintf('%s_%s', $this->namespace, $key);
    }

    /**
     * @param array<string> $keys
     * @return array<string>
     */
    public function keys(array $keys): array
    {
        return array_map(fn (string $key) => $this->key($key), $keys);
    }

    public function fromKey(string $cacheKey): string
    {
        return str_replace(sprintf('%s_', $this->namespace), '', $cacheKey);
    }
}
