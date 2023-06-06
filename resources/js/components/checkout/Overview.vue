<template>
    <div style="margin-bottom: 7rem;">
        <table>
            <thead>
                <tr>
                    <th colspan="2">OVERVIEW</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th>Shipping Data</th>
                    <th>Billing Data</th>
                </tr>
                <tr>
                    <td>Name: {{ checkout.shippingData.name }}</td>
                    <td>Name: {{ checkout.billingData.name }}</td>
                </tr>
                <tr>
                    <td>Zip code: {{ checkout.shippingData.zipCode }}</td>
                    <td>Zip code: {{ checkout.billingData.zipCode }}</td>
                </tr>
                <tr>
                    <td>City: {{ checkout.shippingData.city }}</td>
                    <td>City: {{ checkout.billingData.city }}</td>
                </tr>
                <tr>
                    <td>Address: {{ checkout.shippingData.street }}</td>
                    <td>Address: {{ checkout.billingData.street }}</td>
                </tr>
                <tr>
                    <td>Comments: {{ checkout.shippingData.comment }}</td>
                    <td>Tax number: {{ checkout.billingData.taxNumber }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup>
import { useCheckoutStore } from '../services/store'

let checkout = useCheckoutStore()
</script>

<style lang="scss" scoped></style>
