<?php

namespace Miladev\Laracart\Tests;

use Miladev\Laracart\Cart;
use Tests\TestCase;

class CartTest extends TestCase
{
    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidArgumentExceptionOnInvalidCartName()
    {
        $this->expectException(\InvalidArgumentException::class);

        $cart = new Cart();

        $cart->setCart('');
    }

    public function testCartName()
    {
        $cart = new Cart('test');

        $this->assertEquals('test_cart', $cart->getCart());

        $cart = $cart->named('test2');

        $this->assertEquals('test2_cart', $cart->getCart(), 'Set cart name using named() method');
    }

    public function testInvalidCartName()
    {
        $cart = new Cart();

        $cart->setCart('test');

        $this->assertNotEquals('Cart123', $cart->getCart());
    }

    /**
     * @expectedException Exception
     */
    public function testExceptionOnInvalidCartItem()
    {
        $this->expectException(\Exception::class);

        $cart = new Cart('test');

        $cart->add(['id' => '123']);
    }

    public function testAddCartItem()
    {
        $cart = new Cart('test');

        $item = [
            'id' => 123,
            'name' => 'T-shirt',
            'price' => 50,
            'quantity' => 2,
        ];

        $cartItems = $cart->add($item);

        $this->assertEquals($item, (array) $cartItems->first());
    }

    public function testUpdateCartItem()
    {
        $cart = new Cart('test');

        $item = [
            'id' => 123,
            'name' => 'T-shirt',
            'price' => 50,
            'quantity' => 2,
        ];

        $cartItems = $cart->add($item);

        $cartItems = $cart->update(['id' => 123, 'name' => 'Shirt']);

        $this->assertNotEquals($item, (array) $cartItems->first());

        $this->assertEquals(array_merge($item, ['name' => 'Shirt']), (array) $cartItems->first());
    }

    public function testUpdateQuantity()
    {
        $cart = new Cart('test');

        $item = [
            'id' => 123,
            'name' => 'T-shirt',
            'price' => 50,
            'quantity' => 2,
        ];

        $cartItems = $cart->add($item);

        $cartItems = $cart->updateQty(123, 10);

        $this->assertEquals(array_merge($item, ['quantity' => 10]), (array) $cartItems->first());
    }

    public function testUpdatePrice()
    {
        $cart = new Cart('test');

        $item = [
            'id' => 123,
            'name' => 'T-shirt',
            'price' => 50,
            'quantity' => 2,
        ];

        $cartItems = $cart->add($item);

        $cartItems = $cart->updatePrice(123, 1000);

        $this->assertEquals(array_merge($item, ['price' => 1000]), (array) $cartItems->first());
    }

    public function testRemoveItemFromCart()
    {
        $cart = new Cart('test');

        $item = [
            'id' => 123,
            'name' => 'T-shirt',
            'price' => 50,
            'quantity' => 2,
        ];

        $cartItems = $cart->add($item);

        $cartItems = $cart->remove(123);

        $this->assertEquals(0, $cartItems->count());
    }

    public function testGetItems()
    {
        $cart = new Cart('test');

        $item = [
            'id' => 123,
            'name' => 'T-shirt',
            'price' => 50,
            'quantity' => 2,
        ];

        $cart->add($item);

        $cart->add([
            'id' => 124,
            'name' => 'Shoes',
            'price' => 500,
            'quantity' => 1,
        ]);

        $this->assertEquals(2, $cart->items()->count());
        $this->assertEquals(2, $cart->getItems()->count());
    }

    public function testGetItemById()
    {
        $cart = new Cart('test');

        $item = [
            'id' => 123,
            'name' => 'T-shirt',
            'price' => 50,
            'quantity' => 2,
        ];

        $cart->add($item);

        $cart->add([
            'id' => 124,
            'name' => 'Shoes',
            'price' => 500,
            'quantity' => 1,
        ]);

        $this->assertEquals($item, (array) $cart->get(123));
    }

    public function testCartHasItem()
    {
        $cart = new Cart('test');

        $item = [
            'id' => 123,
            'name' => 'T-shirt',
            'price' => 50,
            'quantity' => 2,
        ];

        $cart->add($item);

        $this->assertTrue($cart->has(123));
    }

    public function testCountUniqueItems()
    {
        $cart = new Cart('test');

        $item = [
            'id' => 123,
            'name' => 'T-shirt',
            'price' => 50,
            'quantity' => 2,
        ];

        $cart->add($item);

        $cart->add([
            'id' => 124,
            'name' => 'Shoes',
            'price' => 500,
            'quantity' => 100,
        ]);

        $this->assertEquals(2, $cart->count());
    }

    public function testTotalQuantity()
    {
        $cart = new Cart('test');

        $item = [
            'id' => 123,
            'name' => 'T-shirt',
            'price' => 50,
            'quantity' => 2,
        ];

        $cart->add($item);

        $cart->add([
            'id' => 124,
            'name' => 'Shoes',
            'price' => 500,
            'quantity' => 100,
        ]);

        $this->assertEquals(102, $cart->totalQuantity());
    }

    public function testGetTotalPrice()
    {
        $cart = new Cart('test');

        $item = [
            'id' => 123,
            'name' => 'T-shirt',
            'price' => 50,
            'quantity' => 2,
        ];

        $cart->add($item);

        $cart->add([
            'id' => 124,
            'name' => 'Shoes',
            'price' => 500,
            'quantity' => 100,
        ]);

        $this->assertEquals(50100, $cart->getTotal());
    }

    public function testApplyPercentageCoupon()
    {
        $cart = new Cart('test');

        $cart->add([
            'id' => 123,
            'name' => 'T-shirt',
            'price' => 50,
            'quantity' => 2,
        ]);

        $cart->applyCoupon([
            'code' => 'SAVE10',
            'type' => 'percentage',
            'value' => 10,
        ]);

        $this->assertTrue($cart->hasCoupon());
        $this->assertEquals('SAVE10', $cart->getCoupon()['code']);
        $this->assertEquals(10.0, $cart->getDiscountAmount());
        $this->assertEquals(90.0, $cart->getTotal());
    }

    public function testApplyFixedCoupon()
    {
        $cart = new Cart('test');

        $cart->add([
            'id' => 123,
            'name' => 'T-shirt',
            'price' => 50,
            'quantity' => 2,
        ]);

        $cart->applyCoupon([
            'code' => 'LESS15',
            'type' => 'fixed',
            'value' => 15,
        ]);

        $this->assertEquals(15.0, $cart->getDiscountAmount());
        $this->assertEquals(85.0, $cart->getTotal());
    }

    public function testCouponDiscountCannotExceedSubtotal()
    {
        $cart = new Cart('test');

        $cart->add([
            'id' => 123,
            'name' => 'T-shirt',
            'price' => 50,
            'quantity' => 2,
        ]);

        $cart->applyCoupon([
            'code' => 'BIGSAVE',
            'type' => 'fixed',
            'value' => 500,
        ]);

        $this->assertEquals(100.0, $cart->getDiscountAmount());
        $this->assertEquals(0.0, $cart->getTotal());
    }

    public function testRemoveCoupon()
    {
        $cart = new Cart('test');

        $cart->add([
            'id' => 123,
            'name' => 'T-shirt',
            'price' => 50,
            'quantity' => 2,
        ]);

        $cart->applyCoupon([
            'code' => 'SAVE10',
            'type' => 'percentage',
            'value' => 10,
        ]);

        $cart->removeCoupon();

        $this->assertFalse($cart->hasCoupon());
        $this->assertNull($cart->getCoupon());
        $this->assertEquals(100.0, $cart->getTotal());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testCouponCodeIsRequired()
    {
        $this->expectException(\InvalidArgumentException::class);

        $cart = new Cart('test');

        $cart->applyCoupon([
            'type' => 'percentage',
            'value' => 10,
        ]);
    }

    public function testClearCart()
    {
        $cart = new Cart('test');

        $item = [
            'id' => 123,
            'name' => 'T-shirt',
            'price' => 50,
            'quantity' => 2,
        ];

        $cart->add($item);

        $cart->clear();

        $this->assertEquals(0, $cart->items()->count());
    }
}
