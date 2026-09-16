<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Product;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Customer::factory(10)->create();

        $realProducts = [
            ['name' => 'Dove Beauty Bathing Bar', 'code' => 'PRD-DOVE1', 'price_per_unit' => 65.00, 'tax_percentage' => 18.00, 'stock_on_hand' => 50],
            ['name' => 'Pears Pure & Gentle Soap', 'code' => 'PRD-PEARS', 'price_per_unit' => 55.00, 'tax_percentage' => 18.00, 'stock_on_hand' => 30],
            ['name' => 'Colgate Toothpaste', 'code' => 'PRD-COLG1', 'price_per_unit' => 50.00, 'tax_percentage' => 12.00, 'stock_on_hand' => 15],
            ['name' => 'Parle-G Biscuit', 'code' => 'PRD-PARL1', 'price_per_unit' => 10.00, 'tax_percentage' => 5.00, 'stock_on_hand' => 100],
            ['name' => 'Cinthol Original Soap', 'code' => 'PRD-CINTH', 'price_per_unit' => 40.00, 'tax_percentage' => 18.00, 'stock_on_hand' => 4],
            ['name' => 'Lifebuoy Total 10 Soap', 'code' => 'PRD-LIFE1', 'price_per_unit' => 35.00, 'tax_percentage' => 18.00, 'stock_on_hand' => 45],
            ['name' => 'Medimix Ayurvedic Soap', 'code' => 'PRD-MEDI1', 'price_per_unit' => 45.00, 'tax_percentage' => 18.00, 'stock_on_hand' => 8],
            ['name' => 'Maggi 2-Minute Noodles', 'code' => 'PRD-MAGG1', 'price_per_unit' => 14.00, 'tax_percentage' => 12.00, 'stock_on_hand' => 120],
            ['name' => 'Britannia Good Day Cookies', 'code' => 'PRD-BRIT1', 'price_per_unit' => 20.00, 'tax_percentage' => 12.00, 'stock_on_hand' => 60],
            ['name' => 'Tata Salt (1kg)', 'code' => 'PRD-TATA1', 'price_per_unit' => 25.00, 'tax_percentage' => 5.00, 'stock_on_hand' => 5],
        ];

        foreach ($realProducts as $product) {
            Product::create($product);
        }

        // Generate 15 more random products to keep the total at 25
        Product::factory(15)->create();
    }
}