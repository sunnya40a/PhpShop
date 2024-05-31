<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentStatus extends Model
{
    use HasFactory;
    protected $table = 'paymentstatuses';
    protected $fillable = ['code', 'status', 'onpurchase', 'onsale'];

    // Define the inverse relationship with PurchaseHistory
    public function purchaseHistories()
    {
        return $this->hasMany(PurchaseHistory::class, 'paid_status');
    }
}
