<template>
    <table width="100%">
        <thead>
            <tr>
                <th>Pickup Point</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <iframe width="100%" height="489px" loading="lazy" src="https://online.sprinter.hu/terkep/#/"></iframe>
                </td>
            </tr>
        </tbody>
    </table>
</template>

<script setup>
import { useCheckoutStore } from '../services/store'

let checkout = useCheckoutStore()

window.addEventListener('message', (event) => {
    if (event?.origin !== 'https://online.sprinter.hu') {
        return
    }

    try {
        let apt = JSON.parse(event.data)

        checkout.setPickupPoint({
            zipCode: apt.zipCode,
            city: apt.city,
            street: apt.address.split(",").pop().trim(),
            comment: apt.shopName,
        })
    } catch (error) {
        console.log(error)
        return
    }
}, false)
</script>

<style lang="scss" scoped></style>