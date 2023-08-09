# Laravel Meta

[![Latest Stable Version](https://poser.pugx.org/miladev/lara-meta/v)](//packagist.org/packages/miladev/lara-cart)
[![License](https://poser.pugx.org/miladev/lara-meta/license)](//packagist.org/packages/miladev/lara-cart)
[![Total Downloads](https://poser.pugx.org/miladev/lara-meta/downloads)](//packagist.org/packages/miladev/lara-cart)

<a href="https://github.com/miladev95/laracart/issues"><img src="https://img.shields.io/github/issues/miladev95/laracart.svg" alt=""></a>
<a href="https://github.com/miladev95/laracart/stargazers"><img src="https://img.shields.io/github/stars/miladev95/laracart.svg" alt=""></a>
<a href="https://github.com/miladev95/laracart/network"><img src="https://img.shields.io/github/forks/miladev95/laracart.svg" alt=""></a>

# Lara-PHPCart
Laravel PHP shopping cart

## Features

- Simple API
- Support multiple cart instances

## Requirements

- Laravel 5+

## Installation
Lara-phpcart is available via Composer

```bash
$ composer require miladev/lara-phpcart
```

## Integrations

#### Laravel 5.5+ integrations

##### Package Discovery
`Miladev/lara-cart` utilize the Laravel's package auto discovery feature. So, you don't need to add manually Service provider and Facade in Laravel application's config/app.php. Laravel will automatically register the service provider and facades for you.

#### Laravel < 5.5 integrations

After you have installed the Laravel-PHPCart, open the config/app.php file which is included with Laravel and add the following lines.

In the $providers array add the following service provider.

```php
'Miladev\Laracart\CartServiceProvider'
```

Add the facade of this package to the $aliases array.

```php
'Cart' => 'Miladev\Laracart\Facades\Cart'
```

You can now use this facade in place of instantiating the Cart yourself in the following examples.

## Usage

### Add Item

The add method required `id`, `name`, `price` and `quantity` keys. However, you can pass any data that your application required.

```php
use Miladev\Laracart\Cart;

$cart = new Cart();

$cart->add([
    'id'       => 1001,
    'name'     => 'Skinny Jeans',
    'quantity' => 1,
    'price'    => 90
]);
```

### Update Item


```php
$cart->update([
    'id'       => 1001,
    'name'     => 'Hoodie'
]);
```

### Update quantity


```php
$cart->updateQty(1001, 3);
```

### Update price

```php
$cart->updatePrice(1001, 30);
```

### Remove an Item

```php
$cart->remove(1001);
```

### Get all Items

```php
$cart->getItems();
// or
$cart->items();
```

### Get an Item

```php
$cart->get(1001);
```

### Determining if an Item exists in the cart

```php
$cart->has(1001);
```

### Get the total number of items in the cart

```php
$cart->count();
```

### Get the total quantities of items in the cart

```php
$cart->totalQuantity();
```

### Total sum

```php
$cart->getTotal();
```

### Empty the cart

```php
$cart->clear();
```

### Multiple carts

Lara-PHPCart supports multiple cart instances, so that you can have as many shopping cart instances on the same page as you want without any conflicts. 

```php
$cart = new Cart('cart1');
// or
$cart->setCart('cart2');
$cart->add([
    'id'       => 1001,
    'name'     => 'Skinny Jeans',
    'quantity' => 1,
    'price'    => 90
]);

//or
$cart->named('cart3')->add([
    'id'       => 1001,
    'name'     => 'Jeans',
    'quantity' => 2,
    'price'    => 100
]);
```
