<template>
    <table width="100%">
        <thead>
            <tr>
                <th>Overview</th>
                <th></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <h5>SHIPPING DATA</h5>
                    <p>Name: {{ checkout.shippingData.name }}</p>
                    <p>Zip code: {{ checkout.shippingData.zipCode }}</p>
                    <p>City: {{ checkout.shippingData.city }}</p>
                    <p>Address: {{ checkout.shippingData.street }}</p>
                    <p>Comments: {{ checkout.shippingData.comment }}</p>
                </td>
                <td>
                    <h5>BILLING DATA</h5>
                    <p>Name: {{ checkout.billingData.name }}</p>
                    <p>Zip code: {{ checkout.billingData.zipCode }}</p>
                    <p>City: {{ checkout.billingData.city }}</p>
                    <p>Address: {{ checkout.billingData.street }}</p>
                    <p>Tax number: {{ checkout.billingData.taxNumber }}</p>
                </td>
                <td></td>
            </tr>
        </tbody>
    </table>
</template>

<script setup>
import { useCheckoutStore } from '../services/store'

let checkout = useCheckoutStore()
</script>

<style lang="scss" scoped></style>