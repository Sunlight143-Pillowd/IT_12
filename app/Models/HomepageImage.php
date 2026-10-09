<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomepageImage extends Model
{
    protected $fillable = [
        'key',
        'image_path',
    ];
}
