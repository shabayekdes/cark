<?php

use App\Models\Term;
use App\Models\Taxonomy;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class ModelYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $termBrand = Term::where('slug', 'LIKE', 'hyonda')->first();

        $models = ['النترا', 'نيو النترا'];
        $years = [2000, 2001, 2002, 2003, 2004, 2005, 2006, 2007, 2008, 2009, 2010, 2011, 2012, 2013, 2014, 2015, 2016, 2017, 2018, 2019, 2020, 2021];

        foreach ($models as $model){

            $slugModel = $termBrand->slug . '-' . Str::slug($model, '-');

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
                'parent' => $termModel->term_id,
            ],[
                'term_id' => $termModel->term_id,
                'taxonomy' => 'vehicle',
                'description' => '',
                'parent' => $termModel->term_id,
                'count' => 0,
            ]);

            foreach ($years as $year) {
                $slugYear = $slugModel . '-' . Str::slug($year, '-');

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
                        'parent' => $termModel->term_id,
                    ],[
                        'term_id' => $termYear->term_id,
                        'taxonomy' => 'vehicle',
                        'description' => '',
                        'parent' => $termModel->term_id,
                        'count' => 0,
                    ]);

            }

        }

    }
}
