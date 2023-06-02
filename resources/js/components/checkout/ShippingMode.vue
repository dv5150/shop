<template>
    <div>
        <table width="100%">
            <thead>
                <tr>
                    <th>Shipping mode</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="shippingMode in checkout.shippingModes"
                    @click.prevent="checkout.selectShippingMode(shippingMode)" :style="[{
                        'color: red;': checkout.selectedShippingMode?.id === shippingMode.id,
                    }]">
                    <td>{{ shippingMode.name }}</td>
                    <td>{{ shippingMode.price_gross }}</td>
                </tr>
            </tbody>
        </table>
        <component :is="checkout.activeShippingModeComponent"></component>
    </div>
</template>

<script setup>
import _ from 'lodash'
import { useCheckoutStore } from '../services/store'

let checkout = useCheckoutStore()
</script>

<style lang="scss" scoped></style>