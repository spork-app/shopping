export default {
    state: {
        lists: [],
        orders: [],
        cart: {},
        query: '',
        items: [],
        itemsOrder: [],
        page: 1,
        next_page_url: '/api/shopping-search',
    },
    getters: {
        lists: (state) => state.lists,
        orders: (state) => state.orders,
        cart: (state) => state.cart,
        searchQuery: (state) => state.query,
        storeItems: (state) => state.items,
        storePage: (state) => state.page,
    },
    mutations: {
        query(state, query) {
            state.query = query;
        },
        establishTheStore(state, featureLists) {
            state.lists = featureLists.filter(item => item.settings.type === 'list');
            state.orders = featureLists.filter(item => item.settings.type === 'order');
            state.cart = featureLists.filter(item => item.settings.type === 'cart')[0];
        },
        incrementPage(state) {
            state.page ++;
        }
    },
    actions: {
        async getShoppingLists({ commit, state }) {
            const { data: { data } } = await axios.get(buildUrl('/api/feature-list', {
                filter: {
                    feature: 'shopping',
                },
            }));

            commit('establishTheStore', data);
        },

        addItemToCart({ state, dispatch }, item) {
            const ids = state.cart?.settings?.items?.map(itemInCart => itemInCart.id);
            if (ids.includes(item.id)) {
                state.cart.settings.items = state.cart.settings.items.map((itemInCart) => {
                    if (item.id === itemInCart.id) {
                        itemInCart.count++;
                    }

                    return itemInCart;
                });
            } else {
                if (!state.cart.settings.items) {
                    state.cart.settings = {
                        items: [],
                    }
                }

                state.cart.settings?.items?.push(item);
            }

            dispatch('updateCart')
        },
        removeItemToCart({ state, dispatch }, item) {
            const ids = state.cart.settings.items.map(itemInCart => itemInCart.id);
            if (!ids.includes(item.id)) {
                return;
            }

            for (const index in state.cart.settings.items) {
                const itemInCart = state.cart.settings.items[index];

                if (!itemInCart) {
                    continue;
                }

                if (itemInCart.id !== item.id) {
                    continue;
                }

                itemInCart.count --;

                if (itemInCart.count <= 0) {
                    state.cart.settings.items.splice(index, 1);
                }
            }

            dispatch('updateCart')
        },
        async queryStore({ getters, state }) {
            const { data: { data, next_page_url } } = await axios.get(buildUrl(state.next_page_url, {
                query: getters.searchQuery,
                page: state.page
            }))

            const idOrder = data.map(item => item.id);
            state.itemsOrder = state.itemsOrder.concat(idOrder).unique();

            const reduced = state.items.concat(data).reduce((items, item) => ({
                ...items,
                [item.id]: item,
            }), {});

            console.log(reduced)
            state.items = state.itemsOrder.map((id) => reduced[id]);
        },
        async updateCart({ getters }) {
            const { id, ...data } = getters.cart;
            await axios.put('/api/feature-list/'+ id, {
                name: data.name,
                settings: data.settings,
            })
        }
    },
};

