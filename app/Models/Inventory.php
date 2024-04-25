<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'item_list';


    /**
     * Specify the primary key type as string
     *
     * @var string
     */

    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'item_list',
        'description',
        'qty',
        'unit',
        'category',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'inventory';
}
