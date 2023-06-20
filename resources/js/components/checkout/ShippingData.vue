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
            <input-error :field-name="'shippingData.name'"></input-error>

            <label for="zipCode">Zip Code</label>
            <input type="text" id="zipCode" placeholder="1000" v-model="checkout.shippingData.zipCode" :disabled="cart.disableShippingModeFields">
            <input-error :field-name="'shippingData.zipCode'"></input-error>

            <label for="city">City</label>
            <input type="text" id="city" placeholder="Budapest" v-model="checkout.shippingData.city" :disabled="cart.disableShippingModeFields">
            <input-error :field-name="'shippingData.city'"></input-error>

            <label for="address">Street</label>
            <input type="text" id="address" placeholder="Main road 72" v-model="checkout.shippingData.street" :disabled="cart.disableShippingModeFields">
            <input-error :field-name="'shippingData.street'"></input-error>

            <label for="comment">Comments</label>
            <textarea placeholder="Please deliver fast" id="comment" rows="4" v-model="checkout.shippingData.comment"></textarea>
            <input-error :field-name="'shippingData.comment'"></input-error>
        </fieldset>
    </div>
</template>

<script setup>
import {useCartStore, useCheckoutStore} from '../services/store'
import InputError from "../partials/InputError"

let cart = useCartStore()
let checkout = useCheckoutStore()
</script>

<style lang="scss" scoped></style>
