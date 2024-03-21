<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseHistory extends Model
{

        /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'purchase_history';
    use HasFactory;
}
