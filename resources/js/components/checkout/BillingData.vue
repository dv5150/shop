<template>
    <table width="100%">
        <thead>
            <tr>
                <th>Billing Address</th>
                <th>
                    <button @click.prevent="copyShippingData">
                        <span>
                            [ Copy shipping data ]
                        </span>
                    </button>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    Name
                </td>
                <td>
                    <input type="text" id="small-input" placeholder="Jack Bauer" v-model="checkout.billingData.name"
                        name="name">
                </td>
            </tr>
            <tr>
                <td>
                    Zip Code
                </td>
                <td>
                    <input type="text" id="small-input" placeholder="1000" v-model="checkout.billingData.zipCode"
                        name="zipCode">
                </td>
            </tr>
            <tr>
                <td>
                    City
                </td>
                <td>
                    <input type="text" id="small-input" placeholder="Budapest" v-model="checkout.billingData.city"
                        name="city">
                </td>
            </tr>
            <tr>
                <td>
                    Street
                </td>
                <td>
                    <input type="text" id="small-input" placeholder="Main road 72" v-model="checkout.billingData.street"
                        name="address">
                </td>
            </tr>
            <tr>
                <td>
                    Tax Number
                </td>
                <td>
                    <input type="text" id="small-input" placeholder="123456789" v-model="checkout.billingData.taxNumber"
                        name="taxNumber">
                </td>
            </tr>
        </tbody>
    </table>
</template>

<script setup>
import { useCheckoutStore } from '../services/store'

let checkout = useCheckoutStore()

let copyShippingData = () => {
    checkout.$patch({
        billingData: {
            ...checkout.shippingData,
            taxNumber: checkout.billingData.taxNumber
        }
    })
}
</script>

<style lang="scss" scoped></style>