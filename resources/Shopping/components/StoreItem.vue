<template>
    <div class="col-span-1 flex flex-col text-center bg-white rounded-lg shadow divide-y divide-gray-200">
        <div class="flex-1 flex flex-col p-8">
            <img class="w-32 h-32 flex-shrink-0 mx-auto rounded-full" :src="item.image_url" alt="" />
            <h3 class="mt-6 text-gray-900 text-sm font-medium">{{ item.name }}</h3>
        </div>
        <div>
            <div class="-mt-px flex divide-x divide-gray-200">
                <div class="w-0 flex-1 flex">
                    <button @click.prevent="() => $store.dispatch('addItemToCart', { id: item.id, price: item.price, name: item.name, image_url: item.image_url, count: item.count, store: item.store })"
                            class="gap-2 relative -mr-px w-0 flex-1 inline-flex items-center justify-center py-4 text-sm text-gray-700 font-medium border border-transparent rounded-bl-lg hover:text-gray-500">
                        <ShoppingCartIcon class="w-5 h-5 text-gray-400" aria-hidden="true" />
                        <span>Add<span v-if="itemsInCart[item.id]">&nbsp;({{itemsInCart[item.id]}})</span></span>
                    </button>
                </div>
                <div class="-ml-px w-0 flex-1 flex" v-if="itemsInCart[item.id]">
                    <button @click.prevent="() => $store.dispatch('removeItemToCart', item)"
                            class="gap-2 relative w-0 flex-1 inline-flex items-center justify-center py-4 text-sm text-gray-500 font-medium border border-transparent rounded-br-lg hover:text-red-600">
                        <TrashIcon class="w-5 h-5 fill-current" aria-hidden="true" />
                        <span>Remove</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ShoppingCartIcon, TrashIcon } from '@heroicons/vue/solid'

export default {
    components: {
        ShoppingCartIcon,
        TrashIcon,
    },
    name: "Item",
    props: ['item'],
    computed: {
        itemsInCart() {
            if (!this.$store.getters.cart.settings?.items) {
                return {}
            }

            return this.$store.getters.cart.settings?.items?.reduce((items, item) => ({
                ...items,
                [item.id]: item.count
            }), {});
        }
    }
}
</script>

<style scoped>

</style>
