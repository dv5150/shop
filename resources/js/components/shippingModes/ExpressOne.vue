<template>
    <table width="100%">
        <thead>
            <tr>
                <th colspan="2">Pickup Point</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    Name
                </td>
                <td>
                    <input type="text" id="small-input" placeholder="Jack Bauer" v-model="checkout.shippingData.name">
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <iframe width="100%" style="border: none; height:1000px;"
                        src="https://webcas.expressone.hu/eoneshops/eo_api_v2.php" allowtransparency="false"
                        scrolling="yes"></iframe>
                </td>
            </tr>
        </tbody>
    </table>
</template>

<script setup>
import { useCheckoutStore } from '../services/store'

let checkout = useCheckoutStore()

let handler = (event) => {
    if (event?.origin !== 'https://webcas.expressone.hu') {
        return
    }

    try {
        let apt = event.data

        checkout.setPickupPoint({
            zipCode: apt.zip_code,
            city: apt.city,
            street: apt.street,
            comment: '',
        })
    } catch (error) {
        console.log(error)
        return
    }
}

window.addEventListener('message', handler, false)
</script>

<style lang="scss" scoped></style>