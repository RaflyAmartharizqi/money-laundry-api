<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionOrderModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'transaction_order';
    protected $primaryKey = 'transaction_order_id';
    protected $fillable = [
        'users_id',
        'customer_id',
        'package_laundry_id',
        'order_date',
        'pick_up_date',
        'status',
        'payment_status',
        'payment_date',
        'weight',
        'subtotal',
        'subtotal_add_on_item',
        'total_price',
        'quantity',
    ];

    public function add_on_item()
    {
        return $this->hasMany(AddOnItemModel::class, 'transaction_order_id', 'transaction_order_id');
    }

    public function customer()
    {
        return $this->hasOne(CustomerModel::class, 'customer_id', 'customer_id');
    }

    public function package_laundry()
    {
        return $this->hasOne(PackageLaundryModel::class, 'package_laundry_id', 'package_laundry_id');
    }

}
