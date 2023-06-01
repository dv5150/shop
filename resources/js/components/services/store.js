import axios from 'axios'
import { defineStore } from 'pinia'

export const useCartStore = defineStore('cart', {
    state: () => {
        return {
            isOpen: false,
            products: [],
            currency: null,
            coupon: null,
            shippingMode: null,
            subtotal: 0,
            total: 0,
        }
    },
    getters: {
        cartItemLength: (state) => _.sumBy(state.products, (product) => product.quantity),
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
        updateWholeTable(response) {
            this.products = response.data.cart.items
            this.coupon = response.data.cart.coupon
            this.subtotal = response.data.cart.subtotal
            this.total = response.data.cart.total
            this.currency = response.data.cart.currency
            this.shippingMode = response.data.cart.shippingMode
        }
    }
})

export const useCheckoutStore = defineStore('checkout', {
    state: () => {
        return {
            shippingModes: [],
            selectedShippingMode: null,
            paymentModes: [],
            selectedPaymentMode: null,
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
        init() {
            axios.get('/api/shop/checkout/shipping-modes')
                .then(response => this.shippingModes = response.data.shippingModes)
        },
        selectShippingMode(shippingMode) {
            let cart = useCartStore()

            axios.post(`/api/shop/cart/shipping-mode/${shippingMode.provider}`)
                .then(response => {
                    this.selectedShippingMode = response.data.cart.shippingMode
                    cart.updateWholeTable(response)
                })
        },
        selectPaymentMode(paymentMode) {
            this.selectedPaymentMode = { ...paymentMode }
        },
        setPickupPoint(pickupPoint) {
            this.shippingData = {
                name: this.shippingData.name,
                comment: this.shippingData.comment,
                ...pickupPoint
            }
        }
    }
})