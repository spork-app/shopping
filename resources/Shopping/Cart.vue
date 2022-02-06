<template>
    <div class="m-4">
        <div class="mb-4 border-b pb-4">
            <div class="mt-0 md:flex md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                        Cart
                    </h2>
                </div>
                <div class="mt-4 flex-shrink-0 flex md:mt-0 md:ml-4">
                    <button type="button" class="ml-3 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Checkout
                    </button>
                </div>
            </div>
        </div>
        <div v-if="$store.getters.cart?.settings?.items?.length > 0" role="list" class="grid grid-cols-1 gap-3 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            <store-item v-for="item in $store.getters.cart?.settings?.items" :key="item.id" :item="item"></store-item>
        </div>
        <div v-else class="w-full p-4 border bg-white italic rounded">
            No items in the store
        </div>

        <feature-required feature="shopping" />
    </div>
</template>

<script>
import { ShoppingCartIcon, PhoneIcon, ChevronRightIcon, ChevronLeftIcon, TrashIcon } from '@heroicons/vue/solid'

export default {
    name: "Cart",
    components: {
        ShoppingCartIcon,
        PhoneIcon,
        ChevronLeftIcon,
        ChevronRightIcon,
        TrashIcon,
    },
    computed: {
        itemsInCart() {
            if (!this.$store.getters.cart.settings.items) {
                return {}
            }

            return this.$store.getters.cart?.settings?.items?.reduce((items, item) => ({
                ...items,
                [item.id]: item.count
            }), {}) ?? {}
        }
    }
}
</script>
