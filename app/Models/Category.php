<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
<<<<<<< HEAD
        'image_path',
=======
>>>>>>> 8ea77616480ea087a512cd1892f2c9623776d9ce
    ];
}
