<template>
    <!-- Item list -->
    <div>
        <cart-item-list :products="cart.products"></cart-item-list>
        <hr>
        <personal-data></personal-data>
        <hr>
        <shipping-mode></shipping-mode>
        <hr>
        <shipping-data></shipping-data>
        <hr>
        <billing-data></billing-data>
        <hr>
        <overview></overview>
        <hr>

        <button type="button" @click="submitOrder">
            Submit Order
        </button>

    </div>
</template>

<script setup>
import { useCartStore, useCheckoutStore } from './services/store'
import BillingData from './checkout/BillingData'
import CartItemList from './cart/CartItemList'
import ShippingData from './checkout/ShippingData'
import ShippingMode from './checkout/ShippingMode'
import Overview from './checkout/Overview'
import PersonalData from './checkout/PersonalData'

let cart = useCartStore()

let checkout = useCheckoutStore()

let submitOrder = () => {
    let data = {
        cartData: cart.products,
        personalData: checkout.personalData,
        shippingData: checkout.shippingData,
        billingData: checkout.billingData,
    }

    axios.post('/api/shop/checkout', data).then(response => {
        if (response.status === 201) {
            window.location = response.data.redirectUrl
        }

        // @todo: handle error
    })
}
</script>

<style scoped lang="scss"></style>