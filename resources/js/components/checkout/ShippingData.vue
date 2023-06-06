<template>
    <div style="margin-bottom: 7rem;">
        <table>
            <thead>
                <tr>
                    <th colspan="2">Shipping Address</th>
                </tr>
            </thead>
        </table>
        <fieldset>
            <label for="name">Name</label>
            <input type="text" id="name" placeholder="Jack Bauer" v-model="checkout.shippingData.name">

            <label for="zipCode">Zip Code</label>
            <input type="text" id="zipCode" placeholder="1000" v-model="checkout.shippingData.zipCode" :disabled="cart.disableShippingModeFields">

            <label for="city">City</label>
            <input type="text" id="city" placeholder="Budapest" v-model="checkout.shippingData.city" :disabled="cart.disableShippingModeFields">

            <label for="address">Street</label>
            <input type="text" id="address" placeholder="Main road 72" v-model="checkout.shippingData.street" :disabled="cart.disableShippingModeFields">

            <label for="comment">Comments</label>
            <textarea placeholder="Please deliver fast" id="comment" rows="4" v-model="checkout.shippingData.comment"></textarea>
        </fieldset>
    </div>
</template>

<script setup>
import {useCartStore, useCheckoutStore} from '../services/store'

let cart = useCartStore()
let checkout = useCheckoutStore()
</script>

<style lang="scss" scoped></style>
