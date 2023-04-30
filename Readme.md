# <span style="color: #F05340;">Laravel Webshop built with Vue 3, Pinia and Filament</span>
## The goal of the project:
To create a Laravel webshop which is easy to install, customizable as much as possible while also keeping it developer-friendly.

##### `Chaotic version, will be tested on Windows 11 using Laravel 8`
---
## Setup

1. Install the migration files and frontend components to your project:
    - `$ php artisan shop:install`
2. Run the migrations to prepare the database:
    - `$ php artisan migrate`
3. Use the example app.js to get an idea:

    ```js
    require('./bootstrap')

    import { createApp } from 'vue'
    import { createPinia } from 'pinia'
    import { useCartStore, useCheckoutStore } from './components/shop/services/store'
    import CartDrawer from './components/shop/cart/CartDrawer'
    import CartWidget from './components/shop/cart/CartWidget'
    import Checkout from './components/shop/Checkout'
    import ProductList from './components/shop/ProductList'

    const app = createApp({})

    app.use(createPinia())

    app.component('cart-drawer', CartDrawer)
    app.component('cart-widget', CartWidget)
    app.component('checkout', Checkout)
    app.component('product-list', ProductList)

    app.mount('#app')

    let cart = useCartStore()
    cart.init()

    let checkout = useCheckoutStore()
    checkout.init()
    ```
4. Run the compiler:
    - `$ npm run dev`

---

## Setup Filament support (Optional)

Install the Filament support resources:

`$ php artisan shop:filament`

This does not include a User Filament resource. \
However, if you would like to, you can display a user's previous orders on their admin page,
just update your own User Filament resource with the following:

```php
use App\Filament\Resources\UserResource\RelationManagers\OrdersRelationManager;

public static function getRelations(): array
{
    return [
        OrdersRelationManager::class,
    ];
}
```