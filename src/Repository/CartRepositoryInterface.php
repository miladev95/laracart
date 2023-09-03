<?php

namespace Miladev\Laracart\Repository;

interface CartRepositoryInterface
{
    public function getItems();

    public function findItem($key);

    public function insert(array $item);

    // Alias of insert
    public function update(array $item);

    public function validateItem(array $item);
}