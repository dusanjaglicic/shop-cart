<script setup>
import { Link, router, usePage } from '@inertiajs/vue3'
import { ref } from 'vue'
import Navbar from '@/Components/Navbar.vue'
const props = defineProps({
    product: Object,
})

const page = usePage()
const quantity = ref(1)

function addToCart() {
    router.post('/cart/items', {
        product_id: props.product.id,
        quantity: quantity.value,
    }, {
        onSuccess: () => router.visit('/cart'),
    })
}
</script>

<template>
    <div class="max-w-3xl mx-auto p-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold">Item</h1>

            <Navbar />
        </div>

        <div class="border rounded-lg p-6 bg-white shadow-sm mt-4">
            <h1 class="text-2xl font-bold">{{ product.name }}</h1>

            <p class="mt-3 text-gray-700">
                Price: <b>{{ product.price }}</b>
            </p>

            <p class="mt-1 text-gray-500 text-sm">
                Stock: {{ product.stock_quantity }}
            </p>

            <div class="mt-6 flex items-center gap-3">
                <input
                    type="number"
                    min="1"
                    class="w-24 border rounded px-2 py-2"
                    v-model.number="quantity"
                />

                <button
                    type="button"
                    class="px-4 py-2 rounded bg-black text-white disabled:opacity-50"
                    :disabled="product.stock_quantity === 0"
                    @click="addToCart"
                >
                    Add to cart
                </button>

                <Link href="/cart" class="text-sm underline">
                    Cart
                </Link>
            </div>

            <div v-if="page.props.errors?.quantity" class="mt-3 text-sm text-red-600">
                {{ page.props.errors.quantity }}
            </div>
        </div>
    </div>
</template>
