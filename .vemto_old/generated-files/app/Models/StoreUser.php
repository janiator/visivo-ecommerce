<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StoreUser extends Model
{
    use HasFactory;

    protected $table = 'store_user';

    protected $guarded = [];

    protected $fillable = ['role'];
}
