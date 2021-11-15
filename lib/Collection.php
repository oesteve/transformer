<?php

namespace Oesteve\Transformer;

use closure;

/**
 * @template T
 * @implements \Iterator<string,T>
 */
class Collection implements \Iterator
{
    /**
     * @var class-string<T>
     */
    private string $className;

    /**
     * @var array<string, Item<T>>
     */
    private array $items = [];

    private int $pointer = 0;

    /**
     * @param class-string<T> $className
     * @param array<string|null> $keys
     * @throws KeyAlreadyDefinedException
     */
    public function __construct(string $className, array $keys)
    {
        $this->className = $className;
        foreach ($keys as $idx => $key) {
            if ($key === null) {
                throw new NullKeyException(
                    sprintf(
                        "Null key value on position #%s for class %s",
                        $idx,
                        $className
                    )
                );
            }

            $this->addItem($key);
        }
    }

    /**
     * @return array<Item<T>>
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function forEach(closure $closure): void
    {
        foreach ($this->items as $item) {
            $closure($item, $item->getKey());
        }
    }

    /**
     * @return Item<T>
     * @throws KeyAlreadyDefinedException
     */
    public function addItem(string $key, mixed $value = null): Item
    {
        if (isset($this->items[$key])) {
            throw new KeyAlreadyDefinedException();
        }

        $item = new Item($key, $value);
        $this->items[$key] = $item;

        return $item;
    }

    /**
     * @return array<string,mixed>
     */
    public function getValues(): array
    {
        $data = [];
        foreach ($this->getItems() as $item) {
            $data[$item->getKey()] = $item->getValue();
        }

        return $data;
    }

    /**
     * @param array<T> $res
     * @param string $field
     * @return self<T>
     */
    public function from(array $res, string $field = 'id'): self
    {
        foreach ($res as $itemValue) {
            $itemKey = PropertyAccessor::get($itemValue, $field);
            $this
                ->get($itemKey)
                ->set($itemValue);
        }

        return $this;
    }

    /**
     * @return Item<T>
     * @throws KeyNotFoundException
     */
    public function get(string $key): Item
    {
        if (!isset($this->items[$key])) {
            throw new KeyNotFoundException("Key $key not found in collection");
        }

        return $this->items[$key];
    }

    /**
     * @param closure $closure
     */
    public function map(Closure $closure): void
    {
        foreach ($this->items as $item) {
            $closure($item, $item->getValue());
        }
    }

    /**
     * @return class-string<T>
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * @return array<string>
     */
    public function getKeys(): array
    {
        return array_keys($this->items);
    }

    /**
     * @param Collection<T> $filtered
     * @throws KeyNotFoundException
     */
    public function push(Collection $filtered): void
    {
        foreach ($filtered->items as $item) {
            $value = $item->getValue();
            if ($value === null) {
                continue;
            }

            $this->get($item->getKey())->set($value);
        }
    }

    public function empty(): bool
    {
        return count($this->items) === 0;
    }

    /**
     * @return T | null
     */
    public function current()
    {
        $keys = $this->getKeys();
        $key = $keys[$this->pointer]??null;

        if ($key === null) {
            return null;
        }
        return $this->items[$key]->getValue();
    }

    /**
     * @return T | null
     */
    public function next()
    {
        $keys = $this->getKeys();
        $key = $keys[$this->pointer];
        return $this->items[$key]->getValue();
    }

    public function key(): ?string
    {
        $keys = $this->getKeys();
        return $keys[$this->pointer]??null;
    }

    public function valid(): bool
    {
        return isset($this->items[$this->pointer]);
    }

    public function rewind(): void
    {
        $this->pointer = 0;
    }
}
