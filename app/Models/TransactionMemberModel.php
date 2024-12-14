<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionMemberModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'transaction_member';
    protected $primaryKey = 'transaction_member_id';
    protected $fillable = [
        'users_id',
        'admin_id',
        'account_status_id',
        'subscription_range',
        'total_price',
    ];
}
