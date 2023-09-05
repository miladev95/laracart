<?php

namespace Miladev\Laracart\Repository;

interface CartRepository
{
    public function getItems();

    public function findItem($product_id);

    public function insert(array $item);

    // Alias of insert
    public function update(array $item);
    public function increaseQty($product_id);
    public function decreaseQty($product_id);

    public function destroy($product_id);

    public function validateItem(array $item);
}