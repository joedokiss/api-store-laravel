<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    public $timestamps = false;

    protected $fillable = ['parent_id', 'store_name'];
}