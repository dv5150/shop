<template>
    <ul v-if="checkout.errors?.[fieldName]" style="color: red; font-size: small; list-style: none;">
        <li v-for="error in checkout.errors[fieldName]">{{ error }}</li>
    </ul>
</template>

<script setup>
import { useCheckoutStore } from '../services/store'

let props = defineProps({
    fieldName: String,
})

let checkout = useCheckoutStore()
</script>
