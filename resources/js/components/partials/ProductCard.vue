<template>
    <div style="padding: 10px 0px;">
        <h5>{{ product.name }}</h5>
        <p>
            <s v-if="product.price_gross < product.price_gross_original">
                {{ product.price_gross_original }}
            </s>
            {{ product.price_gross }}
        </p>
        <p>
            <a href="#" @click.prevent="cart.increaseQuantity(product.id, 1)">
                [ Add to cart ]
            </a>
        </p>
    </div>
</template>

<script setup>
import { useCartStore } from '../services/store'

let props = defineProps({
    product: Object,
})

let cart = useCartStore()
</script>

<style lang="scss" scoped></style>