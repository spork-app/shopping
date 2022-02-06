<template>
    <div class="m-4">
        <div class="mb-4 border-b pb-4">
            <div class="md:flex md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                        Orders
                    </h2>
                </div>
            </div>
        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <ul class="divide-y divide-gray-200">
                <li v-for="order in $store.getters.orders" :key="order.id">
                    <order-row :order="order" />
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
import { CalendarIcon, ChevronRightIcon } from '@heroicons/vue/solid'
import OrderRow from "./components/OrderRow";


export default {
    components: {
        CalendarIcon,
        ChevronRightIcon,
        OrderRow,
    },
}
</script>
