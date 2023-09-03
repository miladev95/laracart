<?php
namespace Miladev\Laracart;

use Exception;
use InvalidArgumentException;
use Miladev\Laracart\Repository\CollectionRepository;

class Cart
{
    const CARTSUFFIX = '_cart';

    private $source;

    public function __construct()
    {
        $this->source = resolve(CartInterface::class);
    }

    public function add(array $product)
    {
        $this->source->validateItem($product);
        $this->source->insert($product);

        // If item already added, increment the quantity
        if ($this->has($product['id'])) {
            $item = $this->get($product['id']);

            return $this->updateQty($item->id, $item->quantity + $product['quantity']);
        }

        $this->collection->setItems(Session::get($this->getCart(), []));

        $items = $this->collection->insert($product);

        Session::put($this->getCart(), $items);

        return $this->source->make($items);
    }

    /**
     * Update an item.
     *
     * @param  Array  $product
     * @return \Miladev\Laracart\Repository\CollectionRepository
     */
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

    /**
     * Update quantity of an Item.
     *
     * @param mixed $id
     * @param int $quantity
     *
     * @return \Miladev\Laracart\Repository\CollectionRepository
     */
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

        return $this->update($item);
    }

    /**
     * Remove an item from the cart.
     *
     * @param  int $id
     * @return $this
     */
    public function remove($id)
    {
        $items = Session::get($this->getCart(), []);

        unset($items[$id]);

        Session::put($this->getCart(), $items);

        return $this->collection->make($items);
    }

    /**
     * Helper wrapper for cart items.
     *
     * @return \Miladev\Laracart\Repository\CollectionRepository
     */
    public function items()
    {
        return $this->getItems();
    }

    /**
     * Get all the items.
     *
     * @return \Miladev\Laracart\Repository\CollectionRepository
     */
    public function getItems()
    {
        return $this->collection->make(Session::get($this->getCart()));
    }

    /**
     * Get a single item.
     * @param  $id
     *
     * @return Array
     */
    public function get($id)
    {
        $this->collection->setItems(Session::get($this->getCart(), []));

        return $this->collection->findItem($id);
    }

    /**
     * Check an item exist or not.
     * @param  $id
     *
     * @return boolean
     */
    public function has($id)
    {
        $this->collection->setItems(Session::get($this->getCart(), []));

        return $this->collection->findItem($id) ? true : false;
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

    /**
     * Get the total amount
     *
     * @return float
     */

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
