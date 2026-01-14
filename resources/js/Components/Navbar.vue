<script setup>
import { Link, usePage } from '@inertiajs/vue3'

const page = usePage()
</script>

<template>
    <header class="border-b bg-white">
        <div class="max-w-5xl mx-auto px-6 py-4 flex items-center justify-between">
            <!-- Logo / Home -->


            <!-- Links -->
            <nav class="flex items-center gap-4 text-sm">
                <Link href="/products" class="underline">
                    Products
                </Link>

                <Link href="/cart" class="underline">
                    Cart
                </Link>

                <!-- Auth -->
                <template v-if="page.props.auth?.user">
          <span class="text-gray-600">
            {{ page.props.auth.user.name }}
          </span>

                    <Link
                        href="/logout"
                        method="post"
                        as="button"
                        class="underline"
                    >
                        Logout
                    </Link>
                </template>

                <template v-else>
                    <Link href="/login" class="underline">
                        Login
                    </Link>

                    <Link href="/register" class="underline">
                        Register
                    </Link>
                </template>
            </nav>
        </div>
    </header>
</template>

