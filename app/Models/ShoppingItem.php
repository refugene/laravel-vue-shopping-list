<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShoppingItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'is_picked_up',
        'sort_order'
    ];
}
