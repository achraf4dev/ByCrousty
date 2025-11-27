<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            "El Toque Final" => [
                ["name" => "Nuggets", "description" => "3 trozos de pollo empanados estilo Crousty.", "price_eur" => 2.86, "points" => 29],
                ["name" => "Crousty Burger", "description" => "Hamburguesa de pollo empanizado al estilo Crousty, jugosa y crujiente, con salsa Tasty y queso cheddar fundido.", "price_eur" => 4.50, "points" => 45],
                ["name" => "Patatas Fritas", "description" => "Papas fritas doradas y crujientes recién hechas, sazonadas con sal al punto.", "price_eur" => 3.00, "points" => 30],
            ],
            "Crousty Combos" => [
                ["name" => "Combo Crousty Grande", "description" => "Incluye bebida y el 'El Og' grande (arroz blanco con pollo empanizado al estilo Crousty y salsa tailandesa casera).", "price_eur" => 15.00, "points" => 150],
                ["name" => "Crousty Adicción Grande", "description" => "Incluye bebida, postre y el 'El Og' grande (plato de arroz con pollo empanizado estilo Crousty).", "price_eur" => 16.00, "points" => 160],
                ["name" => "Combo del Pueblo", "description" => "Incluye 'El Og' pequeño, la hamburguesa Crousty Smash, bebida y patatas fritas.", "price_eur" => 13.00, "points" => 130],
            ],
            "Postres" => [
                ["name" => "Tiramisú", "description" => "Capas suaves de crema batida y bizcocho Crousty con un toque crujiente; un postre que te abraza en cada cucharada.", "price_eur" => 5.10, "points" => 51],
                ["name" => "Tarta Daim", "description" => "Tarta de chocolate estilo Daim con caramelo crujiente.", "price_eur" => 4.00, "points" => 40],
                ["name" => "Tiramisú Nubi", "description" => "Tiramisú cremoso en capas, el toque dulce del menú.", "price_eur" => 3.50, "points" => 35],
            ],
            "Bebidas" => [
                ["name" => "Coca Cola", "description" => "Refresco de cola clásico con gas (330 ml).", "price_eur" => 1.90, "points" => 19],
                ["name" => "Capri Sun", "description" => "Bebida de zumo de frutas envasada en bolsa (200 ml).", "price_eur" => 1.90, "points" => 19],
                ["name" => "Coca Cola Cherry", "description" => "Refresco de cola con sabor a cereza (330 ml).", "price_eur" => 1.90, "points" => 19],
            ],
        ];

        foreach ($data as $catName => $products) {
            $category = Category::firstOrCreate([
                'name' => $catName
            ], [
                'description' => $catName,
                'status' => 'active',
            ]);

            foreach ($products as $prod) {
                Product::firstOrCreate([
                    'name' => $prod['name'],
                    'category_id' => $category->id,
                ], [
                    'description' => $prod['description'],
                    'price' => $prod['price_eur'],
                    'points' => $prod['points']*10,
                    'status' => 'active',
                ]);
            }
        }
    }
}
