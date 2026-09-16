<?php

namespace App\Services;

use App\Jobs\SendOrderConfirmationJob;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class OrderService
{
    public function createOrder(array $customerData, array $items): Order
    {
        return DB::transaction(function () use ($customerData, $items) {
            $customer = Customer::firstOrCreate(
                ['email' => $customerData['email']],
                ['name' => $customerData['name']]
            );

            $productQuantities = collect($items)
                ->groupBy('product_id')
                ->map(fn($group) => $group->sum('quantity'));

            $productIds = $productQuantities->keys()->sort()->values()->toArray();

            $products = Product::whereIn('id', $productIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $subtotal = 0;
            $taxTotal = 0;
            $orderItemsData = [];

            foreach ($productQuantities as $productId => $quantity) {
                $product = $products->get($productId);

                if (!$product || $product->stock_on_hand < $quantity) {
                    throw new RuntimeException("Insufficient stock for product: " . ($product?->name ?? "ID {$productId}"));
                }

                $product->decrement('stock_on_hand', $quantity);

                $lineSubtotal = round($product->price_per_unit * $quantity, 2);
                $lineTax = round($lineSubtotal * ($product->tax_percentage / 100), 2);
                $lineTotal = round($lineSubtotal + $lineTax, 2);

                $subtotal += $lineSubtotal;
                $taxTotal += $lineTax;

                $orderItemsData[] = [
                    'product_id' => $product->id,
                    'quantity'   => $quantity,
                    'unit_price' => $product->price_per_unit,
                    'tax_amount' => $lineTax,
                    'line_total' => $lineTotal,
                ];
            }

            $order = Order::create([
                'customer_id' => $customer->id,
                'subtotal'    => $subtotal,
                'tax_total'   => $taxTotal,
                'grand_total' => round($subtotal + $taxTotal, 2),
            ]);

            $order->items()->createMany($orderItemsData);

            SendOrderConfirmationJob::dispatch($order);

            return $order->load(['customer', 'items.product']);
        });
    }
}
