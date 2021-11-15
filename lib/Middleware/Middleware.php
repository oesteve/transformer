<?php

namespace Oesteve\Transformer\Middleware;
use Oesteve\Transformer\Collection;


interface Middleware
{
    /**
     * @param Collection<mixed> $collection
     * @param callable $next
     */
    public function next(Collection $collection, callable $next): void;
}
