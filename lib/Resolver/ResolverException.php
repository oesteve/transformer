<?php

namespace Oesteve\Transformer\Resolver;

/**
 * @template T
 */
class ResolverException extends \Exception
{
    /**
     * @var array<\Throwable>
     */
    private array $errors;

    /**
     * @var array<string,T>
     */
    private array $validResults;

    /**
     * @param array<\Throwable> $errors
     * @param array<string,T>   $validResults
     */
    public function __construct(array $errors, array $validResults)
    {
        parent::__construct('Transformer throws exceptions');
        $this->errors = $errors;
        $this->validResults = $validResults;
    }

    /**
     * @return array<\Throwable>
     */
    public function getErrors(): array
    {
        return array_values($this->errors);
    }

    /**
     * @return array<int,T>
     */
    public function getValidResults(): array
    {
        return array_values($this->validResults);
    }
}
