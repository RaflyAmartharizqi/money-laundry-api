<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AddOnItemModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'add_on_item';
    protected $primaryKey = 'add_on_item_id';
    protected $fillable = [
        'transaction_order_id',
        'item_name',
        'quantity',
        'price_per_item',
        'subtotal',
    ];
}
