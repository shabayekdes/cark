<?php

namespace App\Http\Controllers\Web;

use App\Models\Product;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        return view('title.edit');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        if ($request->has('product')) {
            $products   =  array_map(function($data) {
                return str_getcsv(mb_convert_encoding($data, 'UTF-8'), ",");
            }, file($request->product));

            $headerProduct = array_map('trim', $products[0]);

            unset($products[0]);

            foreach ($products as $product) {

                $productCombine = array_combine($headerProduct, $product);
                $productFiltered = array_filter($productCombine);

                $sliceProduct = Arr::only($productFiltered, ['post_title', 'post_excerpt']);
                $productUpdated = Product::where('ID', $productCombine["﻿ID"])->first();

                $productUpdated->update($sliceProduct);

        
            }

            return 'Done';
        }
    }
}
