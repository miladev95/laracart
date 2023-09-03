<?php
namespace Miladev\Laracart;

use Exception;
use InvalidArgumentException;
use Miladev\Laracart\Repository\CartRepository;
use Miladev\Laracart\Repository\CollectionRepository;

class Cart
{
    const CARTSUFFIX = '_cart';

    private $source;

    public function __construct()
    {
        $this->source = resolve(CartRepository::class);
    }

    public function add(array $product)
    {
        $this->source->validateItem($product);


        // If item already added, increment the quantity
        if (isset($product['id'])) {
            $item = $this->get($product['id']);

            return $this->updateQty($item->id, $item->quantity + $product['quantity']);
        }

        $product = $this->source->insert($product);

        return $this->get($product->id);
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

        $item = array_merge((array) $this->get($product['id']), $product);

        $items = $this->collection->insert($item);

        Session::put($this->getCart(), $items);

        return $this->collection->make($items);
    }


    public function updateQty($id, $quantity)
    {
        $item = (array) $this->get($id);

        $item['quantity'] = $quantity;

        return $this->update($item);
    }

    /**
     * Update price of an Item.
     *
     * @param mixed $id
     * @param float $price
     *
     * @return \Miladev\Laracart\Repository\CollectionRepository
     */
    public function updatePrice($id, $price)
    {
        $item = (array) $this->get($id);

        $item['price'] = $price;

        return $this->source->update($item);
    }

    public function remove($id)
    {
        return $this->source->destroy($id);
    }

    public function getItems()
    {
        return $this->source->getItems();
    }

    public function get($id)
    {
        return $this->source->findItem($id);
    }


    public function has($id)
    {
        return (bool) $this->source->findItem($id);
    }

    /**
     * Get the number of Unique items in the cart
     *
     * @return int
     */

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

    /**
     * Get the total quantities of items in the cart
     *
     * @return int
     */

    public function totalQuantity()
    {
        $items = $this->getItems();

        return $items->sum(function ($item) {
            return $item->quantity;
        });
    }

    /**
     * Clone a cart to another
     *
     * @param  mix $cart
     *
     * @return void
     */

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

    /**
     * Alias of clear (Deprecated)
     *
     * @return void
     */

    public function flash()
    {
        $this->clear();
    }

    /**
     * Empty cart
     *
     * @return void
     */

    public function clear()
    {
        Session::forget($this->getCart());
    }
}
