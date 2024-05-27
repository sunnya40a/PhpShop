<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseHistory extends Model
{
    use HasFactory;
    /**
     * The primary key associated with the table.
     *
     * @var unsignedInteger
     */
    protected $primaryKey = 'PO';


    /**
     * Specify the primary key type as string
     *
     * @var unsignedInteger
     */

    protected $keyType = 'unsignedInteger';


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
        'PO',
        'Pdate',
        'item_list',
        'material_desc',
        'qty',
        'unit',
        'u_price',
        'p_price',
        'user',
        'category',
        'supplier_id',
        'Rdate',
        'paid_status',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'purchaseHistory';
}
