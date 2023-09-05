<?php

namespace Miladev\Laracart;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;
use Miladev\Laracart\Repository\CartRepository;

class Cart
{
    const CARTSUFFIX = '_cart';

    private $source;

    public function __construct()
    {
        $this->source = resolve(CartRepository::class);
    }

    public function getItems()
    {
        return $this->source->getItems();
    }

    public function get($id)
    {
        return $this->source->findItem($id);
    }

    public function add(array $product)
    {
        $this->source->validateItem($product);


        // If item already added, increment the quantity
        if (isset($product['id'])) {
            $item = $this->get($product['id']);

            return $this->updateQty($item->id, $item->quantity + $product['quantity']);
        }

        $cart = $this->source->insert($product);

        return $cart;
    }


    public function update(array $product)
    {
        $this->collection->setItems(Session::get($this->getCart(), []));

        if (!isset($product['id'])) {
            throw new Exception('id is required');
        }

        if (!$this->has($product['id'])) {
            throw new Exception('There is no item in shopping cart with id: ' . $product['id']);
        }

        $item = array_merge((array)$this->get($product['id']), $product);

        $items = $this->collection->insert($item);

        Session::put($this->getCart(), $items);

        return $this->collection->make($items);
    }


    public function updateQty($id, $quantity)
    {
        $item = (array)$this->get($id);

        $item['quantity'] = $quantity;

        return $this->update($item);
    }


    public function updatePrice($id, $price)
    {
        $item = (array)$this->get($id);

        $item['price'] = $price;

        return $this->source->update($item);
    }

    public function remove($product_id)
    {
        return $this->source->destroy($product_id);
    }


    public function has($id)
    {
        return (bool)$this->source->findItem($id);
    }


    public function count()
    {
        $items = $this->getItems();
        return $items->count();
    }


    public function getTotal()
    {
        $items = $this->getItems();

        return $items->sum(function ($item) {
            return $item->price * $item->quantity;
        });
    }


    public function totalQuantity()
    {
        $items = $this->getItems();

        return $items->sum(function ($item) {
            return $item->quantity;
        });
    }


    public function copy($cart)
    {
        if (is_object($cart)) {
            if (!$cart instanceof \Miladev\Laracart\Cart) {
                throw new InvalidArgumentException("Argument must be an instance of " . get_class($this));
            }

            $items = Session::get($cart->getCart(), []);
        } else {
            if (!Session::has($cart . self::CARTSUFFIX)) {
                throw new Exception('Cart does not exist: ' . $cart);
            }

            $items = Session::get($cart . self::CARTSUFFIX, []);
        }

        Session::put($this->getCart(), $items);

    }


    public function flash()
    {
        $this->clear();
    }


    public function clear()
    {
        Session::forget($this->getCart());
    }
}
