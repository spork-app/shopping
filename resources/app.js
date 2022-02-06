Spork.setupStore({
    Shopping: require("./store").default,
})

Spork.component('store-item', require('./Shopping/components/StoreItem').default);

Spork.routesFor('maintenance', [
    Spork.authenticatedRoute('/shopping', require('./Shopping/Shopping').default, {
        children: [
            Spork.authenticatedRoute('orders', require('./Shopping/PastOrders').default),
            Spork.authenticatedRoute('cart', require('./Shopping/Cart').default),
            Spork.authenticatedRoute('', require('./Shopping/Store').default),
        ]
    }),
]);