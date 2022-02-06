## Shopping

Simply add to your spork app through composer!

```
composer require spork/shopping
```

Publish your assets

```
php artisan vendor:publish --provider=Spork\\Shopping\\ShoppingServiceProvider
```

You'll need to run `artisan migrate` to ensure your database gets the new repeating events schema

Lastly, register the Service Provider in your Spork App's `config/app.php` file. That will automatically add the Shopping entry to the menu.