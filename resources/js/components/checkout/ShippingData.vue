<template>
    <table width="100%">
        <thead>
            <tr>
                <th colspan="2">Shipping Address</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    Name
                </td>
                <td>
                    <input type="text" id="small-input" placeholder="Jack Bauer" v-model="checkout.shippingData.name"
                        name="name">
                </td>
            </tr>
            <tr>
                <td>
                    Zip Code
                </td>
                <td>
                    <input type="text" id="small-input" placeholder="1000" v-model="checkout.shippingData.zipCode"
                        :disabled="checkout.selectedShippingMode?.provider && checkout.selectedShippingMode?.provider !== 'default'"
                        name="zipCode">
                </td>
            </tr>
            <tr>
                <td>
                    City
                </td>
                <td>
                    <input type="text" id="small-input" placeholder="Budapest" v-model="checkout.shippingData.city"
                        :disabled="checkout.selectedShippingMode?.provider && checkout.selectedShippingMode?.provider !== 'default'"
                        name="city">
                </td>
            </tr>
            <tr>
                <td>
                    Street
                </td>
                <td>
                    <input type="text" id="small-input" placeholder="Main road 72" v-model="checkout.shippingData.street"
                        :disabled="checkout.selectedShippingMode?.provider && checkout.selectedShippingMode?.provider !== 'default'"
                        name="address">
                </td>
            </tr>
            <tr>
                <td>
                    Comments
                </td>
                <td>
                    <textarea id="message" rows="4" placeholder="Please deliver fast"
                        v-model="checkout.shippingData.comment" name="comments"></textarea>
                </td>
            </tr>
        </tbody>
    </table>
</template>

<script setup>
import { useCheckoutStore } from '../services/store'

let checkout = useCheckoutStore()
</script>

<style lang="scss" scoped></style>