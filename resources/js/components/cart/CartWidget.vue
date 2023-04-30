<template>
    <button type="button" @click.prevent="cart.$patch({ isOpen: true })">
        Cart ({{ cart.cartItemLength }})
    </button>
</template>

<script setup>
import { useCartStore } from '../services/store'

let cart = useCartStore()
</script>

<style lang="scss" scoped></style>