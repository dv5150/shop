<template>
    <div style="margin-bottom: 7rem;">
        <table>
            <thead>
                <tr>
                    <th colspan="2">Personal Data</th>
                </tr>
            </thead>
        </table>
        <fieldset>
            <label for="email">E-Mail</label>
            <input type="text" id="email" placeholder="jackbauer@fake+mail.com" v-model="checkout.personalData.email">

            <label for="email">Phone</label>
            <input type="text" id="phone" placeholder="+36301234567" v-model="checkout.personalData.phone">

            <label for="comment">Comments</label>
            <textarea placeholder="Without sugar, please" id="comment" rows="4" v-model="checkout.personalData.comment"></textarea>
        </fieldset>
    </div>
</template>

<script setup>
import { useCheckoutStore } from '../services/store'

let checkout = useCheckoutStore()
</script>

<style lang="scss" scoped></style>
