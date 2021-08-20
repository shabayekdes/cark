<?php

namespace App\Imports;

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsImport implements ToCollection, WithHeadingRow
{
    use Importable;

    protected $brand;

    public function __construct($brand)
    {
        $this->brand = $brand;
    }

    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $key => $row) {

            $slug = Str::slug($row["product_title"], '-') . Str::random(10);

            $date = [
                "post_author" => $row["product_author"],
                "post_content" => $row["product_content"] != null ? $row["product_content"] : '',
                "post_excerpt" => $row["product_excerpt"] != null ? $row["product_excerpt"] : '',
                "post_title" => $row["product_title"],
                "post_type" => 'product',
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
                "guid" => "https://upload.cark-egypt.com/{$slug}",
            ];

            $productCreated = Product::create($date);
            // dd($productCreated);

            $meta = $this->productMetaInsert($row);

            // dd($meta);

            $productCreated->meta()->createMany($meta);
            $years = explode(",", $row['years']);
            $categories = explode(",", $row['categories']);
            // $termTaxonomy = [...$categories, ...$years]; //php7.4
            $termTaxonomy = array_unique(array_merge($years, $categories));
            if (!empty($row['attributes'])) {
                $termAttribute = explode(",", $row['attributes']);
            }else {
                $termAttribute = [];
            }

            $termTaxonomy[] = 2;
            $termTaxonomy[] = $this->brand;
            $termTaxonomy[] = $row['model'];

            $result = array_unique(array_merge($termTaxonomy, $termAttribute));

            $termTaxonomyIds = array_fill_keys($result, ['term_order' => 0]);

            DB::table('term_taxonomy')->whereIn('term_taxonomy_id', $result)->increment('count');

            $productCreated->term()->attach($termTaxonomyIds);

            $slug = Str::slug($row["thumb_title"], '-') . Str::random(10);

            $date = [
                "post_author" => $row["product_author"],
                "post_content" => $row["thumb_content"] != null ? $row["thumb_content"] : '',
                "post_title" => $row["thumb_title"],
                "post_excerpt" => $row["thumb_excerpt"] != null ? $row["thumb_excerpt"] : '',
                "post_type" => 'attachment',
                "post_date" => now(),
                "post_date_gmt" => now(),
                "post_modified" => now(),
                "post_modified_gmt" => now(),
                "post_name" => $slug,
                "to_ping" => "",
                "pinged" => "",
                "comment_status" => "closed",
                "ping_status" => "closed",
                "post_status" => "inherit",
                "post_mime_type" => $row["thumb_mime_type"],
                "post_content_filtered" => "",
                "guid" => "https://upload.cark-egypt.com/{$slug}",
            ];

            $thumbCreated = Product::create($date);

            $metadata = [
                "width" => 800,
                "height" => 800,
                "file" => $row['wp_attached_file']
              ];

            $thumbMeta = [
                [
                    "meta_key" => "_wp_attached_file",
                    "meta_value" => $row['wp_attached_file'],
                ],
                [
                    "meta_key" => "_wp_attachment_metadata",
                    "meta_value" => serialize($metadata)
                ]
            ];
            $thumbCreated->meta()->createMany($thumbMeta);
            $productCreated->meta()->create([
                "meta_key" => "_thumbnail_id",
                "meta_value" => $thumbCreated->ID,
            ]);

        }
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
                "meta_value" => $product['regular_price'],
            ],
            [
                "meta_key" => "_sale_price",
                "meta_value" => $product['sale_price'] != null ? $product['sale_price'] : '',
            ],
            [
                "meta_key" => "_virtual",
                "meta_value" => $product['virtual'] == 0 ? 'no' : 'yes',
            ],
            [
                "meta_key" => "_price",
                "meta_value" => $product['regular_price'] != null ? $product['regular_price'] : '',
            ]
        ];

        if (!empty($product['sale_price_dates_from']) && !empty($product['sale_price_dates_to'])) {
            $meta[] = [
                "meta_key" => "sale_price_dates_from",
                "meta_value" => strtotime($product['sale_price_dates_from']),
            ];
            $meta[] = [
                "meta_key" => "sale_price_dates_to",
                "meta_value" => strtotime($product['sale_price_dates_to']),
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
                "meta_key" => "product_attributes",
                "meta_value" => serialize($productAttributes->all()),
            ];
        }

        return $meta;
    }
}
