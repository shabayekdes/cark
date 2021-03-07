<?php

use App\Models\Term;
use App\Models\Taxonomy;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class VehicleTaxonomySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $terms = array_map(function($data) {
            return str_getcsv(mb_convert_encoding($data, 'UTF-8'), ",");
        }, file('/home/u927736292/domains/cark-egypt.com/public_html/upload/public/upload/model.csv'));

        $items = [];
        foreach ($terms as $key => $term) {
            $model = explode(",", $term[1]);

            $test = array_fill_keys($model, [2000, 2001, 2002, 2003, 2004, 2005, 2006, 2007, 2008, 2009, 2010, 2011, 2012, 2013, 2014, 2015, 2016, 2017, 2018, 2019, 2020, 2021]);

            $items[$term[0]] = $test;
        }
        
        foreach ($items as $model => $item) {
            $slugModel = Str::slug($model, '-');

            $termModel = Term::updateOrCreate([
                    'slug' => $slugModel, 
                ],[
                    'name' => $model,
                    'slug' => $slugModel,
                    'term_group' => 0
                ]);

            Taxonomy::updateOrCreate([
                    'term_id' => $termModel->term_id,
                    'taxonomy' => 'vehicle',
                    'parent' => 0,
                ],[
                    'term_id' => $termModel->term_id,
                    'taxonomy' => 'vehicle',
                    'description' => '',
                    'parent' => 0,
                    'count' => 0,
                ]);

            foreach ($item as $marker => $value) {

                $slugMarker = $slugModel . '-' . Str::slug($marker, '-');

                $termMarker = Term::updateOrCreate([
                        'slug' => $slugMarker, 
                    ],[
                        'name' => $marker,
                        'slug' => $slugMarker,
                        'term_group' => 0
                    ]);
    
                Taxonomy::updateOrCreate([
                        'term_id' => $termMarker->term_id,
                        'taxonomy' => 'vehicle',
                        'parent' => $termModel->term_id,
                    ],[
                        'term_id' => $termMarker->term_id,
                        'taxonomy' => 'vehicle',
                        'description' => '',
                        'parent' => $termModel->term_id,
                        'count' => 0,
                    ]);
                foreach ($value as $year) {
                    $slugYear = $slugMarker . '-' . Str::slug($year, '-');

                    $termYear = Term::updateOrCreate([
                            'slug' => $slugYear, 
                        ],[
                            'name' => $year,
                            'slug' => $slugYear,
                            'term_group' => 0
                        ]);
        
                    Taxonomy::updateOrCreate([
                            'term_id' => $termYear->term_id,
                            'taxonomy' => 'vehicle',
                            'parent' => $termMarker->term_id,
                        ],[
                            'term_id' => $termYear->term_id,
                            'taxonomy' => 'vehicle',
                            'description' => '',
                            'parent' => $termMarker->term_id,
                            'count' => 0,
                        ]);
                }
            }
        }

        // $test = [
        //     'kia' => [
        //         'sss' => [2000, 2001, 2002]
        //     ]
        // ];
    
    }
}
