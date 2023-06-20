<template>
    <div style="margin-bottom: 7rem;">
        <table>
            <thead>
                <tr>
                    <th>Shipping mode</th>
                </tr>
            </thead>
        </table>
        <div class="row">
            <div class="column" v-for="shippingMode in cart.availableShippingModes">
                <label
                    :for="'shipping-mode-'+shippingMode.provider"
                    class="label-inline"
                >
                    <input
                        type="radio"
                        :checked="cart.selectedShippingMode?.provider === shippingMode.provider"
                        @click.prevent="cart.selectShippingMode(shippingMode)"
                        :id="'shipping-mode-'+shippingMode.provider"
                    > {{ shippingMode.name }} ({{ shippingMode.priceGross }} {{ cart.currency.code }})
                </label>
            </div>
        </div>
        <div class="row">
            <div class="column">
                <input-error :field-name="'shippingMode.provider'"></input-error>
            </div>
        </div>
        <div class="row">
            <div class="column">
                <component :is="checkout.activeShippingModeComponent"></component>
            </div>
        </div>
    </div>
</template>

<script setup>
import {useCartStore, useCheckoutStore} from '../services/store'
import InputError from "../partials/InputError"

let cart = useCartStore()
let checkout = useCheckoutStore()
</script>

<style lang="scss" scoped></style>
