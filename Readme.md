# Laravel Webshop API</span>

---
## The goal of the project:
To provide a set of easily customizable webshop API endpoints.

---

## Requirements
- Laravel 8+
- PHP >=8.1

---

## Setup
1. `$ composer require "dv5150/shop:^1.0"`
2. `$ php artisan shop:install:api` 
3. (Optional) Update the `resources/lang/vendor/shop/en/validation.php` localization file if needed
4. (Optional) Update the newly published migration files if needed
5. Run the migrations to prepare the database: `$ php artisan migrate`

## API Endpoints
### Cart endpoints

| METHOD | URL                                            | PARAMETERS                                                          | DESCRIPTION                                                                                   |
|--------|------------------------------------------------|---------------------------------------------------------------------|-----------------------------------------------------------------------------------------------|
| GET    | api/shop/cart                                  | ---                                                                 | Retrieve the complete cart data                                                               |
| DELETE | api/shop/cart/coupon                           | ---                                                                 | Remove the currently applied coupon                                                           |
| POST   | api/shop/cart/coupon/{`code`}                  | `code` <br> [string, required] <br> eg.: "SUMMERSALE50"             | Apply a coupon                                                                                |
| POST   | api/shop/cart/payment-mode/{`provider`}        | `provider` <br> [string, required] <br> eg.: "stripe"`              | Select the payment mode                                                                       |
| POST   | api/shop/cart/shipping-mode/{`provider`}       | `provider` <br> [string, required] <br> eg.: "foxpost"`             | Select the shipping mode                                                                      |
| DELETE | api/shop/cart/{`product`}                      | `product` <br> [integer, required]                                  | Remove the selected product from the cart                                                     |
| POST   | api/shop/cart/{`product`}/add/{`quantity?`}    | `product` <br> [integer, required] <br> `quantity` <br> [integer]   | Add the selected product to the cart <br> or increase the amount by the given number          |    
| POST   | api/shop/cart/{`product`}/remove/{`quantity?`} | `product` <br> [integer, required] <br> `quantity` <br> [integer]   | Decrease the amount of the selected product <br> by the given number or remove it completely  | 

### Checkout endpoints
| METHOD | URL               | PARAMETERS | DESCRIPTION                               |
|--------|-------------------|------------|-------------------------------------------|
| POST   | api/shop/checkout | ---        | Send and store the order in the database  | 

### Payment endpoints
| METHOD | URL                                   | PARAMETERS                                                                                       | DESCRIPTION                                     |
|--------|---------------------------------------|--------------------------------------------------------------------------------------------------|-------------------------------------------------|
| POST   | api/shop/payment/{`provider`}/webhook | `provider` <br> [string, required] <br> eg.: "stripe"                                            | Receive webhooks from payment providers         |
| GET    | payment/{`provider`}/pay/{`order`}    | `provider` <br> [string, required] <br> eg.: "stripe" <br> `order` <br> [string, required, UUID] | Start the payment process of the selected Order |

## Using custom models
Update the `config/shop.php` config file with your own models if needed.

Your custom models must be set up using one of the following methods:
- Extend the webshop's default model
- Create a new model and implement the following contracts `and` concerns as stated below:

| Model        | Contract                                                     | Concern                                                                          |
|--------------|--------------------------------------------------------------|----------------------------------------------------------------------------------|
| Coupon       | `DV5150\Shop\Contracts\Deals\Coupons\BaseCouponContract`     | `DV5150\Shop\Concerns\Deals\BaseCouponTrait`                                     |
| Discount     | `DV5150\Shop\Contracts\Deals\Discounts\BaseDiscountContract` | `DV5150\Shop\Concerns\Deals\BaseDiscountTrait`                                   |
| Order        | `DV5150\Shop\Contracts\Models\OrderContract`                 | ---                                                                              |
| OrderItem    | `DV5150\Shop\Contracts\Models\OrderItemContract`             | ---                                                                              |
| Payment      | `DV5150\Shop\Contracts\Models\PaymentContract`               | ---                                                                              |
| PaymentMode  | `DV5150\Shop\Contracts\Models\PaymentModeContract`           | ---                                                                              |
| Product      | `DV5150\Shop\Contracts\Models\SellableItemContract`          | `DV5150\Shop\Concerns\Models\SellableItemTrait` <br> `Spatie\Sluggable\HasSlug`  |
| ShippingMode | `DV5150\Shop\Contracts\Models\ShippingModeContract`          | ---                                                                              |
| User         | `DV5150\Shop\Contracts\Models\ShopUserContract`              | `DV5150\Shop\Concerns\Models\ShopUserTrait`                                      |

## Replacing existing controllers and services
Bind the following contracts in your application's service provider to replace the default controllers, services and transformers:

| Logical Entity          | Contract                                                              | Default entity                                           |
|-------------------------|-----------------------------------------------------------------------|----------------------------------------------------------|
| Cart API Controller     | `DV5150\Shop\Contracts\Controllers\API\CartAPIControllerContract`     | `DV5150\Shop\Http\Controllers\API\CartAPIController`     |
| Checkout API Controller | `DV5150\Shop\Contracts\Controllers\API\CheckoutAPIControllerContract` | `DV5150\Shop\Http\Controllers\API\CheckoutAPIController` |
| Payment Controller      | `DV5150\Shop\Contracts\Controllers\PaymentControllerContract`         | `DV5150\Shop\Http\Controllers\PaymentController`         |
|                         |                                                                       |                                                          |
| Cart Service            | `DV5150\Shop\Contracts\Services\CartServiceContract`                  | `DV5150\Shop\Services\CartService`                       |
| Checkout Service        | `DV5150\Shop\Contracts\Services\CheckoutServiceContract`              | `DV5150\Shop\Services\CheckoutService`                   |
| Coupon Service          | `DV5150\Shop\Contracts\Services\CouponServiceContract`                | `DV5150\Shop\Services\CouponService`                     |
| Message Service         | `DV5150\Shop\Contracts\Services\MessageServiceContract`               | `DV5150\Shop\Services\MessageService`                    |
| Payment Mode Service    | `DV5150\Shop\Contracts\Services\PaymentModeServiceContract`           | `DV5150\Shop\Services\PaymentModeService`                |
| Shipping Mode Service   | `DV5150\Shop\Contracts\Services\ShippingModeServiceContract`          | `DV5150\Shop\Services\ShippingModeService`               |
| Product List Service    | `DV5150\Shop\Contracts\Services\ProductListComposerServiceContract`   | `DV5150\Shop\Services\ProductListComposerService`        |
| Shop Service            | `DV5150\Shop\Contracts\Services\ShopServiceContract`                  | `DV5150\Shop\Services\ShopService`                       |
|                         |                                                                       |                                                          |
| Order Transformer       | `DV5150\Shop\Contracts\Transformers\OrderDataTransformerContract`     | `DV5150\Shop\Transformers\OrderDataTransformer`          |
| Order Item Transformer  | `DV5150\Shop\Contracts\Transformers\OrderItemDataTransformerContract` | `DV5150\Shop\Transformers\OrderItemDataTransformer`      |

## Facades
The package provides the following facades:

| Facade | Service                            |
|--------|------------------------------------|
| Cart   | `DV5150\Shop\Services\CartService` |
| Shop   | `DV5150\Shop\Services\ShopService` |

Cart facade - available methods:

| Method                                                                                    | Description                                                                                                             |
|-------------------------------------------------------------------------------------------|-------------------------------------------------------------------------------------------------------------------------|
| `Cart::all(): CartCollectionContract`                                                     | Retrieves the global cart state                                                                                         |
| `Cart::reset(): CartCollectionContract`                                                   | Empty the cart                                                                                                          |
| `Cart::addItem(SellableItemContract $item, int $quantity = 1): CartCollectionContract`    | Add an item to the cart / Increase its quantity                                                                         |
| `Cart::removeItem(SellableItemContract $item, int $quantity = 1): CartCollectionContract` | Decrease the quantity of an item in the cart / Remove it from the cart                                                  |
| `Cart::eraseItem(SellableItemContract $item): CartCollectionContract`                     | Remove an item from the cart completely                                                                                 |
| `Cart::getSubtotal(CartCollectionContract $cartResults): float`                           | Get the subtotal of the items in cart including product-level discounts <br> but not the cart-level discounts (coupons) |
| `Cart::getTotal(CartCollectionContract $cartResults): float`                              | Get the final price of the cart, including all kinds of discounts, shipping and payment mode fees.                      |
| `Cart::hasDigitalItemsOnly(): bool`                                                       | Determines whether the cart contains only digital items.                                                                |
| `Cart::setCoupon(?BaseCouponContract $coupon): CartCollectionContract`                    | Applies the given coupon on the cart if possible.                                                                       |
| `Cart::getCoupon(): ?BaseCouponContract`                                                  | Retrieves the applied coupon entity if there's any.                                                                     |
| `Cart::getCouponSummary(CartCollectionContract $cartResults): ?array`                     | Retrieves the applied coupon and the amount of its discount if there's any.                                             |
| `Cart::setShippingMode(?ShippingModeContract $shippingMode): CartCollectionContract`      | Applies the given shipping mode on the cart if possible.                                                                |
| `Cart::getShippingMode(): ?ShippingModeContract`                                          | Retrieves the applied shipping mode if there's any.                                                                     |
| `Cart::setPaymentMode(?PaymentModeContract $paymentMode): CartCollectionContract`         | Applies the given payment mode on the cart if possible.                                                                 |
| `Cart::getPaymentMode(): ?PaymentModeContract`                                            | Retrieves the applied payment mode if there's any.                                                                      |
| `Cart::saveCart(CartCollectionContract $cart): CartCollectionContract`                    | Saves the cart in the session.                                                                                          |

Shop facade - available methods

| Method                                                          | Description                                          |
|-----------------------------------------------------------------|------------------------------------------------------|
| `Shop::registerPaymentProviders(array $paymentProviders): void` | Register a payment provider                          |
| `Shop::getPaymentProvider(string $key): ?string`                | Retrieve a payment provider                          |
| `Shop::getAllPaymentProviders(): array`                         | Retrieve all payment providers                       |
| `Shop::isFrontendInstalled(): bool`                             | Determines whether the frontend package is installed |

## Filament support

Using [Filament](https://filamentphp.com/)? We've got you covered. Take a look at the Laravel Webshop Filament support [package](https://github.com/dv5150/shop-filament).

---

## Frontend

If you need an out-of-the-box frontend scaffolding, check out this [package](https://github.com/dv5150/shop-frontend).

---

## Stripe support

Looking to implement [Stripe](https://stripe.com) payment gateway? Click [here](https://github.com/dv5150/shop-stripe).

---