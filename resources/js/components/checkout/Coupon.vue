<template>
    <table width="100%">
        <thead style="text-align: left">
            <tr>
                <th colspan="2">Coupon</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    Coupon code
                </td>
                <td>
                    <input type="text" id="couponCode" placeholder="ABCDEF123">
                </td>
                <td>
                    <button @click="applyCoupon">Apply coupon</button>
                </td>
            </tr>
        </tbody>
        <tfoot v-if="cart.coupon">
            <tr>
                <td class="bg-gray-400 p-5">
                    {{ cart.coupon.fullName }}
                </td>
                <td class="bg-gray-400 p-5">
                    <a href="#" @click="cart.eraseCoupon()">[REMOVE COUPON]</a>
                </td>
            </tr>
        </tfoot>
    </table>
</template>

<script setup>
import { useCartStore } from '../services/store'

let cart = useCartStore()

let applyCoupon = () => {
    let codeInputField = document.querySelector('#couponCode')

    cart.applyCoupon(codeInputField.value)

    codeInputField.value = ''
}
</script>

<style lang="scss" scoped></style>