<template>
    <div style="margin-bottom: 7rem;">
        <table>
            <thead>
                <tr>
                    <th>Payment mode</th>
                </tr>
            </thead>
        </table>
        <div class="row">
            <div class="column" v-for="paymentMode in cart.availablePaymentModes">
                <label
                    :for="'payment-mode-'+paymentMode.provider"
                    class="label-inline"
                >
                    <input
                        type="radio"
                        @click.prevent="cart.selectPaymentMode(paymentMode)"
                        :checked="cart.selectedPaymentMode?.provider === paymentMode.provider"
                        :id="'payment-mode-'+paymentMode.provider"
                    > {{ paymentMode.name }} ({{ paymentMode.priceGross }} {{ cart.currency.code }})
                </label>
            </div>
        </div>
        <div class="row">
            <div class="column">
                <input-error :field-name="'paymentMode.provider'"></input-error>
            </div>
        </div>
    </div>
</template>

<script setup>
import {useCartStore, useCheckoutStore} from '../services/store'
import InputError from "../partials/InputError.vue";

let cart = useCartStore()
let checkout = useCheckoutStore()
</script>

<style lang="scss" scoped></style>
