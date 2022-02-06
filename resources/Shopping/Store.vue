<template>
    <div class="p-4  overflow-auto">
        <div class="mb-4 border-b pb-4">
            <div class="md:flex md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                        Store
                    </h2>
                </div>
                <div class="mt-4 flex-shrink-0 flex md:mt-0 md:ml-4">
                    <button type="button" class="ml-3 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-400 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Checkout
                    </button>
                </div>
            </div>
        </div>
        <div id="scroll" ref="scrollComponent" @scroll.native="handleScroll" v-if="$store.getters.storeItems.length > 0" role="list" class="grid grid-cols-1 gap-3 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 pb-32">
            <store-item v-for="item in $store.getters.storeItems" :key="'shopping-item-'+item.id" :item="item"></store-item>
        </div>
        <div v-else class="w-full p-4 border bg-white italic rounded">
            No items in the store
        </div>
    </div>
</template>

<script>
import { ShoppingCartIcon, PhoneIcon, ChevronRightIcon, ChevronLeftIcon, TrashIcon } from '@heroicons/vue/solid'
import { useStore } from 'vuex'
import { onMounted, onUnmounted, ref } from "vue";
import StoreItem from './components/StoreItem';

export default {
    name: "Store",
    components: {
        ShoppingCartIcon,
        PhoneIcon,
        ChevronLeftIcon,
        ChevronRightIcon,
        TrashIcon,
        StoreItem,
    },
    setup() {
        const store = useStore();

        function createDebounce() {
            let timeout = null;
            return function (fnc, delayMs) {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    fnc();
                }, delayMs || 100);
            };
        }

        const scrollDebounce = createDebounce();

        const handleScroll = (e) => {
            let element = scrollComponent.value

            if (!element) {
                return;
            }

            if ( element.getBoundingClientRect().bottom <= window.innerHeight+100 ) {
                scrollDebounce(() => {
                    store.commit('incrementPage');
                    store.dispatch('queryStore')
                }, 200)
            }
        }

        onMounted(() => {
            window.addEventListener("scroll", handleScroll)
        })

        onUnmounted(() => {
            window.removeEventListener("scroll", handleScroll)
        })

        console.log("setup")

        const scrollComponent = ref(null)
        return {
            scrollComponent,
            handleScroll
        }

    }
}
</script>
