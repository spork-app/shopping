<template>
    <a href="#" class="block hover:bg-gray-50">
        <div class="px-4 py-4 flex items-center sm:px-6">
            <div class="min-w-0 flex-1 sm:flex sm:items-center sm:justify-between">
                <div class="truncate">
                    <div class="flex text-sm">
                        <p class="font-medium text-indigo-600 truncate">{{ order.name }}</p>
                        <p class="ml-1 flex-shrink-0 font-normal text-gray-500">at {{ stores.join(', ') }}</p>
                    </div>
                    <div class="mt-2 flex">
                        <div class="flex items-center text-sm text-gray-500">
                            <CalendarIcon class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" aria-hidden="true" />
                            <p>
                                Placed on
                                {{ ' ' }}
                                <time :datetime="updatedOn">{{ updatedOn }}</time>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mt-4 flex-shrink-0 sm:mt-0 sm:ml-5">
                    <div class="flex overflow-hidden -space-x-1">
                        <img v-for="item in order.settings.items" :key="item.id" class="inline-block h-6 w-6 rounded-full ring-2 ring-white border border-gray-300" :src="item.image_url" :alt="item.name" />
                    </div>
                </div>
            </div>
            <div class="ml-5 flex-shrink-0">
                <ChevronRightIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
            </div>
        </div>
    </a>
</template>

<script>
import { CalendarIcon, ChevronRightIcon } from '@heroicons/vue/solid'

export default {
    name: "OrderRow",
    props: ['order'],
    components: {
        ChevronRightIcon,
        CalendarIcon,
    },
    computed: {
        stores() {
            return this.order.settings.items.map(item => item.store).unique()
        },
        updatedOn() {
            return dayjs(this.order.updated_at).format('LL')
        }
    }
}
</script>

<style scoped>

</style>
