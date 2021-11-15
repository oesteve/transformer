<?php

namespace Oesteve\Transformer\Middleware;

use Oesteve\Transformer\Collection;
use Psr\Log\LoggerInterface;

class LoggerMiddleware implements Middleware
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function next(Collection $collection, callable $next): void
    {
        $this->logger->debug(
            'Transform',
            [
                'class' => $collection->getClassName(),
                'keys' => $collection->getKeys()
            ]
        );
        $next($collection);
    }
}
