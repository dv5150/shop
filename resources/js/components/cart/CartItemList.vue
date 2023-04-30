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
                    <span>{{ product.item.price_gross }}</span>
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
                <td>{{ product.item.price_gross * product.quantity }}</td>
            </tr>
            <tr>
                <th>
                    TOTAL
                </th>
                <td></td>
                <td>{{ cart.totalPrice }}</td>
            </tr>
        </tbody>
    </table>
</template>

<script setup>
import { useCartStore } from '../services/store'

let cart = useCartStore()
</script>

<style lang="scss" scoped></style>