<?php

namespace Miladev\Laracart\Repository;

use Exception;
use Illuminate\Support\Facades\Auth;
use Miladev\Laracart\Models\Cart;

class DBRepository implements CartRepository
{
    protected $requiredFields = [
        'product_id',
        'name',
        'price',
        'quantity',
    ];
    private $user_id;

    public function __construct()
    {
        $this->user_id = Auth::id();
    }

    public function getItems()
    {
        return Cart::where('user_id', $this->user_id)->get();
    }

    public function findItem($product_id)
    {
        return Cart::where('product_id', $product_id)->where('user_id', $this->user_id)->first();
    }

    public function destroy($product_id)
    {
        return Cart::where('product_id', $product_id)->where('user_id', $this->user_id)->delete();
    }


    public function insert(array $item)
    {
        $this->validateItem($item);

        $product = $this->findItem($item['product_id']);
        // If item already added, increment the quantity
        if ($product) {
            return $this->increaseQty($item['product_id']);
        }

        $item['user_id'] = $this->user_id;
        return Cart::create($item);
    }

    public function increaseQty($product_id)
    {
        $product = $this->findItem($product_id);
        if ($product) {
            return $product->update([
                'quantity' => $product->quantity + 1,
            ]);
        }

        return null;
    }

    public function decreaseQty($product_id)
    {
        $product = $this->findItem($product_id);
        if ($product) {
            return $product->update([
                'quantity' => $product->quantity - 1,
            ]);
        }

        return null;
    }

    public function update(array $item)
    {
        $cart = Cart::find($item['product_id']);
        if ($cart) {
            return $cart->update([
                'product_id' => $item->product_id,
                'name' => $item->name,
                'quantity' => $item->quantity,
                'price' => $item->price,
            ]);
        }

        return null;

    }

    public function validateItem(array $item)
    {
        $fields = array_diff_key(array_flip($this->requiredFields), $item);

        if ($fields) {
            throw new Exception('Some required fields missing: ' . implode(",", array_keys($fields)));
        }

        if ($item['quantity'] < 1) {
            throw new Exception('Quantity can not be less than 1');
        }

        if (!is_numeric($item['price'])) {
            throw new Exception('Price must be a numeric number');
        }
    }

}