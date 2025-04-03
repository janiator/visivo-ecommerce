<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CollectionProduct extends Model
{
    use HasFactory;

    protected $table = 'collection_product';

    protected $guarded = [];
}
