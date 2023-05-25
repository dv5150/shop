<template>
    <table width="100%">
        <thead style="text-align: left">
            <tr>
                <th>Product name</th>
                <th>QTY</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="product in cart.products">
                <th>
                    {{ product.item.name }} <br>
                    <span v-if="product.item.discount">
                        <s>{{ product.item.price_gross_original }} {{ cart.currency?.code }}</s>
                    </span>
                    <span style="color: blue; margin-left: 10px;">
                        {{ product.item.price_gross }} {{ cart.currency?.code }}
                    </span>
                    <br>
                    <span v-if="product.item.discount" style="color: blue;">
                        {{ product.item.discount.fullName }}
                    </span>
                </th>
                <td>
                    <button @click="cart.decreaseQuantity(product.item.id)">
                        [ DECREMENT ]
                    </button>
                    <input type="number" :value="product.quantity" disabled />
                    <button @click="cart.increaseQuantity(product.item.id)">
                        [ INCREMENT ]
                    </button>
                    <button @click="cart.eraseProduct(product.item.id)">
                        [ REMOVE ]
                    </button>
                </td>
                <td>{{ product.subtotal }} {{ cart.currency?.code }}</td>
            </tr>
            <tr v-if="cart.coupon?.couponItem">
                <th>
                    CART SUBTOTAL
                </th>
                <td></td>
                <td><strong>{{ cart.subtotal }} {{ cart.currency?.code }}</strong></td>
            </tr>
            <tr v-if="cart.coupon?.couponItem">
                <th>
                    {{ cart.coupon.couponItem.fullName }}
                </th>
                <td></td>
                <td>{{ cart.coupon.couponDiscountAmount }} {{ cart.currency?.code }}</td>
            </tr>
            <tr>
                <th>
                    CART TOTAL
                </th>
                <td></td>
                <td><strong>{{ cart.total }} {{ cart.currency?.code }}</strong></td>
            </tr>
        </tbody>
    </table>
</template>

<script setup>
import { useCartStore } from '../services/store'

let cart = useCartStore()
</script>

<style lang="scss" scoped></style>