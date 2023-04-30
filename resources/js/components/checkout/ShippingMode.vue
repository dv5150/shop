<template>
    <table width="100%">
        <thead>
            <tr>
                <th>Shipping mode</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="shippingMode in checkout.shippingModes" @click.prevent="checkout.selectShippingMode(shippingMode)"
                :style="[{
                    'color: red;': checkActiveShippingMode(shippingMode.id),
                }]">
                <td>{{ shippingMode.name }}</td>
                <td>{{ shippingMode.price_gross }}</td>
            </tr>
        </tbody>
    </table>
    <component :is="shippingModeComponents[checkout.selectedShippingMode?.provider]"></component>
</template>

<script setup>
import { useCheckoutStore } from '../services/store'

import ExpressOne from '../shippingModes/ExpressOne'
import FoxPost from '../shippingModes/FoxPost'
import PickPackPont from '../shippingModes/PickPackPont'

let checkout = useCheckoutStore()

let checkActiveShippingMode = (id) => checkout.selectedShippingMode?.id === id

let shippingModeComponents = {
    expressone: ExpressOne,
    foxpost: FoxPost,
    pickpackpont: PickPackPont,
}

</script>

<style lang="scss" scoped></style>