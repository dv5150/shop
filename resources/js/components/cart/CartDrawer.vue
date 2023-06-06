<template>
    <div :style="{ display: cart.isOpen ? 'block' : 'none' }">
        <button type="button" class="button button-outline" @click.prevent="cart.$patch({ isOpen: false })">
            [ CLOSE ]
        </button>
        <div>
            <cart-item-list></cart-item-list>
        </div>
        <a v-if="cart.products.length > 0 && checkoutUrl" :href="checkoutUrl">
            [ CHECKOUT ]
        </a>
    </div>
</template>

<script setup>
import { useCartStore } from '../services/store'
import { defineProps } from 'vue'
import CartItemList from './CartItemList'

let props = defineProps({
    checkoutUrl: String
})

let cart = useCartStore()
</script>

<style scoped lang="scss"></style>
