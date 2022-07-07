<?php
namespace Miladev\Laracart;

interface CartInterface
{
    public function setCart($name);

    public function getCart();

    public function add(array $product);

    public function update(array $product);

    public function remove($id);

    public function getItems();

    public function get($id);

    public function has($id);

    public function clear();

    public function totalQuantity();

    public function getTotal();
}
