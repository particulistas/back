<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            'Local y nave' => ['local', 'nave'],
            'Habitaciones' => ['Habitaciones'],
            'Garage' => ['Garage'],
            'Oficinas' => ['Oficinas'],
            'Trastero' => ['Trastero'],
            'Vivienda' => ['Piso', 'Ático', 'Casa', 'Chalet', 'Duplex', 'Loft', 'Apartamento', 'Estudio', 'Casa', 'Chalet'],
        ];

        foreach ($categories as $parent => $children) {
            $parentCategory = Category::create(['name' => $parent, 'parent_id' => null]);

            foreach ($children as $child) {
                Category::create(['name' => $child, 'parent_id' => $parentCategory->id]);
            }
        }
    }
}

