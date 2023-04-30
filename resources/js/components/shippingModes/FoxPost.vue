<template>
    <table width="100%" style="height: 800px;">
        <thead>
            <tr>
                <th>Pickup Point</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <iframe frameborder="0" loading="lazy"
                        src="https://cdn.foxpost.hu/apt-finder/v1/app/?theme=dark&lang=en&discount=1" width="100%"
                        height="766px"></iframe>
                </td>
            </tr>
        </tbody>
    </table>
</template>

<script setup>
import { useCheckoutStore } from '../services/store'

let checkout = useCheckoutStore()

window.addEventListener('message', (event) => {
    if (event?.origin !== 'https://cdn.foxpost.hu') {
        return
    }

    try {
        let apt = JSON.parse(event.data)

        checkout.setPickupPoint({
            zipCode: apt.zip,
            city: apt.city,
            street: apt.street,
            comment: apt.findme,
        })
    } catch (error) {
        return
    }
}, false)
</script>

<style lang="scss" scoped></style>