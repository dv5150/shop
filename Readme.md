# <span style="color: #F05340;">Laravel Webshop built with Vue 3, Pinia and Filament</span>
## The goal of the project:
To create a Laravel webshop which is easy to install, customizable as much as possible while also keeping it developer-friendly.

##### `Chaotic version, will be tested on Windows 11 using Laravel 8`
---
## Setup

- Install the migration files and frontend components to your project:
    - `$ php artisan shop:install`
- Run the migrations to prepare the database:
    - `$ php artisan migrate`
- Use the example app.js to get an idea:

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
- Run the compiler:
    - `$ npm run dev`