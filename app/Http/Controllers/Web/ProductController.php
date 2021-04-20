<?php

namespace App\Http\Controllers\Web;

use App\Models\Product;
use App\Models\ProductMeta;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

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
        // dd($request->all());
        if ($request->has('product')) {
            $products   =  array_map(function($data) {
                return str_getcsv(mb_convert_encoding($data, 'UTF-8'), ",");
            }, file($request->product));

            dd($products);
            $headerProduct = array_map('trim', $products[0]);

            unset($products[0]);

            foreach ($products as $product) {

                $productCombine = array_combine($headerProduct, $product);
                $productFiltered = array_filter($productCombine);

                $sliceProduct = Arr::only($productFiltered, ['post_author', 'post_content', 'post_title', 'post_excerpt']);
                $productUpdated = Product::where('ID', $productCombine["﻿ID"])->first();
                
                $productUpdated->update($sliceProduct);

                if (!empty($productFiltered['_sale_price_dates_from'])) {
                    $productFiltered['_sale_price_dates_from'] = strtotime($productFiltered['_sale_price_dates_from']);
                }

                if (!empty($productFiltered['_sale_price_dates_to'])) {
                    $productFiltered['_sale_price_dates_to'] = strtotime($productFiltered['_sale_price_dates_to']);
                }

                $sliceProductMetas = Arr::only($productFiltered, ['_regular_price', '_sale_price', '_virtual', '_price', '_sale_price_dates_from', '_sale_price_dates_to']);

                foreach ($sliceProductMetas as $key => $value) {
                    ProductMeta::where('post_id', $productCombine["﻿ID"])->where('meta_key', $key)->update([
                        'meta_value' => $value
                    ]);
                }

                if(isset($productFiltered['term_taxonomy_id'])){
                    $termTaxonomy = explode(",", $productCombine['term_taxonomy_id']);
                    $termTaxonomy[] = 2;
                    $termTaxonomyIds = array_fill_keys($termTaxonomy, ['term_order' => 0]);
                    $termIds = $productUpdated->term()->get()->pluck('term_id')->toArray();
                    DB::table('wca_term_taxonomy')->whereIn('term_taxonomy_id', $termIds)->where('count', '!=', 0)->decrement('count');

                    $productUpdated->term()->sync($termTaxonomyIds);
                    DB::table('wca_term_taxonomy')->whereIn('term_taxonomy_id', $termTaxonomy)->increment('count');

                }
            }

            return 'Done';
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
