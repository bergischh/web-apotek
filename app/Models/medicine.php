<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class medicine extends Model
{
    use HasFactory;

    protected $fillable = [
        'type', 'name', 'price', 'stock' //ini harus sama yang ada di file create medicine 
    ];
}
