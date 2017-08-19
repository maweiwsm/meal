<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    // 指定允许批量赋值的字段
    protected $fillable = ['title', 'description'];
}
