<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
   /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'icl_translations';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'translation_id';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public $timestamps = false;
}
