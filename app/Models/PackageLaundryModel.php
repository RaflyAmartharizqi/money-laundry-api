<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PackageLaundryModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'package_laundry';
    protected $primaryKey = 'package_laundry_id';
    protected $fillable = [
        'users_id',
        'name',
        'logo',
        'price_per_kg',
        'description',
    ];
}
