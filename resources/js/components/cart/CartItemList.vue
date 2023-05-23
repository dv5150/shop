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
                    <span v-if="product.item.price_gross < product.item.price_gross_original">
                        <s>{{ product.item.price_gross_original }}</s>
                    </span>
                    <span style="color: blue; margin-left: 10px;">
                        {{ product.item.price_gross }}
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
                <td>{{ product.subtotal }}</td>
            </tr>
            <tr>
                <th>
                    TOTAL
                </th>
                <td></td>
                <td>{{ cart.total }}</td>
            </tr>
        </tbody>
    </table>
</template>

<script setup>
import { useCartStore } from '../services/store'

let cart = useCartStore()
</script>

<style lang="scss" scoped></style>