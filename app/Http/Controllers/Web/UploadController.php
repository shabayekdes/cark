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
    public function index()
    {
        return view('upload-file');
    }

    public function store()
    {
        if (request()->has('mycsv')) {
            $products   =  array_map(function($data) {
                return str_getcsv(mb_convert_encoding($data, 'UTF-8'), ",");
            }, file(request()->mycsv));
            // $thumbArr   =  array_map('str_getcsv', file(request()->thumb));

            dd($products);
            $headerProduct = array_map('trim', $products[0]);
            $headerThumb = array_map('trim', $thumbArr[0]);

            unset($products[0]);
            unset($thumbArr[0]);

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

                    $productCreated->meta()->createMany($meta);


                    $termTaxonomy = explode(",", $productCombine['term_taxonomy_id']);
                    $termTaxonomyIds = array_fill_keys($termTaxonomy, ['term_order' => 0]);

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

        return $meta;
    }

}
