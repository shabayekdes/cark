<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Term extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'terms';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'term_id';

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
    public function taxonomy()
    {
        return $this->hasOne(Term::class, 'term_id', 'term_taxonomy_id');
    }
}
