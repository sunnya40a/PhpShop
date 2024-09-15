<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        's_name',
        'mobile1',
        'mobile2',
        'c_person',
        'contact_info'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'suppliers';

    // Define the inverse relationship with PurchaseHistory
    public function purchaseHistories()
    {
        return $this->hasMany(PurchaseHistory::class, 'supplier_id');
    }

    public function Inventory()
    {
        return $this->hasMany(Inventory::class, 'supplier_id');
    }
}
