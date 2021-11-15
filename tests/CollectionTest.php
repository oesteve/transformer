<?php

namespace Oesteve\Tests\Transformer;

use Oesteve\Transformer\Collection;
use Oesteve\Transformer\Item;
use Oesteve\Transformer\KeyAlreadyDefinedException;
use Oesteve\Transformer\KeyNotFoundException;
use Oesteve\Transformer\NullKeyException;
use Oesteve\Transformer\UnableToDetermineItemKeyException;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    public function testGetInvalidKey(): void
    {
        $collection = new Collection('MyClass', ['bar']);

        $this->expectException(KeyNotFoundException::class);
        $collection->get('foo');
    }

    public function testAccessorError(): void
    {
        $collection = new Collection('MyClass', ['bar']);

        $this->expectException(UnableToDetermineItemKeyException::class);
        $collection->from([['bar' => 'foo']]);
    }

    public function testAddDefinedKey(): void
    {
        $collection = new Collection('MyClass', ['bar']);

        $this->expectException(KeyAlreadyDefinedException::class);
        $collection->addItem('bar');
    }

    public function testReference(): void
    {
        $collection = new Collection('MyClass', ['bar', 'foo']);

        $collection->forEach(
            fn (Item $item) => $item->set('value: ' . $item->getKey())
        );

        $this->assertEquals(
            ['bar' => 'value: bar', 'foo' => 'value: foo'],
            $collection->getValues()
        );

        self::assertEquals(
            'bar',
            $collection->get('bar')->getKey()
        );
        self::assertEquals(
            'value: bar',
            $collection->get('bar')->getValue()
        );
    }

    public function testFrom(): void
    {
        $collection = new Collection('MyClass', ['bar', 'foo']);

        $data = [
            ['id' => 'foo', 'value' => 'Foo value'],
            ['id' => 'bar', 'value' => 'Bar value'],
        ];

        $collection->from($data);

        $this->assertEquals(
            ['id' => 'bar', 'value' => 'Bar value'],
            $collection->get('bar')->getValue()
        );
    }

    public function testFromMap(): void
    {
        $collection = new Collection('MyClass', ['bar', 'foo']);

        $data = [
            ['name' => 'foo', 'value' => 'Foo value'],
            ['name' => 'bar', 'value' => 'Bar value'],
        ];

        $collection
            ->from($data, 'name')
            ->map(function (Item $item, $data) {
                $item->set($data['value']);
            });

        $this->assertEquals(
            'Bar value',
            $collection->get('bar')->getValue()
        );
    }

    public function testAddNullKey(): void
    {
        $this->expectException(NullKeyException::class);
        $this->expectExceptionMessage('Null key value on position #1 for class MyClass');

        new Collection('MyClass', ['bar', null, 'foo']);
    }
}
