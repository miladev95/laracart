<?php

namespace Miladev\Laracart\Repository;

use App\Models\Cart;
use App\Models\Product;
use Exception;
class DBRepository implements CartRepositoryInterface
{

    protected $requiredFields = [
        'id',
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


    public function insert(array $item)
    {
        return Cart::create($item);
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