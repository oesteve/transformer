<?php

namespace Oesteve\Transformer;

use Oesteve\Transformer\Resolver;

interface ResolverLocator
{
    /**
     * @template T
     * @param class-string<T> $dtoClassName
     * @return Resolver<T>
     */
    public function locate(string $dtoClassName): Resolver;
}
