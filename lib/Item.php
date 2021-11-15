<?php

namespace Oesteve\Transformer;


/**
 * @template T
 */
class Item
{
    private string $key;
    private mixed $value;
    private ?\DateTime $expiresAt;

    /**
     * Item constructor.
     * @param string $key
     * @param mixed $value
     */
    public function __construct(string $key, mixed $value = null)
    {
        $this->key = $key;
        $this->value = $value;
    }


    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return T|null
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * @param T $value
     */
    public function set(mixed $value): void
    {
        $this->value = $value;
    }

    public function expiresAt(\DateTime $dateTime):void
    {
        $this->expiresAt = $dateTime;
    }

    /**
     * @return \DateTime|null
     */
    public function getExpiresAt(): ?\DateTime
    {
        return $this->expiresAt;
    }
}
