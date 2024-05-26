<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProductTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();

        $storeQuantity = 10;

        foreach($categories as $category){

            for($i = 1; $i <= $storeQuantity; $i++){
                $product = new Product();
                $product->name = 'Product' . $i;
                $product->stock_quantity = rand(20, 40);
                $product->price = rand(100, 1500);
                $product->category_id = $category->id;
                $product->save();

            }
        }
    }
}
