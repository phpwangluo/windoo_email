<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SitesBlogPhotos extends Model
{
    use HasFactory;
    protected $connection = 'mysql_blog';
    protected $table = 'photos';
}
