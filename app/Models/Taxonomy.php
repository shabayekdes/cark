<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Taxonomy extends Model
{
   /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'term_taxonomy';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'term_taxonomy_id';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public $timestamps = false;

        /**
     * Get the comments for the blog post.
     */
    public function term()
    {
        return $this->belongsTo(Term::class, 'term_id');
    }
}
