<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\ShippingMethod;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SetupShippingMethods extends Command
{
    protected $signature = 'app:setup-shipping-methods';

    protected $description = 'Set up shipping methods (Abholung, Versand Schweiz) and attach to all products';

    public function handle(): int
    {
        // Update or create the two required shipping methods
        $pickup = ShippingMethod::find(1);
        if ($pickup) {
            $pickup->update(['name' => 'Abholung', 'price' => 0, 'order' => 0]);
            $this->info("Updated shipping method: Abholung (id={$pickup->id})");
        } else {
            $pickup = ShippingMethod::create(['name' => 'Abholung', 'price' => 0, 'order' => 0]);
            $this->info("Created shipping method: Abholung (id={$pickup->id})");
        }

        $shipping = ShippingMethod::find(5);
        if ($shipping) {
            $shipping->update(['name' => 'Versand (Schweiz)', 'price' => 20, 'order' => 1]);
            $this->info("Updated shipping method: Versand (Schweiz) (id={$shipping->id})");
        } else {
            $shipping = ShippingMethod::create(['name' => 'Versand (Schweiz)', 'price' => 20, 'order' => 1]);
            $this->info("Created shipping method: Versand (Schweiz) (id={$shipping->id})");
        }

        // Remove unused shipping methods (ids 2, 3, 4)
        $unusedIds = ShippingMethod::whereNotIn('id', [$pickup->id, $shipping->id])->pluck('id');

        if ($unusedIds->isNotEmpty()) {
            DB::table('product_shipping_method')->whereIn('shipping_method_id', $unusedIds)->delete();
            ShippingMethod::whereIn('id', $unusedIds)->delete();
            $this->info("Removed {$unusedIds->count()} unused shipping methods");
        }

        // Attach both methods to all products
        $products = Product::all();
        $methodIds = [$pickup->id, $shipping->id];

        foreach ($products as $product) {
            $product->shippingMethods()->syncWithoutDetaching($methodIds);
        }

        $this->info("Attached shipping methods to {$products->count()} products");

        return Command::SUCCESS;
    }
}
