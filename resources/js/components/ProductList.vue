<template>
    <div v-for="product in products">
        <product-card :product="product"></product-card>
        <hr>
    </div>
</template>

<script setup>
import ProductCard from './partials/ProductCard'

let props = defineProps({
    products: Array
})
</script>

<style lang="scss" scoped></style>