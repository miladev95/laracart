<?php

namespace Miladev\Laracart\Tests;

use Miladev\Laracart\Repository\CollectionRepository;
use Tests\TestCase;

class CollectionTest extends TestCase
{
    public $cart;
    public $item;

    protected function setUp()
    {
        parent::setUp();

        $this->item = [
            'id' => 123,
            'name' => 'T-shirt',
            'price' => 50,
            'quantity' => 2,
        ];
    }

    public function testSetItems()
    {
        $collection = new CollectionRepository();

        $collection->setItems([
            [
                'id' => 125,
                'name' => 'shirt',
                'price' => 500,
                'quantity' => 1,
            ],
            $this->item,
        ]);

        $this->assertEquals($this->item, $collection->getItems()[1]);
    }

    public function testFindItem()
    {
        $collection = new CollectionRepository();

        $collection->setItems([
            [
                'id' => 125,
                'name' => 'shirt',
                'price' => 500,
                'quantity' => 1,
            ],
            $this->item,
        ]);

        $this->assertEquals($this->item, $collection->findItem(1));
    }

    public function testAnItemIsExistInCollection()
    {
        $collection = new CollectionRepository();

        $collection->setItems([
            125 => [
                'id' => 125,
                'name' => 'shirt',
                'price' => 500,
                'quantity' => 1,
            ],
            123 => $this->item,
        ]);

        $this->assertTrue($collection->has($this->item));
    }

    public function testFindAnItemInCollection()
    {
        $collection = new CollectionRepository();

        $collection->setItems([
            125 => [
                'id' => 125,
                'name' => 'shirt',
                'price' => 500,
                'quantity' => 1,
            ],
            123 => $this->item,
        ]);

        $this->assertEquals($this->item, $collection->findItem($this->item['id']));
    }

    public function testItemNotExistInCollection()
    {
        $collection = new CollectionRepository();

        $collection->setItems([
            125 => [
                'id' => 125,
                'name' => 'shirt',
                'price' => 500,
                'quantity' => 1,
            ],
            123 => $this->item,
        ]);

        $this->assertNull($collection->findItem(404));
    }

    public function testInsertAnItemInCollection()
    {
        $collection = new CollectionRepository();

        $collection->insert([
            'id' => 125,
            'name' => 'shirt',
            'price' => 500,
            'quantity' => 1,
        ]);

        $this->assertEquals(125, $collection->findItem(125)->id);
    }

    /**
     * @expectedException Exception
     */
    public function testValidateAnItem()
    {
        $collection = new CollectionRepository();

        $collection->validateItem([
            'id' => 125,
            'quantity' => 1,
        ]);
    }
}
