<?php

namespace DV5150\Shop\Services;

use DV5150\Shop\Support\CartCollection;
use DV5150\Shop\Contracts\ProductContract;
use DV5150\Shop\Models\CartItemCapsule;
use Illuminate\Support\Facades\Session;

class CartService
{
    public function all(): CartCollection
    {
        if ($cart = Session::get($this->getSessionKey())) {
            return unserialize($cart)
                ->map(fn (CartItemCapsule $capsule) => $capsule->refreshDiscount());
        }

        return $this->reset();
    }

    public function reset(): CartCollection
    {
        $cart = new CartCollection();

        $this->saveCart($cart);

        return $cart;
    }

    public function addItem(ProductContract $item, int $quantity = 1): CartCollection
    {
        /** @var CartCollection $cart */
        $cart = $this->all();

        $cart = $cart->hasItem($item)
            ? $cart->incrementQuantityBy($item, $quantity)
            : $cart->push(new CartItemCapsule($item, $quantity));

        $this->saveCart($cart);

        return $this->all();
    }

    public function removeItem(ProductContract $item, int $quantity = 1): CartCollection
    {
        /** @var CartCollection $cart */
        $cart = $this->all();

        if ($cart->hasItem($item)) {
            $cart = $cart->decrementQuantityBy($item, $quantity);
        }

        $this->saveCart($cart);

        return $this->all();
    }

    public function eraseItem(ProductContract $item): CartCollection
    {
        /** @var CartCollection $cart */
        $cart = $this->all();

        if ($cart->hasItem($item)) {
            $cart = $cart->eraseItem($item);
        }

        $this->saveCart($cart);

        return $this->all();
    }

    public function hasDigitalItemsOnly(): bool
    {
        /** @var CartCollection $cart */
        $cart = $this->all();

        return $cart->hasDigitalItemsOnly();
    }

    public function toArray(): array
    {
        return $this->all()->toArray();
    }

    public function toJson($options = 0): string
    {
        return $this->all()->toJson($options);
    }

    public function saveCart(CartCollection $cart): CartCollection
    {
        Session::put($this->getSessionKey(), serialize($cart));

        return $cart;
    }

    public function getSessionKey(): string
    {
        return 'cart';
    }
}
