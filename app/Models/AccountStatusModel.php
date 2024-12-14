<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountStatusModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'account_status';
    protected $primaryKey = 'account_status_id';
    protected $fillable = [
        'name',
        'price',
        'range',
    ];

}
