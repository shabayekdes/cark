<?php

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class ChangePostNameSlugSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $productGroups = Product::where('post_type', 'product')->get()->groupBy('post_name');

        foreach ($productGroups as $group){
            if($group->count() > 1){
                foreach ($group as $product){

                    $product->update([
                        'post_name' => $product->post_name . '-' . Str::random(10),
                    ]);

                }
            }
        }

    }
}
