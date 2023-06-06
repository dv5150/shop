import axios from 'axios'
import { defineStore } from 'pinia'
import { defineAsyncComponent } from 'vue'

export const useCartStore = defineStore('cart', {
    state: () => {
        return {
            products: [],
            selectedShippingMode: null,
            selectedPaymentMode: null,

            isOpen: false,
            currency: null,
            coupon: null,
            availableShippingModes: [],
            subtotal: 0,
            total: 0,
        }
    },
    getters: {
        cartItemLength: (state) => _.sumBy(state.products, (product) => product.quantity),
        availablePaymentModes: (state) => state.selectedShippingMode?.paymentModes,
        disableShippingModeFields: (state) => {
            return state.selectedShippingMode?.provider && (state.selectedShippingMode?.provider !== 'default')
        },
    },
    actions: {
        init() {
            axios.get('/api/shop/cart')
                .then(response => this.updateWholeTable(response))
        },
        increaseQuantity(id, quantity = 1) {
            axios.post(`/api/shop/cart/${id}/add/${quantity}`)
                .then(response => this.updateWholeTable(response))
        },
        decreaseQuantity(id, quantity = 1) {
            axios.post(`/api/shop/cart/${id}/remove/${quantity}`)
                .then(response => this.updateWholeTable(response))
        },
        eraseProduct(id) {
            axios.delete(`/api/shop/cart/${id}`)
                .then(response => this.updateWholeTable(response))
        },
        applyCoupon(code) {
            axios.post(`/api/shop/cart/coupon/${code}`)
                .then(response => this.updateWholeTable(response))
        },
        eraseCoupon() {
            axios.delete(`/api/shop/cart/coupon`)
                .then(response => this.updateWholeTable(response))
        },
        selectShippingMode(shippingMode) {
            axios.post(`/api/shop/cart/shipping-mode/${shippingMode.provider}`)
                .then(response => this.updateWholeTable(response))
        },
        selectPaymentMode(paymentMode) {
            axios.post(`/api/shop/cart/payment-mode/${paymentMode.provider}`)
                .then(response => this.updateWholeTable(response))
        },
        updateWholeTable(response) {
            this.products = response.data.cart.items
            this.coupon = response.data.cart.coupon
            this.subtotal = response.data.cart.subtotal
            this.total = response.data.cart.total
            this.currency = response.data.cart.currency
            this.availableShippingModes = response.data.cart.availableShippingModes
            this.selectedShippingMode = response.data.cart.shippingMode
            this.selectedPaymentMode = response.data.cart.paymentMode
        }
    }
})

export const useCheckoutStore = defineStore('checkout', {
    state: () => {
        return {
            personalData: {
                email: '',
                phone: '',
                comment: '',
            },
            shippingData: {
                name: '',
                zipCode: '',
                city: '',
                street: '',
                comment: '',
            },
            billingData: {
                name: '',
                zipCode: '',
                city: '',
                street: '',
                taxNumber: '',
            },
        }
    },
    actions: {
        setPickupPoint(pickupPoint) {
            this.shippingData = {
                name: this.shippingData.name,
                comment: this.shippingData.comment,
                ...pickupPoint
            }
        }
    },
    getters: {
        activeShippingModeComponent: (state) => {
            let cart = useCartStore()

            let componentName = cart.selectedShippingMode?.componentName

            if (!componentName) {
                return null
            }

            return defineAsyncComponent(() => import(`../shippingModes/${componentName}.vue`))
        }
    },
})
