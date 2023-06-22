<template>
    <table style="margin-bottom: 7rem;">
        <thead>
            <tr>
                <th colspan="3">Coupon</th>
            </tr>
        </thead>
        <tbody>
            <tr v-if="!cart.coupon?.couponItem">
                <td>
                    Coupon code
                </td>
                <td>
                    <input type="text" id="couponCode" placeholder="ABCDEF123">
                </td>
                <td>
                    <a href="#" class="button" @click="applyCoupon">[ APPLY COUPON ]</a>
                </td>
            </tr>
            <tr v-if="cart.coupon?.couponItem">
                <td colspan="2">
                    {{ cart.coupon.couponItem.name }}
                </td>
                <td>
                    <a href="#" class="button button-clear" @click="cart.eraseCoupon()">[ REMOVE COUPON ]</a>
                </td>
            </tr>
        </tbody>
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
