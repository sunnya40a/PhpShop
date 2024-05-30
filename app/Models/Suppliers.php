<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class suppliers extends Model
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
        'contact_info',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'suppliers';
}
