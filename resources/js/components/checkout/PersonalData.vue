<template>
    <table width="100%">
        <thead style="text-align: left">
            <tr>
                <th colspan="2">Personal Data</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    E-Mail
                </td>
                <td>
                    <input type="text" id="small-input" placeholder="jackbauer@fake+mail.com"
                        v-model="checkout.personalData.email" name="email">
                </td>
            </tr>
            <tr>
                <td>
                    Phone
                </td>
                <td>
                    <input type="text" id="small-input" placeholder="+36301234567" v-model="checkout.personalData.phone"
                        name="phone">
                </td>
            </tr>
            <tr>
                <td>
                    Comments
                </td>
                <td>
                    <textarea id="message" rows="4" placeholder="Without sugar, please"
                        v-model="checkout.personalData.comment" name="comment"></textarea>
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