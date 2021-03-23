<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {

      $test = [
        "width" => 800,
        "height" => 800
        // "file" => "2021/03/تيل-الفرامل.jpg"
      ];

      dd(serialize($test));
      dd(unserialize('a:5:{s:5:"width";i:800;s:6:"height";i:800;s:4:"file";s:33:"2021/03/تيل-الفرامل.jpg";s:5:"sizes";a:19:{s:6:"medium";a:4:{s:4:"file";s:33:"تيل-الفرامل-300x300.jpg";s:5:"width";i:300;s:6:"height";i:300;s:9:"mime-type";s:10:"image/jpeg";}s:9:"thumbnail";a:4:{s:4:"file";s:33:"تيل-الفرامل-150x150.jpg";s:5:"width";i:150;s:6:"height";i:150;s:9:"mime-type";s:10:"image/jpeg";}s:12:"medium_large";a:4:{s:4:"file";s:33:"تيل-الفرامل-768x768.jpg";s:5:"width";i:768;s:6:"height";i:768;s:9:"mime-type";s:10:"image/jpeg";}s:29:"chromium-recent-posts-thumb-s";a:4:{s:4:"file";s:33:"تيل-الفرامل-160x190.jpg";s:5:"width";i:160;s:6:"height";i:190;s:9:"mime-type";s:10:"image/jpeg";}s:29:"chromium-recent-posts-thumb-m";a:4:{s:4:"file";s:33:"تيل-الفرامل-225x267.jpg";s:5:"width";i:225;s:6:"height";i:267;s:9:"mime-type";s:10:"image/jpeg";}s:31:"chromium-recent-posts-thumb-top";a:4:{s:4:"file";s:33:"تيل-الفرامل-384x216.jpg";s:5:"width";i:384;s:6:"height";i:216;s:9:"mime-type";s:10:"image/jpeg";}s:18:"chromium-gallery-s";a:4:{s:4:"file";s:33:"تيل-الفرامل-384x216.jpg";s:5:"width";i:384;s:6:"height";i:216;s:9:"mime-type";s:10:"image/jpeg";}s:18:"chromium-gallery-m";a:4:{s:4:"file";s:33:"تيل-الفرامل-640x360.jpg";s:5:"width";i:640;s:6:"height";i:360;s:9:"mime-type";s:10:"image/jpeg";}s:18:"chromium-gallery-l";a:4:{s:4:"file";s:33:"تيل-الفرامل-800x576.jpg";s:5:"width";i:800;s:6:"height";i:576;s:9:"mime-type";s:10:"image/jpeg";}s:18:"chromium-grid-blog";a:4:{s:4:"file";s:33:"تيل-الفرامل-350x350.jpg";s:5:"width";i:350;s:6:"height";i:350;s:9:"mime-type";s:10:"image/jpeg";}s:20:"chromium-grid-blog-m";a:4:{s:4:"file";s:33:"تيل-الفرامل-600x600.jpg";s:5:"width";i:600;s:6:"height";i:600;s:9:"mime-type";s:10:"image/jpeg";}s:14:"post-thumbnail";a:4:{s:4:"file";s:33:"تيل-الفرامل-800x350.jpg";s:5:"width";i:800;s:6:"height";i:350;s:9:"mime-type";s:10:"image/jpeg";}s:21:"woocommerce_thumbnail";a:4:{s:4:"file";s:33:"تيل-الفرامل-300x300.jpg";s:5:"width";i:300;s:6:"height";i:300;s:9:"mime-type";s:10:"image/jpeg";}s:18:"woocommerce_single";a:4:{s:4:"file";s:33:"تيل-الفرامل-600x600.jpg";s:5:"width";i:600;s:6:"height";i:600;s:9:"mime-type";s:10:"image/jpeg";}s:29:"woocommerce_gallery_thumbnail";a:4:{s:4:"file";s:33:"تيل-الفرامل-100x100.jpg";s:5:"width";i:100;s:6:"height";i:100;s:9:"mime-type";s:10:"image/jpeg";}s:12:"shop_catalog";a:4:{s:4:"file";s:33:"تيل-الفرامل-300x300.jpg";s:5:"width";i:300;s:6:"height";i:300;s:9:"mime-type";s:10:"image/jpeg";}s:11:"shop_single";a:4:{s:4:"file";s:33:"تيل-الفرامل-600x600.jpg";s:5:"width";i:600;s:6:"height";i:600;s:9:"mime-type";s:10:"image/jpeg";}s:14:"shop_thumbnail";a:4:{s:4:"file";s:33:"تيل-الفرامل-100x100.jpg";s:5:"width";i:100;s:6:"height";i:100;s:9:"mime-type";s:10:"image/jpeg";}s:28:"dgwt-wcas-product-suggestion";a:4:{s:4:"file";s:31:"تيل-الفرامل-64x64.jpg";s:5:"width";i:64;s:6:"height";i:64;s:9:"mime-type";s:10:"image/jpeg";}}s:10:"image_meta";a:12:{s:8:"aperture";s:1:"0";s:6:"credit";s:0:"";s:6:"camera";s:0:"";s:7:"caption";s:0:"";s:17:"created_timestamp";s:1:"0";s:9:"copyright";s:0:"";s:12:"focal_length";s:1:"0";s:3:"iso";s:1:"0";s:13:"shutter_speed";s:1:"0";s:5:"title";s:0:"";s:11:"orientation";s:1:"1";s:8:"keywords";a:0:{}}}'));
      $product = Product::find(8916);
        dd($product->term()->get()->pluck('term_id')->toArray());
      $taxonomy = DB::table('wca_term_taxonomy')->whereIn('term_taxonomy_id', [1,2])->where('count', '!=', 0)->decrement('count');
      dd($taxonomy);


        $product->term()->sync([ 2 => ['term_order' => 0]]);

        dd($product);

        $productAttributes = explode(",", 'pa_car-type,pa_parts-type,pa_сar-brand');

        $productAttributes = collect($productAttributes)->mapWithKeys(function ($item, $key) {
            return [$item => [
                'name' => $item,
                "value" => "",
                "position" => $key,
                "is_visible" => 1,
                "is_variation" => 0,
                "is_taxonomy" => 1,
            ]];
        });

      dd($productAttributes->all());
        // dd(strtotime('3/27/2012'));
        // $price = DB::table('wp_icl_translations')->max('trid');
        // dd($price);
        $attacgment = unserialize('a:3:{s:11:"pa_car-type";a:6:{s:4:"name";s:11:"pa_car-type";s:5:"value";s:0:"";s:8:"position";i:0;s:10:"is_visible";i:1;s:12:"is_variation";i:0;s:11:"is_taxonomy";i:1;}s:13:"pa_parts-type";a:6:{s:4:"name";s:13:"pa_parts-type";s:5:"value";s:0:"";s:8:"position";i:1;s:10:"is_visible";i:1;s:12:"is_variation";i:0;s:11:"is_taxonomy";i:1;}s:17:"pa_%d1%81ar-brand";a:6:{s:4:"name";s:13:"pa_сar-brand";s:5:"value";s:0:"";s:8:"position";i:2;s:10:"is_visible";i:1;s:12:"is_variation";i:0;s:11:"is_taxonomy";i:1;}}');

        dd($attacgment);

        $txt = [
            "pa_color" => [
            "name" => "pa_color",
            "value" => "",
            "position" => 0,
            "is_visible" => 0,
            "is_variation" => 1,
            "is_taxonomy" => 1,
            ],
          "size" => [
            "name" => "Size",
            "value" => "14 | 15 | 16",
            "position" => 1,
            "is_visible" => 1,
            "is_variation" => 1,
            "is_taxonomy" => 0,
          ],
          "pa_%d1%81ar-brand" => [
            "name" => "pa_сar-brand",
            "value" => "",
            "position" => 3,
            "is_visible" => 1,
            "is_variation" => 1,
            "is_taxonomy" => 1,
          ]
          ];

          dd(serialize($txt));
    }
}
