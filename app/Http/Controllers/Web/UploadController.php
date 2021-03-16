<?php

namespace App\Http\Controllers\Web;

use App\Models\Product;
use App\Models\ProductMeta;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class UploadController extends Controller
{

    /**
     * Create form upload
     *
     * @return void
     */
    public function create()
    {
        return view('product.upload');
    }

    /**
     * Store products from csv
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        $request->validate([
            'product' => 'required|file',
            'thumb' => 'required|file',
        ]);

        if ($request->has('product') || $request->has('thumb')) {
            $products   =  array_map(function($data) {
                return str_getcsv(mb_convert_encoding($data, 'UTF-8'), ",");
            }, file($request->product));
            $thumbArr   =  array_map('str_getcsv', file($request->thumb));


            $headerProduct = array_map('trim', $products[0]);
            $headerThumb = array_map('trim', $thumbArr[0]);

            unset($products[0]);
            unset($thumbArr[0]);

            $productsCount = count($products) / 2;
            if($productsCount != $thumbArr){
            return redirect()
                        ->back()
                        ->with('error', 'Products not equal thumb count!');
            }

            // dd(array_chunk($data,2));

            $chunkProducts = array_chunk($products,2);
            for ($i=0; $i < count($chunkProducts); $i++) { 

                $trid = DB::table('wca_icl_translations')->max('trid');
                $trid++;
                $productIds = [];
                foreach ($chunkProducts[$i] as $product) {
                    $productCombine = array_combine($headerProduct, $product);
                    // dd($productCombine);

                    $result = $this->productInsert($productCombine);
                    $productCreated = Product::create($result);

                    $productIds[] = $productCreated->ID;

                    $meta = $this->productMetaInsert($productCombine);

                    // dd($meta);

                    $productCreated->meta()->createMany($meta);

                    $termTaxonomy = explode(",", $productCombine['term_taxonomy_id']);
                    if (!empty($productCombine['attributes'])) {
                        $termAttribute = explode(",", $productCombine['attributes']);
                    }else {
                        $termAttribute = [];
                    }

                    $termTaxonomy[] = 2;
                    $result = array_unique(array_merge($termTaxonomy, $termAttribute));
                    $termTaxonomyIds = array_fill_keys($result, ['term_order' => 0]);

                    $productCreated->term()->attach($termTaxonomyIds);
                }

                $thumbData = array_values($thumbArr)[$i];
                $thumbData = array_combine($headerThumb, $thumbData);

                $result = $this->productInsert($thumbData);
                $result['post_status'] = "inherit";
                $result['post_mime_type'] = $thumbData['post_mime_type'];

                $thumbCreated = Product::create($result);

                $thumbMeta = [
                    [
                        "meta_key" => "_wp_attached_file",
                        "meta_value" => $thumbData['_wp_attached_file'],
                    ],
                    [
                        "meta_key" => "_wp_attachment_metadata",
                        "meta_value" => "",
                    ]
                ];
                $thumbCreated->meta()->createMany($thumbMeta);

                ProductMeta::insert([
                    [
                        "post_id" => $productIds[0],
                        "meta_key" => "_thumbnail_id",
                        "meta_value" => $thumbCreated->ID,
                    ],
                    [
                        "post_id" => $productIds[1],
                        "meta_key" => "_thumbnail_id",
                        "meta_value" => $thumbCreated->ID,
                    ]
                ]);

                $trid = DB::table('wca_icl_translations')->insert([
                    [
                        'element_type' => 'post_product',
                        'element_id' => $productIds[0],
                        'trid' => $trid,
                        'language_code' => 'en',
                        'source_language_code' => null 
                    ],
                    [
                        'element_type' => 'post_product',
                        'element_id' => $productIds[1],
                        'trid' => $trid,
                        'language_code' => 'ar',
                        'source_language_code' => 'en' 
                    ]
                ]);


            }

            return redirect()
                        ->back()
                        ->with('success', 'Products created successfully!');
        }
    }

    /**
     * Create form upload
     *
     * @return void
     */
    public function edit()
    {
        return view('product.edit');
    }

    /**
     * Store products from csv
     *
     * @param Request $request
     * @return void
     */
    public function update(Request $request)
    {
        $request->validate(['product' => 'required|file']);

        if ($request->has('product')) {
            $products   =  array_map(function($data) {
                return str_getcsv(mb_convert_encoding($data, 'UTF-8'), ",");
            }, file($request->product));

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

                    $productUpdated->term()->sync($termTaxonomyIds);
                }
            }

            return 'Done';
        }

        return 'please upload file';
    }

    /**
     * Filter product to insert into database
     *
     * @param array $productData
     * @return void
     */
    protected function productInsert($productData)
    {
        $sliceProduct = Arr::only($productData, [
                "post_author",
                "post_content",
                "post_title",
                "post_excerpt",
                "post_type",
            ]);

        $slug = Str::slug($sliceProduct["post_title"], '-');

        $date = [
            "post_date" => now(),
            "post_date_gmt" => now(),
            "post_modified" => now(),
            "post_modified_gmt" => now(),
            "post_name" => $slug,
            "to_ping" => "",
            "pinged" => "",
            "comment_status" => "closed",
            "ping_status" => "closed",
            "post_content_filtered" => "",
            "guid" => "https://recruitment.talentsmine.net/job/{$slug}",
        ];  

        return array_merge($sliceProduct, $date);
    }
    
    /**
     * Filter product meta to insert into database
     *
     * @param array $productData
     * @return void
     */
    protected function productMetaInsert($product)
    {

        $meta = [
            [
                "meta_key" => "_edit_lock",
                "meta_value" => "1",
            ],
            [
                "meta_key" => "_edit_last",
                "meta_value" => "1",
            ],
            [
                "meta_key" => "_tax_status",
                "meta_value" => "taxable",
            ],
            [
                "meta_key" => "_tax_class",
                "meta_value" => "",
            ],
            [
                "meta_key" => "_manage_stock",
                "meta_value" => "no",
            ],
            [
                "meta_key" => "_backorders",
                "meta_value" => "no",
            ],
            [
                "meta_key" => "_sold_individually",
                "meta_value" => "no",
            ],
            [
                "meta_key" => "_downloadable",
                "meta_value" => "no",
            ],
            [
                "meta_key" => "_download_limit",
                "meta_value" => "17",
            ],
            [
                "meta_key" => "_stock_status",
                "meta_value" => "instock",
            ],
            [
                "meta_key" => "_download_expiry",
                "meta_value" => "70",
            ],
            [
                "meta_key" => "_wc_average_rating",
                "meta_value" => "0",
            ],
            [
                "meta_key" => "_wc_review_count",
                "meta_value" => "0",
            ],
            [
                "meta_key" => "_product_version",
                "meta_value" => "5.0.0",
            ],
            [
                "meta_key" => "_stock",
                "meta_value" => "NULL",
            ],
            [
                "meta_key" => "pageview",
                "meta_value" => "1",
            ],
            [
                "meta_key" => "_wc_review_count",
                "meta_value" => "0",
            ],
            [
                "meta_key" => "total_sales",
                "meta_value" => "0",
            ],
            [
                "meta_key" => "_regular_price",
                "meta_value" => $product['_regular_price'],
            ],
            [
                "meta_key" => "_sale_price",
                "meta_value" => $product['_sale_price'],
            ],
            [
                "meta_key" => "_virtual",
                "meta_value" => $product['virtual'] == 0 ? 'no' : 'yes',
            ],
            [
                "meta_key" => "_price",
                "meta_value" => $product['_regular_price'],
            ]
        ];

        if (!empty($product['_sale_price_dates_from']) && !empty($product['_sale_price_dates_to'])) {
            $meta[] = [
                "meta_key" => "_sale_price_dates_from",
                "meta_value" => strtotime($product['_sale_price_dates_from']),
            ];
            $meta[] = [
                "meta_key" => "_sale_price_dates_to",
                "meta_value" => strtotime($product['_sale_price_dates_to']),
            ];
        }

        if (!empty($product['product_attributes'])) {
            $productAttributes = explode(",", $product['product_attributes']);
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
            
            $meta[] = [
                "meta_key" => "_product_attributes",
                "meta_value" => serialize($productAttributes->all()),
            ];
        }

        return $meta;
    }

}
