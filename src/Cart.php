<?php

namespace Miladev\Laracart;

use Exception;
use Illuminate\Support\Facades\Session;
use InvalidArgumentException;
class Cart implements CartInterface
{
    public const CART_SUFFIX = '_cart';
    public const COUPON_SUFFIX = '_coupon';

    private string $cartName = 'default';

    public function __construct(string $name = 'default')
    {
        $this->setCart($name);
    }

    public function setCart($name)
    {
        if (!is_string($name) || trim($name) === '') {
            throw new InvalidArgumentException('Cart name is required');
        }

        $this->cartName = $this->normalizeCartName($name);

        return $this;
    }

    public function named(string $name)
    {
        return $this->setCart($name);
    }

    public function getCart()
    {
        return $this->cartName;
    }

    public function getItems()
    {
        return collect(Session::get($this->getCart(), []));
    }

    public function items()
    {
        return $this->getItems();
    }

    public function get($id)
    {
        return $this->getItems()->firstWhere('id', $id);
    }

    public function add(array $product)
    {
        $this->validateItem($product);

        if (isset($product['id'])) {
            $item = $this->get($product['id']);

            if ($item) {
                $currentQuantity = (int) data_get($item, 'quantity', 0);

                return $this->updateQty(data_get($item, 'id'), $currentQuantity + (int) $product['quantity']);
            }
        }

        $items = $this->getItems()->values()->all();
        $items[] = $product;
        Session::put($this->getCart(), $items);

        return $this->getItems();
    }

    public function update(array $product)
    {
        if (!isset($product['id'])) {
            throw new Exception('id is required');
        }

        if (!$this->has($product['id'])) {
            throw new Exception('There is no item in shopping cart with id: ' . $product['id']);
        }

        $item = array_merge((array) $this->get($product['id']), $product);

        $items = $this->getItems()->map(function ($existing) use ($item) {
            return data_get($existing, 'id') == $item['id'] ? array_merge((array) $existing, $item) : (array) $existing;
        })->values()->all();

        Session::put($this->getCart(), $items);

        return $this->getItems();
    }

    public function updateQty($id, $quantity)
    {
        $item = (array) $this->get($id);
        $item['quantity'] = $quantity;

        return $this->update($item);
    }

    public function updatePrice($id, $price)
    {
        $item = (array) $this->get($id);
        $item['price'] = $price;

        return $this->update($item);
    }

    public function decreaseQty($product_id)
    {
        $item = $this->get($product_id);

        if (!$item) {
            return $this->getItems();
        }

        return $this->updateQty($product_id, max(1, (int) data_get($item, 'quantity', 1) - 1));
    }

    public function remove($product_id)
    {
        $items = $this->getItems()->reject(function ($item) use ($product_id) {
            return data_get($item, 'id') == $product_id;
        })->values()->all();

        Session::put($this->getCart(), $items);

        return $this->getItems();
    }

    public function removeAll()
    {
        return $this->clear();
    }

    public function has($id)
    {
        return $this->get($id) !== null;
    }

    public function count()
    {
        return $this->getItems()->count();
    }

    public function totalQuantity()
    {
        return $this->getItems()->sum(function ($item) {
            return (int) data_get($item, 'quantity', 0);
        });
    }

    public function getSubTotal()
    {
        return $this->getItems()->sum(function ($item) {
            return (float) data_get($item, 'price', 0) * (int) data_get($item, 'quantity', 0);
        });
    }

    public function getTotal()
    {
        return max(0, $this->getSubTotal() - $this->getDiscountAmount());
    }

    public function copy($cart)
    {
        $sourceName = $cart instanceof self ? $cart->getCart() : $this->normalizeCartName((string) $cart);

        $items = Session::get($sourceName, []);

        Session::put($this->getCart(), $items);

        return $this;
    }

    public function flash()
    {
        return $this->clear();
    }

    public function clear()
    {
        Session::forget($this->getCart());
        Session::forget($this->couponKey());

        return $this;
    }

    public function applyCoupon(array $coupon)
    {
        $coupon = $this->normalizeCoupon($coupon);

        if ($coupon['code'] === '') {
            throw new InvalidArgumentException('Coupon code is required');
        }

        Session::put($this->couponKey(), $coupon);

        return $this;
    }

    public function removeCoupon()
    {
        Session::forget($this->couponKey());

        return $this;
    }

    public function hasCoupon()
    {
        return Session::has($this->couponKey());
    }

    public function getCoupon()
    {
        return Session::get($this->couponKey());
    }

    public function getDiscountAmount()
    {
        $coupon = $this->getCoupon();

        if (!$coupon) {
            return 0;
        }

        $subtotal = $this->getSubTotal();

        if ($coupon['type'] === 'fixed') {
            return min($subtotal, (float) $coupon['value']);
        }

        if ($coupon['type'] === 'percentage') {
            return min($subtotal, $subtotal * ((float) $coupon['value'] / 100));
        }

        throw new InvalidArgumentException('Unsupported coupon type: ' . $coupon['type']);
    }

    private function normalizeCartName(string $name): string
    {
        $name = trim($name);

        return str_ends_with($name, self::CART_SUFFIX) ? $name : $name . self::CART_SUFFIX;
    }

    private function couponKey(): string
    {
        return $this->getCart() . self::COUPON_SUFFIX;
    }

    private function normalizeCoupon(array $coupon): array
    {
        return [
            'code' => trim((string) ($coupon['code'] ?? '')),
            'type' => strtolower((string) ($coupon['type'] ?? 'percentage')),
            'value' => (float) ($coupon['value'] ?? 0),
            'label' => (string) ($coupon['label'] ?? ''),
        ];
    }

    private function validateItem(array $item): void
    {
        $requiredFields = ['id', 'name', 'price', 'quantity'];
        $missing = array_diff($requiredFields, array_keys($item));

        if (!empty($missing)) {
            throw new Exception('Some required fields missing: ' . implode(',', $missing));
        }

        if ((int) $item['quantity'] < 1) {
            throw new Exception('Quantity can not be less than 1');
        }

        if (!is_numeric($item['price'])) {
            throw new Exception('Price must be a numeric number');
        }
    }
}
