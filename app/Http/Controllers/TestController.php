<?php

namespace App\Http\Controllers;

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
        // dd(strtotime('3/27/2012'));
        // $price = DB::table('wp_icl_translations')->max('trid');
        // dd($price);
        $attacgment = unserialize('a:4:{s:8:"pa_color";a:6:{s:4:"name";s:8:"pa_color";s:5:"value";s:0:"";s:8:"position";i:0;s:10:"is_visible";i:0;s:12:"is_variation";i:1;s:11:"is_taxonomy";i:1;}s:4:"size";a:6:{s:4:"name";s:4:"Size";s:5:"value";s:15:"14" | 15" | 16"";s:8:"position";i:1;s:10:"is_visible";i:1;s:12:"is_variation";i:1;s:11:"is_taxonomy";i:0;}s:13:"pa_parts-type";a:6:{s:4:"name";s:13:"pa_parts-type";s:5:"value";s:0:"";s:8:"position";i:2;s:10:"is_visible";i:1;s:12:"is_variation";i:0;s:11:"is_taxonomy";i:1;}s:17:"pa_%d1%81ar-brand";a:6:{s:4:"name";s:13:"pa_сar-brand";s:5:"value";s:0:"";s:8:"position";i:3;s:10:"is_visible";i:1;s:12:"is_variation";i:1;s:11:"is_taxonomy";i:1;}}');

        // dd($attacgment);

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
