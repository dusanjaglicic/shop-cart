<script setup>
import { Link, router, usePage } from '@inertiajs/vue3'
import Navbar from '@/Components/Navbar.vue'
defineProps({
    cartItems: Array,
    total: Number,
})

const page = usePage()

function updateQty(item, qty) {
    router.patch(`/cart/items/${item.id}`, { quantity: qty }, { preserveScroll: true })
}

function removeItem(item) {
    router.delete(`/cart/items/${item.id}`, { preserveScroll: true })
}
function placeOrder() {
    router.post('/checkout')
}

</script>

<template>
    <div class="max-w-5xl mx-auto p-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold">Cart</h1>
            <Navbar />
        </div>

        <div v-if="cartItems.length === 0" class="p-6 border rounded bg-white">
            <div v-if="page.props.flash?.success" class="text-green-700 font-semibold">
                {{ page.props.flash.success }}
            </div>
            <div v-else>
                Cart is empty.
            </div>
        </div>


        <div v-else class="border rounded bg-white overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                <tr>
                    <th class="text-left p-3">Product</th>
                    <th class="text-left p-3">Price</th>
                    <th class="text-left p-3">Qty</th>
                    <th class="text-left p-3">Subtotal</th>
                    <th class="p-3"></th>
                </tr>
                </thead>

                <tbody>
                <tr v-for="item in cartItems" :key="item.id" class="border-t">
                    <td class="p-3 font-medium">
                        {{ item.product.name }}
                        <div class="text-xs text-gray-500">Stock: {{ item.product.stock_quantity }}</div>
                    </td>

                    <td class="p-3">
                        {{ item.product.price }}
                    </td>

                    <td class="p-3">
                        <input
                            type="number"
                            min="1"
                            class="w-20 border rounded px-2 py-1"
                            :value="item.quantity"
                            @change="updateQty(item, Number($event.target.value))"
                        />
                    </td>

                    <td class="p-3 font-semibold">
                        {{ (Number(item.product.price) * item.quantity).toFixed(2) }}
                    </td>

                    <td class="p-3 text-right">
                        <button class="text-red-600 hover:underline" @click="removeItem(item)">
                            Remove
                        </button>
                    </td>
                </tr>
                </tbody>
            </table>

            <div class="border-t p-4 flex items-center justify-between">
                <div class="text-lg font-bold">
                    Total: {{ Number(total).toFixed(2) }}
                </div>

                <button
                    type="button"
                    class="px-4 py-2 rounded bg-black text-white"
                    @click="placeOrder"
                >
                    Place order
                </button>
            </div>
        </div>
    </div>
</template>
