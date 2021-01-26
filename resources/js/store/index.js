import Vue from 'vue'
import Vuex from 'vuex'
import axios from 'axios'

Vue.use(Vuex)

export default new Vuex.Store({
    state: {
        products: [],
        cart: [],
        loading: false,
    },
    mutations: {
        setCart: (state, payload) => {
            state.cart = payload
        },
        updateAmmount: (state, {id, payload}) => {
            let found = false
            for (var i = 0; i < state.cart.length; i++) {
                if (state.cart[i].product_id == id) {
                    found = true
                    Vue.set(state.cart, i, payload);
                }
            }
            if (!found) {
                Vue.set(state.cart, state.cart.length, payload);
            }
        },
        setLoading (state, payload) {
            state.loading = payload
        }
    },
    actions: {
        addToCart: (context, id) => {
            const token = window.token
            if (!token) {
                alert('login first')
            }
            axios.post('/api/add-to-cart', {
                id: id
            }, {
                headers: {
                    authorization: `Bearer ${token}`,
                }
            })
                .then (res => {
                    context.commit('updateAmmount', {
                        id: id, 
                        payload: res.data
                    })
                });
        },
        loadCart: (context) => {
            const token = window.token

            axios.get('/api/cart/get', {
                headers: {
                    authorization: `Bearer ${token}`,
                }
            })
                .then (res => {
                    context.commit('setCart', res.data);
                });
        },
        cartRemove: (context, id) => {
            const token = window.token

            axios.post('/api/cart/delete', {
                id: id
            },{
                headers: {
                    authorization: `Bearer ${token}`,
                }
            })
                .then (res => {
                    context.commit('setCart', res.data);
                });
        }
    },
    getters: {
        cart: (state) => {
            return state.cart
        },
        loading: (state) => {
            return state.loading
        }
    }
})