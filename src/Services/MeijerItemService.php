<?php

namespace Spork\Shopping\Services;

use App\Models\FeatureList;
use Spork\Shopping\Jobs\UpsertItem;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MeijerItemService
{
    // I'm just reverse engineering their website... Don't mind me....
    // Theres presently 2 options I understand
    //  - The part after `/search/%s` where %s is the search term
    //  - Then `availableInStores` seems to be a CSV style option which takes the ID of the store.
    //  - ?c= seems to be the client
    //  - ?key= is likely a public key which would be subject to change
    //  - ?sort_by=price, name, relevance
    //  - ?sort_order=ascending,descending

    public const API_URL = 'https://ac.cnstrc.com/search/%s?c=ciojs-client-2.15.0&key=key_GdYuTcnduTUtsZd6&&us=web&page=%s&filters[availableInStores]=%s&sort_by=relevance&sort_order=descending&fmt_options[groups_max_depth]=3&fmt_options[groups_start]=current';

    /**
     * Their cart system works through the use of a UUID set as a cookie "meijer-cart"
     * It seems like this url just needs to be set with the above cookie and `GET` requested.
     */
    public const CART_URL = 'https://www.meijer.com/bin/meijer/cart/userstate';

    protected function getDistanceBetweenPoints($lat1, $lon1, $lat2, $lon2) {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;

        return ($miles * 1.609344) * 1000;
    }

    public function search(string $itemName, int $page = 1): LengthAwarePaginator
    {
        try {
            $primaryProperty = FeatureList::whereJson('$.settings.is_primary', true)->first();

            $stores = array_map(fn($store) => [
                'id' => $store->unitid,
                'name' => $store->storeShortName,
                'address' => $store->streetAddress,
                'city' => $store->city,
                'state' => $store->state,
                'zip' => $store->zip,
                'latitude' => $store->latitude,
                'longitude' => $store->longitude,
                'distance_from_home' => $this->getDistanceBetweenPoints($primaryProperty->latitude, $primaryProperty->longitude, $store->latitude, $store->longitude)
            ], json_decode(file_get_contents(resource_path('meijer_stores.json'))));

            $primaryStore = collect($stores)->sortBy('distance_from_home')->first()['id'] ?? 257;
        } catch (\Exception $exception) {
            $primaryStore = 257;
        }


        $response = cache()->remember('meijer-query.'.$itemName.'.'.$page, now()->addDays(7), fn () => Http::get(sprintf(self::API_URL, $itemName, $page, $primaryStore))->json());

        $data = Arr::get($response, 'response.results');
        $total = Arr::get($response, 'response.total_num_results');

        $formattedData = Collection::make($data)->map(function ($item) use ($primaryStore) {
            dispatch(new UpsertItem($item));
            return [
                'id' => $item['data']['id'],
                'name' => $item['value'],
                // stock state?
                'is_buyable' => $item['data']['isBuyable'],
                // Maybe if it's any local store?
                'is_available_in_local_store' => collect(collect($item['data']['facets'])->where('name', 'availableInStores')->first()['values'])->contains($primaryStore),
                'price' => $item['data']['discountSalePriceValue'],
                // How the item itself is measured (oz, each, etc...)
                'unit' => $item['data']['priceUnit'],
                // An array of items.
                'ingredients' => Str::of($item['data']['ingredients'] ?? '')
                    ->explode(',')
                    ->map(fn ($part) => ucfirst(strtolower(trim(trim($part, '*.')))))
                    ->filter(),
                'image_url' => $item['data']['image_url'],
                'is_alcohol' => $item['data']['isAlcohol'],
                'is_home_delivery_available' => !$item['data']['homeDeliveryNotAvailable'],
                'store' => 'meijer',
                'count' => 1,
            ];
        })->filter(fn ($item) => $item['is_available_in_local_store']);

        return new LengthAwarePaginator($formattedData, $total, 20, $page, [
            'path' => '/' . request()->path()
        ]);
    }
}
