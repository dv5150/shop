<template>
    <div class="row" v-for="chunk in _.chunk(products, 4)">
        <div class="column" v-for="product in chunk">
            <product-card :product="product"></product-card>
        </div>
    </div>
</template>

<script setup>
import ProductCard from './partials/ProductCard'
import _ from 'lodash'

let props = defineProps({
    products: Array
})
</script>
