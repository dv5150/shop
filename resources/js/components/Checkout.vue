<template>
    <!-- Item list -->
    <div>
        <cart-item-list :products="cart.products"></cart-item-list>
        <coupon></coupon>
        <personal-data></personal-data>
        <shipping-mode></shipping-mode>
        <shipping-data></shipping-data>
        <billing-data></billing-data>
        <payment-mode v-if="cart.availablePaymentModes?.length"></payment-mode>
        <overview></overview>

        <button type="button" @click="submitOrder">
            [ SUBMIT ORDER ]
        </button>

    </div>
</template>

<script setup>
import { useCartStore, useCheckoutStore } from './services/store'
import BillingData from './checkout/BillingData'
import CartItemList from './cart/CartItemList'
import Overview from './checkout/Overview'
import PaymentMode from "./checkout/PaymentMode.vue";
import PersonalData from './checkout/PersonalData'
import ShippingData from './checkout/ShippingData'
import ShippingMode from './checkout/ShippingMode'

let cart = useCartStore()
let checkout = useCheckoutStore()

let submitOrder = () => {
    let data = {
        cartData: cart.products,
        shippingMode: cart.selectedShippingMode,
        paymentMode: cart.selectedPaymentMode,
        personalData: checkout.personalData,
        shippingData: checkout.shippingData,
        billingData: checkout.billingData,
    }

    axios.post('/api/shop/checkout', data).then(response => {
        if (response.status === 201) {
            window.location = response.data.redirectUrl
        }
    }).catch(err => {
        if (err.response.status === 422) {
            checkout.updateErrorMessages(err.response)
        }
    })
}
</script>
