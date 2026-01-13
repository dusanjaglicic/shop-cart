<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    products: Array,
})
</script>

<template>
    <div class="max-w-5xl mx-auto p-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold">Products</h1>

            <div class="flex gap-3">
                <Link href="/cart" class="text-sm underline">Cart</Link>
                <Link href="/login" class="text-sm underline">Login</Link>
            </div>
        </div>

        <div v-if="products.length === 0" class="p-6 border rounded bg-white">
            No products found.
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div
                v-for="p in products"
                :key="p.id"
                class="border rounded-lg p-4 bg-white shadow-sm"
            >
                <h2 class="font-semibold text-lg">
                    <Link :href="`/products/${p.id}`" class="hover:underline">
                        {{ p.name }}
                    </Link>
                </h2>

                <p class="text-gray-700 mt-2">
                    Price: <b>{{ p.price }}</b>
                </p>

                <p class="text-gray-500 text-sm mt-1">
                    Stock: {{ p.stock_quantity }}
                </p>

                <div class="mt-4">
                    <Link
                        :href="`/products/${p.id}`"
                        class="inline-block px-3 py-2 rounded bg-black text-white text-sm"
                    >
                        View
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
