<template>
    <table style="margin-bottom: 7rem;">
        <thead>
            <tr>
                <th>Product name</th>
                <th>Gross Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="product in cart.products">
                <td>
                    <p>{{ product.item.name }}</p>
                    <p v-if="product.item.discount" style="color: mediumseagreen;">
                        {{ product.item.discount.shortName }}
                    </p>
                </td>
                <td>
                    <p v-if="product.item.discount">
                        <s>{{ product.item.price_gross_original }} {{ cart.currency?.code }}</s>
                    </p>
                    <p v-if="product.item.discount" style="color: mediumseagreen;">
                        {{ product.item.price_gross }} {{ cart.currency?.code }}
                    </p>
                </td>
                <td>
                    <span v-text="product.quantity+'x'"></span>
                    <a @click="cart.eraseProduct(product.item.id)">
                        [ X ]
                    </a>
                    <a @click="cart.decreaseQuantity(product.item.id)">
                        [ - ]
                    </a>
                    <a @click="cart.increaseQuantity(product.item.id)">
                        [ + ]
                    </a>
                </td>
                <td>{{ product.subtotal }} {{ cart.currency?.code }}</td>
            </tr>
            <tr>
                <th colspan="3">
                    CART SUBTOTAL
                </th>
                <th>{{ cart.subtotal }} {{ cart.currency?.code }}</th>
            </tr>
            <tr v-if="cart.coupon?.couponItem" style="color: mediumseagreen;">
                <td>
                    {{ cart.coupon.couponItem.shortName }}
                </td>
                <td>{{ cart.coupon.couponDiscountAmount }} {{ cart.currency?.code }}</td>
                <td>1x</td>
                <td>{{ cart.coupon.couponDiscountAmount }} {{ cart.currency?.code }}</td>
            </tr>
            <tr v-if="cart.selectedShippingMode">
                <td>
                    {{ cart.selectedShippingMode.name }}
                </td>
                <td>{{ cart.selectedShippingMode.priceGross }} {{ cart.currency?.code }}</td>
                <td>1x</td>
                <td>{{ cart.selectedShippingMode.priceGross }} {{ cart.currency?.code }}</td>
            </tr>
            <tr v-if="cart.selectedPaymentMode">
                <td>
                    {{ cart.selectedPaymentMode.name }}
                </td>
                <td>{{ cart.selectedPaymentMode.priceGross }} {{ cart.currency?.code }}</td>
                <td>1x</td>
                <td>{{ cart.selectedPaymentMode.priceGross }} {{ cart.currency?.code }}</td>
            </tr>
            <tr>
                <th colspan="3">
                    CART TOTAL
                </th>
                <th>{{ cart.total }} {{ cart.currency?.code }}</th>
            </tr>
        </tbody>
    </table>
</template>

<script setup>
import { useCartStore } from '../services/store'

let cart = useCartStore()
</script>
