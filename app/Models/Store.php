<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Store
 * @package App\Models
 * @version February 27, 2017, 8:28 am UTC
 */
class Store extends Model
{
    use SoftDeletes;

    public $table = 'stores';
    

    protected $dates = ['deleted_at'];


    public $fillable = [
        'book_id',
        'amount',
        'total_amount'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'book_id' => 'integer',
        'amount' => 'integer',
        'total_amount' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'book_id' => 'required',
        'amount' => 'required',
        'total_amount' => 'required'
    ];
    
    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id');
    }
    
}
