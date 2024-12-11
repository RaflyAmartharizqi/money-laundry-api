<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'customer';
    protected $primaryKey = 'customer_id';
    protected $fillable = [
        'users_id',
        'name',
        'phone_number',
    ];
}
