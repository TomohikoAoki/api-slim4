<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsContent extends Model
{
    protected $fillable = ['name', 'title', 'public', 'content', 'shop_ids', 'thumb_filename'];
}