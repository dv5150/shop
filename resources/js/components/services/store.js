import axios from 'axios'
import { defineStore } from 'pinia'

export const useCartStore = defineStore('cart', {
    state: () => {
        return {
            isOpen: false,
            products: [],
            coupon: null,
            total: 0
        }
    },
    getters: {
        cartItemLength: (state) => _.sumBy(state.products, (product) => product.quantity)
    },
    actions: {
        init() {
            axios.get('/api/shop/cart')
                .then(response => {
                    this.products = response.data.cart.items
                    this.coupon = response.data.cart.coupon
                    this.total = response.data.cart.total
                })
        },
        increaseQuantity(id, quantity = 1) {
            axios.post(`/api/shop/cart/${id}/add/${quantity}`)
                .then(response => this.products = response.data.cart.items)
        },
        decreaseQuantity(id, quantity = 1) {
            axios.post(`/api/shop/cart/${id}/remove/${quantity}`)
                .then(response => this.products = response.data.cart.items)
        },
        eraseProduct(id) {
            axios.delete(`/api/shop/cart/${id}`)
                .then(response => this.products = response.data.cart.items)
        },
        applyCoupon(code) {
            axios.post(`/api/shop/cart/coupon/${code}`)
                .then(response => {
                    this.coupon = response.data.cart.coupon
                    this.total = response.data.cart.total
                })
        },
        eraseCoupon() {
            axios.delete(`/api/shop/cart/coupon`)
                .then(response => {
                    this.coupon = response.data.cart.coupon
                    this.total = response.data.cart.total
                })
        },
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
            this.selectedShippingMode = { ...shippingMode }
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