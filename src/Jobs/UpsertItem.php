<?php

namespace Spork\Shopping\Jobs;

use Spork\Shopping\Models\StoreItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Str;

class UpsertItem implements ShouldQueue
{
    use Queueable;

    public $item;

    public function __construct($item)
    {
        $this->item = $item;
    }

    public function handle(): void
    {
        $item = StoreItem::firstWhere('product_id', $this->item['data']['id']);

        $data = [
            'product_id' => $this->item['data']['id'],
            'name' => $this->item['value'],
            // stock state?
            'is_in_stock' => $this->item['data']['isBuyable'],
            // Maybe if it's any local store?
            'is_available_in_local_store' => collect(collect($this->item['data']['facets'])->where('name', 'availableInStores')->first()['values'])->contains(257),
            'price' => $this->item['data']['discountSalePriceValue'],
            // How the item itself is measured (oz, each, etc...)
            'unit' => $this->item['data']['priceUnit'],
            // An array of items.
            'ingredients' => Str::of($this->item['data']['ingredients'] ?? '')
                ->explode(',')
                ->map(fn ($part) => ucfirst(strtolower(trim(trim($part, '*.')))))
                ->filter(),
            'image_url' => $this->item['data']['image_url'],
            'is_alcohol' => $this->item['data']['isAlcohol'],
            'is_home_delivery_available' => !$this->item['data']['homeDeliveryNotAvailable'],
            'store' => 'meijer',
            'manufacturer' => $this->item['data']['manufacturerName'] ?? null,
        ];

        foreach ($data as $key => $datum) {
            if (is_string($datum)) {
                $data[$key] = Str::replace([" ", "\t", "\n", "\r", "\0", "\x0B", " "], ' ', $datum);
            }
        }

        if (empty($item)) {
            StoreItem::create($data);
            return;
        }

        $item->update($data);

    }
}
