<?php

namespace Miladev\Laracart\Repository;

use Exception;
use Miladev\Laracart\Models\Cart;

class DBRepository implements CartRepository
{
    protected $requiredFields = [
        'product_id',
        'name',
        'price',
        'quantity',
    ];
    public function getItems()
    {
        return Cart::all();
    }

    public function findItem($id)
    {
        return Cart::find($id)->first();
    }

    public function destroy($id)
    {
        return Cart::find($id)->delete();
    }


    public function insert(array $item)
    {
        $this->validateItem($item);


        // If item already added, increment the quantity
        if (isset($item['id'])) {
            $item = $this->findItem($item['id']);

            return $this->updateQty($item);
        }

        return Cart::create($item);
    }

    public function updateQty($item)
    {
        return $item->update([
            'quantity' => $item->quantity + 1,
        ]);
    }

    public function update(array $item)
    {
        $cart = Cart::find($item->id)->first();
        return $cart->update([
            'product_id' => $item->product_id,
            'name' => $item->name,
            'quantity' => $item->quantity,
            'price' => $item->price,
        ]);
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