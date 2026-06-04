<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Product;
use App\Models\Recipe;
use App\Models\RecipeDetail;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CoffeeShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Categories
        $categories = [
            ['name' => 'Coffee', 'type' => 'coffee'],
            ['name' => 'Non-Coffee', 'type' => 'non-coffee'],
            ['name' => 'Snack', 'type' => 'snack'],
            ['name' => 'Dessert', 'type' => 'dessert'],
        ];

        foreach ($categories as $cat) {
            Category::create([
                'name' => $cat['name'],
                'slug' => Str::slug($cat['name']),
                'type' => $cat['type'],
            ]);
        }

        $catCoffee = Category::where('type', 'coffee')->first();
        $catNonCoffee = Category::where('type', 'non-coffee')->first();
        $catSnack = Category::where('type', 'snack')->first();
        $catDessert = Category::where('type', 'dessert')->first();

        // 2. Ingredients
        $ingredients = [
            ['name' => 'Coffee Beans (House Blend)', 'unit' => 'gram', 'stock' => 5000, 'min_stock' => 500],
            ['name' => 'Fresh Milk', 'unit' => 'ml', 'stock' => 10000, 'min_stock' => 1000],
            ['name' => 'Palm Sugar', 'unit' => 'ml', 'stock' => 2000, 'min_stock' => 200],
            ['name' => 'Chocolate Powder', 'unit' => 'gram', 'stock' => 1000, 'min_stock' => 100],
            ['name' => 'Matcha Powder', 'unit' => 'gram', 'stock' => 500, 'min_stock' => 50],
            ['name' => 'Tea Bags', 'unit' => 'pcs', 'stock' => 100, 'min_stock' => 10],
        ];

        foreach ($ingredients as $ing) {
            Ingredient::create($ing);
        }

        $ingBeans = Ingredient::where('name', 'Coffee Beans (House Blend)')->first();
        $ingMilk = Ingredient::where('name', 'Fresh Milk')->first();
        $ingSugar = Ingredient::where('name', 'Palm Sugar')->first();

        // 3. Products
        $products = [
            // Coffee
            [
                'category_id' => $catCoffee->id,
                'name' => 'Espresso',
                'sku' => 'CF-ESP',
                'price' => 15000,
                'cost_price' => 5000,
                'is_active' => true,
                'recipe' => [
                    ['ingredient_id' => $ingBeans->id, 'amount' => 18],
                ]
            ],
            [
                'category_id' => $catCoffee->id,
                'name' => 'Caffe Latte',
                'sku' => 'CF-LAT',
                'price' => 28000,
                'cost_price' => 12000,
                'is_active' => true,
                'recipe' => [
                    ['ingredient_id' => $ingBeans->id, 'amount' => 18],
                    ['ingredient_id' => $ingMilk->id, 'amount' => 200],
                ]
            ],
            [
                'category_id' => $catCoffee->id,
                'name' => 'Aren Latte',
                'sku' => 'CF-ARN',
                'price' => 25000,
                'cost_price' => 10000,
                'is_active' => true,
                'recipe' => [
                    ['ingredient_id' => $ingBeans->id, 'amount' => 18],
                    ['ingredient_id' => $ingMilk->id, 'amount' => 150],
                    ['ingredient_id' => $ingSugar->id, 'amount' => 20],
                ]
            ],
            // Non-Coffee
            [
                'category_id' => $catNonCoffee->id,
                'name' => 'Matcha Latte',
                'sku' => 'NC-MAT',
                'price' => 30000,
                'cost_price' => 15000,
                'is_active' => true,
            ],
            [
                'category_id' => $catNonCoffee->id,
                'name' => 'Iced Chocolate',
                'sku' => 'NC-CHO',
                'price' => 25000,
                'cost_price' => 10000,
                'is_active' => true,
            ],
            // Snacks
            [
                'category_id' => $catSnack->id,
                'name' => 'French Fries',
                'sku' => 'SN-FFR',
                'price' => 20000,
                'cost_price' => 8000,
                'is_active' => true,
            ],
            [
                'category_id' => $catSnack->id,
                'name' => 'Chicken Wings',
                'sku' => 'SN-CWG',
                'price' => 35000,
                'cost_price' => 18000,
                'is_active' => true,
            ],
            // Desserts
            [
                'category_id' => $catDessert->id,
                'name' => 'Croissant Butter',
                'sku' => 'DS-CRB',
                'price' => 22000,
                'cost_price' => 10000,
                'is_active' => true,
            ],
            [
                'category_id' => $catDessert->id,
                'name' => 'Chocolate Brownie',
                'sku' => 'DS-BRN',
                'price' => 25000,
                'cost_price' => 12000,
                'is_active' => true,
            ],
        ];

        foreach ($products as $pData) {
            $recipeData = $pData['recipe'] ?? null;
            unset($pData['recipe']);

            $product = Product::create($pData);

            if ($recipeData) {
                $recipe = Recipe::create([
                    'product_id' => $product->id,
                    'name' => 'Standard Recipe ' . $product->name,
                    'is_active' => true,
                ]);

                foreach ($recipeData as $rd) {
                    RecipeDetail::create([
                        'recipe_id' => $recipe->id,
                        'ingredient_id' => $rd['ingredient_id'],
                        'amount' => $rd['amount'],
                    ]);
                }
            }
        }
    }
}
