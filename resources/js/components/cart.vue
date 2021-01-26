<template>
    <li class="nav-item dropdown">
        <a id="navbarDropdown" data-keepOpenOnClick class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            {{ strCart }} ({{ cartSum }})
        </a>

        <div class="dropdown-menu dropdown-menu-right cart" data-keepOpenOnClick aria-labelledby="navbarDropdown">
            <div class="empty" v-if='cart.length == 0'>
                {{ strCartEmpty }}
            </div>
            <div v-else class='cart-items'>
                <div v-for='(cart_item, index) in cart' class='cart-item' :key='index'>
                    <div class='ammount'>({{ cart_item.ammount }})</div>
                    <div class='name'>
                        {{ cart_item.product.name }}
                    </div>
                    <div class="price">
                        {{ cart_item.product.price }}{{ strCartCharacter }}
                    </div>
                    <div class="remove" @click='remove(cart_item.product.id)'>
                        X
                    </div>
                </div>
                <div class="total">
                    {{ cartTotal }}{{ strCartCharacter }}
                </div>
                <div class="checkout">
                    <button @click="order">
                        Order
                    </button>
                </div>
            </div>
        </div>
    </li>
</template>

<script>
export default {
    props: {
        strCart: String,
        strCartEmpty: String,
        strCartCharacter: String,
    },
    data () {
        return {
            tie: 1,
        }
    },
    mounted() {
        this.$store.dispatch("loadCart")
    },
    methods: {
        order () {
            alert('We just sold your cart data to google target advertising program. Read politics of confidentiality next time :D')
        },
        remove (id) {
            this.$store.dispatch('cartRemove', id)
        }
    },
    computed: {
        cart () {
            return this.$store.getters.cart;
        },
        cartSum () {
            let sum = 0
            for (var i = 0; i < this.cart.length; i++) {
                sum += this.cart[i].ammount
            }
            return sum
        },
        cartTotal () {
            let total = 0
            for (var i = 0; i < this.cart.length; i++) {
                total += this.cart[i].ammount * this.cart[i].product.price
            }
            return total.toFixed(2);
        }
    }
}
</script>