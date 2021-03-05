<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function index()
    {
        return view('upload-file');
    }

    public function store()
    {
        if (request()->has('mycsv')) {
            $data   =  array_map('str_getcsv', file(request()->mycsv));
            // dd($data);
            $header = $data[0];
            unset($data[0]);

            // dd(array_chunk($data,2));
            foreach (array_chunk($data,2) as $value) {
                $trid = 1;
                foreach ($value as $key) {
                    $saleData = array_combine($header, $key);
                    dd($saleData);
                }

            }

            return 'Done';
        }

        return 'please upload file';
    }

}
