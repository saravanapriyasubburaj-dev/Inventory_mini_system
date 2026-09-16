<?php

namespace Tests\Feature;

use App\Jobs\SendOrderConfirmationJob;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class OrderCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_order_deducts_stock_and_dispatches_job(): void
    {
        Queue::fake();

        $product = Product::factory()->create([
            'price_per_unit' => 100.00,
            'tax_percentage' => 10.00,
            'stock_on_hand'  => 5,
        ]);

        $payload = [
            'customer' => [
                'name'  => 'Jane Doe',
                'email' => 'jane@example.com',
            ],
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity'   => 2,
                ],
            ],
        ];

        $response = $this->postJson('/api/orders', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.subtotal', '200.00')
            ->assertJsonPath('data.tax_total', '20.00')
            ->assertJsonPath('data.grand_total', '220.00');

        $this->assertDatabaseHas('products', [
            'id'            => $product->id,
            'stock_on_hand' => 3,
        ]);

        Queue::assertPushed(SendOrderConfirmationJob::class);
    }

    public function test_it_fails_cleanly_when_stock_is_insufficient(): void
    {
        Queue::fake();

        $product = Product::factory()->create([
            'stock_on_hand' => 1,
        ]);

        $payload = [
            'customer' => [
                'name'  => 'John Doe',
                'email' => 'john@example.com',
            ],
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity'   => 2,
                ],
            ],
        ];

        $response = $this->postJson('/api/orders', $payload);

        $response->assertStatus(422)
            ->assertJsonStructure(['message']);

        $this->assertDatabaseHas('products', [
            'id'            => $product->id,
            'stock_on_hand' => 1,
        ]);

        Queue::assertNotPushed(SendOrderConfirmationJob::class);
    }
}
